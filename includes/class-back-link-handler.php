<?php
/**
 * Handles backlink.
 *
 * @package WooCommerce/UniPAY
 * @version 1.0.5
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WC_Gateway_Unipay_Back_Link_Handler extends WC_Payment_Gateway {

    /**
     * Success and failed messages.
     * @var array $messages.
     */
    private $messages;

    /**
     * Constructor.
     *
     * @param array @messages
     */
    public function __construct( array $messages ) {
        $this->messages = $messages;

        add_action( 'woocommerce_api_wc_gateway_unipay_order', array( $this, 'handle_order' ) );
    }

    public function handle_order(){

        if(!empty( sanitize_text_field($_GET['ordercode']) )){

            $this->successOrder(sanitize_text_field($_GET['ordercode']));
        }
    }

    /**
     * success order and redirect url.
     * @param $orderId
     */
    protected function successOrder($orderId){

        global $woocommerce;

        $order = new WC_Order( $orderId );

        WC_Gateway_Wocommerce_Unipay::log( 'Complete payment order: '.$order->get_id().'. and redirect order receive page', 'success' );
        wc_add_notice( sprintf( __( $this->messages['success']) , "unipay" )  ,'success' );
        $woocommerce->cart->empty_cart();
        $redirectUrl = esc_url_raw($this->get_return_url( $order ));
        wp_redirect( $redirectUrl ); exit;
    }

}