<?php
/**
 * Handles responses from UniPAY IPN.
 *
 * @package WooCommerce/UniPAY
 * @version 1.0.5
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WC_Gateway_Unipay_IPN_Handler extends WC_Payment_Gateway {

    /**
     * User choose order statuses.
     * @var array
     */
    private $selectedStatuses;

    /**
     * Merchant secret key.
     * @var string
     */
    private $secretkey;

    /**
     * Constructor.
     *
     * @param array $selectedStatuses User choose order statuses.
     * @param @secretkey
     */
    public function __construct( array $selectedStatuses, $secretkey ) {
        $this->selectedStatuses = $selectedStatuses;
        $this->secretkey        = $secretkey;

        add_action( 'woocommerce_api_wc_gateway_unipay', array( $this, 'handle_callback' ) );
    }

    /**
     * Check order status.
     */
    public function handle_callback(){

        global $woocommerce;

        if( empty(sanitize_text_field($_GET['Hash'])) ) return;

        /**
         * Calculated hash.
         * @var string
         */
        $Hash = !empty(sanitize_text_field($_GET['Hash'])) ? sanitize_text_field($_GET['Hash']) : '';

        /**
         * Unipay order id. it's unique.
         * @var string
         */
        $UnipayOrderID = !empty(sanitize_text_field($_GET['UnipayOrderID'])) ? sanitize_text_field($_GET['UnipayOrderID']) : '';

        /**
         * Changeable merchant odrer id.
         * @var string
         */
        $MerchantOrderID = !empty(sanitize_text_field($_GET['MerchantOrderID'])) ? sanitize_text_field($_GET['MerchantOrderID']): '';

        /**
         * Payment status. (COMPLETED, CANCELED, PENDING, FAILED).
         * @var string
         */
        $Status = !empty(sanitize_text_field($_GET['Status'])) ? sanitize_text_field($_GET['Status']) : '';

        /**
         * Transaction reason.
         * @var string
         */
        $Reason = !empty(sanitize_text_field($_GET['Reason'])) ?  sanitize_text_field($_GET['Reason']) : '';

        /**
         * Transaction version.
         * @var string
         */
        $Version = !empty(sanitize_text_field($_GET['Version'])) ?  sanitize_text_field($_GET['Version']) : '';

        /**
         * Calculate hash  with secret key and all string parameters which are passed to function.
         * @param String $unipayOrderId.
         * @param String $merchandOrderId.
         * @param String $status.
         * @param String $secretKey.
         */
        $calculateHash =  $UnipayOrderID.'|'. $MerchantOrderID.'|'.$Status.'|'.$this->secretkey;

        if($Version == 2){
            $correct_hash = hash('sha256', $calculateHash);

            switch ($Status) {
                case 3:
                    $StatusString = "COMPLETED";
                    break;
                case 13:
                case 22:
                case 23:
                    $StatusString = "FAILED";
                    break;
                case -1:
                    $StatusString = "CANCELED";
                    break;
                default:
                    $StatusString = "PENDING";
            }

            $order_id = $MerchantOrderID;

        }else{
            $correct_hash = md5($calculateHash);
            $StatusString = $Status;
            $orderID = explode("-", $MerchantOrderID);
            $order_id = $orderID[1];
        }

        /**
         * Compare passed $hash and calcualted new hash.
         * If equals @param $hash and @param $calculateHash update order status by which user selected status.
         * If not equals order update status cancelled.
         */
        if( $Hash == $correct_hash ){
            $order = new WC_Order( $order_id );
            $order->update_status($this->selectedStatuses[$StatusString],__( 'Awaiting REDSYS payment', 'woocommerce' ));
            WC_Gateway_Wocommerce_Unipay::log( 'Update order ('.$order->get_id().') status. set '.$this->selectedStatuses[$StatusString].' status. UniPAY status is '.$StatusString.' Reason: '.$Reason, 'info' );
            if($StatusString === 'COMPLETED'){
                WC_Gateway_Wocommerce_Unipay::log( 'Complete payment order: '.$order->get_id(), 'success' );
                $order->payment_complete();
                $woocommerce->cart->empty_cart();
            }
        }
        else{
            WC_Gateway_Wocommerce_Unipay::log( 'Calculate hash not equal hash '.$Hash.'!=='.md5($calculateHash).' Reason: '.$Reason,'error' );
        }

    }
}
