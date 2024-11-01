<?php
/*
   Plugin Name: UniPAY Payment Gateway For WooCommerce
   Description: Extends WooCommerce 5.6.0 to Process Payments with UniPAY gateway.
   Version: 2.2
   Plugin URI: http://woocommerce.ge/
   Author: UniPAY
   Author URI: https://www.unipay.com/
   License: Under GPL3
   Text Domain: unipay
   Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'plugins_loaded', 'init_woocommerce_unipay_class' );

function init_woocommerce_unipay_class() {

	if (!class_exists('WC_Payment_Gateway')) {
		return;
	}

	class WC_Gateway_Wocommerce_Unipay extends WC_Payment_Gateway {

	    /**
         * Whether or not logging is enabled
         *
         * @var bool
         */
        public static $log_enabled = false;

        /**
         * Logger instance
         *
         * @var WC_Logger
         */
        public static $log = false;

		/**
		 * Set if the place order button should be renamed on selection.
		 * @var string
		 */
		public $order_button_text;

		/**
		 * yes or no based on whether the method is enabled.
		 * @var string
		 */
		public $enabled;

		/**
		 * Payment method title for the frontend.
		 * @var string
		 */
		public $title;

		/**
		 * Payment method description for the frontend.
		 * @var string
		 */
		public $description;

		/**
		 * Gateway title.
		 * @var string
		 */
		public $method_title = '';

		/**
		 * Gateway description.
		 * @var string
		 */
		public $method_description = '';	

		/**
		 * Icon for the gateway.
		 * @var string
		 */
		public $icon;
	
		/**
		 * Unique Gateway ID.
		 * @var string
		 */
		public $id;
		
		/**
		 * UniPAY create order live url.
		 * @var string
		 */
		public $liveurl;
		
		/**
		 * UniPAY create order live url.
		 * @var string
		 */
		public $liveurlv2;

		/**
		 * API Merchant ID.
		 * @var string
		 */
		private $merchantid;

		/**
		 * Merchant secret key.
		 * @var string
		 */
		private $secretkey;

		/**
		 * Message to be displayed on successful transaction.
		 * @var string
		 */
		private $success_message;

		/**
		 * Message to be displayed on failed transaction.
		 * @var string
		 */
		private $failed_message;

		/**
		 * Call back url.
		 * @var string
		 */
		private $call_back_URL;

		/**
		 * Logo which the user see during create order.
		 * @var string
		*/
		private $logo;

		/**
		 * Slogan which the user see during create order.
		 * @var string
		*/
		private $slogan;

		/**
		 * User choose order statuses.
		 * @var array
		 */
		private $selectedStatuses = array();

		/**
		 * Enable/Disable order-description in create order page.
		 * @var string yes/no
		*/
		private $orderDescriptionStatus;

		/**
         * Constructor for the gateway.
         */
		public function __construct(){

			 // Setup general properties
			 $this->setup_properties();

			// Define fields.
			$this->init_form_fields();

			// Load the settings.
			$this->init_settings();

			// Get settings.
			$this->settings();

			if ( version_compare( WOOCOMMERCE_VERSION, '2.0.0', '>=' ) ) {
				add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( &$this, 'process_admin_options' ) );
			} else {
				add_action( 'woocommerce_update_options_payment_gateways', array( &$this, 'process_admin_options' ) );
			}

			add_action('woocommerce_cancel_unipay',array(&$this, 'cancel_page'));
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

            require_once plugin_dir_path( __FILE__ ) . '/includes/class-ipn-handler.php';
            new WC_Gateway_Unipay_IPN_Handler( $this->selectedStatuses, $this->secretkey);

            require_once plugin_dir_path( __FILE__ ) . '/includes/class-back-link-handler.php';
            new WC_Gateway_Unipay_Back_Link_Handler( array('success' => $this->success_message, 'failed' => $this->failed_message));

			global $woocommerce;
	   	}

        /**
         * Enqueues admin scripts.
         *
         * @since 1.5.2
         */
        public function enqueue_scripts() {
            // Image upload.
            wp_enqueue_media();

            wp_enqueue_script( 'wc-unipay-settings', plugins_url( 'assets/js/wc-unipay-settings.js', __FILE__ ), array( 'jquery' ), '0.1', true );
        }

		/**
		 * Setup general properties for the gateway.
		 */
    	protected function setup_properties() {
			$this->id                 = 'unipay';
			$this->method_title       = __('UniPAY', 'unipay');
			$this->method_description = __('UniPAY Checkout is a payment gateway plugin that allows you to take credit card payments via UniPAY.com','unipay');
			$this->liveurl            = 'https://api.unipay.com/checkout/createorder';
			$this->liveurlv2          = 'https://apiv2.unipay.com/woo/checkout/v1/createorder';
		}

		/**
		 * Generate Title HTML.
		 *
		 * @param  mixed $key
		 * @param  mixed $data
		 * @since  1.0.0
		 * @return string
		 */
		public function generate_title_html( $key, $data ) {
			$field_key = $this->get_field_key( $key );
			$defaults  = array(
				'title' => '',
				'class' => '',
				'description' => '',
			);

			$data = wp_parse_args( $data, $defaults );

			ob_start();
			if($key !== 'call_back_URL') {
			?>
				</table>
				<h3 class="wc-settings-sub-title <?php echo esc_attr( $data['class'] ); ?>" id="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></h3>
				<?php if ( ! empty( $data['description'] ) ) : ?>
					<p><?php echo wp_kses_post( $data['description'] ); ?></p>
				<?php endif; ?>
				<table class="form-table">
				<hr>
			<?php
            }
            else { ?>
                <tr valign="top">
            <th scope="row" class="titledesc">
                <label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
                <?php echo wp_kses_post($this->get_tooltip_html( $data )); ?>
            </th>
            <td class="forminp">
                <fieldset>
                    <legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
                    <?php echo wp_kses_post($this->get_description_html( $data )); ?>
                </fieldset>
            </td>
        </tr> <?php
            }
			return ob_get_clean();
		}

		/**
		 * Get gateway icon.
		 *
		 * @return string
		 */
		public function get_icon() {
			$icon_html = '';
			$icon      = (array) $this->get_icon_image();
			
			foreach ( $icon as $i ) {
				$icon_html .= '<img src="' . esc_attr( $i ) . '" alt="' . esc_attr__( 'UniPAY', 'unipay' ) . '" />';
			}

			return apply_filters( 'woocommerce_gateway_icon', $icon_html, $this->id );
		}

		/**
		 * Get UniPAY images for a size.
		 *
		 * @return array of image URLs
		 */
		protected function get_icon_image(  ) {

			if( $this->settings['image-status'] === 'no' ) return array();
			
			$imageTypes = array_reverse($this->settings['image-type']);					

			if (count($imageTypes) == 0) return array();

			foreach($imageTypes as $imageType) {
				$icon[] = esc_url(WP_PLUGIN_URL . "/" . plugin_basename(dirname(__FILE__)) . '/assets/images/icons/'.$imageType."_logo_".$this->settings['image-color'].'_' . $this->settings['image-size'] .'.png');
			}
			
			
			return apply_filters( 'woocommerce_unipay_icon', $icon );
		}

		/**
		 * Generate Image HTML.
		 *
		 * @param  mixed $key
		 * @param  mixed $data
		 * @since  1.5.0
		 * @return string
		 */
		public function generate_image_html( $key, $data ) {
			$field_key = $this->get_field_key( $key );
			$defaults  = array(
				'title'             => '',
				'disabled'          => false,
				'class'             => '',
				'css'               => '',
				'placeholder'       => '',
				'type'              => 'text',
				'desc_tip'          => false,
				'description'       => '',
				'custom_attributes' => array(),
			);

			$data  = wp_parse_args( $data, $defaults );
			$value = $this->get_option( $key );

			// Hide show add remove buttons.
			$maybe_hide_add_style    = '';
			$maybe_hide_remove_style = '';

			// For backwards compatibility (customers that already have set a url)
			$value_is_url            = filter_var( $value, FILTER_VALIDATE_URL ) !== false;

			if ( empty( $value ) || $value_is_url ) {
				$maybe_hide_remove_style = 'display: none;';
			} else {
				$maybe_hide_add_style = 'display: none;';
			}

			ob_start();
			?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $field_key ); ?>"><?php echo wp_kses_post( $data['title'] ); ?> <?php echo $this->get_tooltip_html( $data ); ?></label>
				</th>

				<td class="image-component-wrapper">
					<div class="image-preview-wrapper">
						<?php
						if ( ! $value_is_url ) {
							echo wp_get_attachment_image( $value, 'thumbnail' );
						} else {
							echo sprintf( __( 'Already using URL as image: %s', 'unipay' ), $value );
						}
						?>
					</div>

					<button
						class="button image_upload"
						data-field-id="<?php echo esc_attr( $field_key ); ?>"
						data-media-frame-title="<?php echo esc_attr( __( 'Select a image to upload', 'unipay' ) ); ?>"
						data-media-frame-button="<?php echo esc_attr( __( 'Use this image', 'unipay' ) ); ?>"
						data-add-image-text="<?php echo esc_attr( __( 'Add image', 'unipay' ) ); ?>"
						style="<?php echo esc_attr( $maybe_hide_add_style ); ?>"
					>
						<?php echo esc_html__( 'Add image', 'unipay' ); ?>
					</button>

					<button
						class="button image_remove"
						data-field-id="<?php echo esc_attr( $field_key ); ?>"
						style="<?php echo esc_attr( $maybe_hide_remove_style ); ?>"
					>
						<?php echo esc_html__( 'Remove image', 'unipay' ); ?>
					</button>

					<input type="hidden"
						name="<?php echo esc_attr( $field_key ); ?>"
						id="<?php echo esc_attr( $field_key ); ?>"
						value="<?php echo esc_attr( $value ); ?>"
					/>
					<?php if ( ! empty( $data['description'] ) ) : ?>
						<p><?php echo $this->get_description_html( $data ); ?></p>
					<?php endif; ?>
				</td>
			</tr>
			<?php

			return ob_get_clean();
		}


		/**
     	* Output the admin options table.
     	*/
		public function admin_options(){
			?>
			<div class="simplify-commerce-banner updated">
				<img  src="<?php echo esc_attr(WP_PLUGIN_URL . "/" . plugin_basename(dirname(__FILE__)) . '/assets/images/logo-admin.png'); ?>" />
				<p class="main">
					<h3>
						<?php esc_html_e( 'UniPAY', 'unipay' ); ?>
					</h3>
					<strong><?php esc_html_e( 'Getting started', 'unipay' ); ?></strong>
				</p>
				<p>
					<?php esc_html_e( 'Accept now, Better Online Payments for Your Business. We take the complexity out of the payment ecosystem, providing smart and seamless solutions that deliver value to your business. Accept payments anywhere, anytime on a single platform.', 'unipay' ); ?>
				</p>
				<p>
					<a href="<?php esc_html_e("https://business.unipay.com") ?>" target="_blank" class="button button-primary"><?php esc_html_e( 'Sign Up as Business', 'unipay' ); ?></a>
				</p>
				<hr>
				<strong><?php esc_html_e("UniPAY Support", 'unipay'); ?></strong>
				<p><?php esc_html_e("Call Us: (+995 32) 2 193 194"); ?><br>
				<p><?php esc_html_e("Email: info@unipay.com"); ?><br>
				<p><strong><?php esc_html_e("Version: 2.2"); ?></strong><p>

			</div>

			<table class="form-table">
				<?php esc_html($this->generate_settings_html()); ?>
			</table>
			<?php
		}

		/**
		 * Define user set variables.
		 */
		protected function settings(){

		    // Get current locale
			get_locale() == 'ka_GE' ? $locale = 'ka_GE' : $locale = 'en_US';

			'yes' === $this->settings['debug'] ? $this->debug = true : $this->debug = false;

			self::$log_enabled              = $this->debug;
			$this->title                    = __($this->settings['title'], 'unipay');
			$this->description              = __($this->settings['description'], 'unipay');
			$this->order_button_text  		= __($this->settings['button-title'], 'unipay');
			$this->merchantid  	            = $this->settings['merchantid'];
			$this->secretkey  		        = $this->settings['secretkey'];
			$this->success_message          = $this->settings['success_message_'.$locale];
			$this->failed_message           = $this->settings['failed_message_'.$locale];
			$this->call_back_URL            = $this->settings['call_back_URL'];
			$this->logo                     = wp_get_attachment_image_src($this->settings['logo_image_url'],'medium')[0];
			$this->slogan                   = $this->settings['slogan'];
            $this->orderDescriptionStatus   = $this->settings['order-description'];
            $this->orderQuantityStatus      = $this->settings['order-quantity'];
			$this->selectedStatuses   		= array(
				'COMPLETED'    => $this->settings['completed'],
				'PENDING'      => $this->settings['processing'],
				'FAILED'       => 'failed',
				'CANCELED'     => 'cancelled'
			);

		}

		/**
         * Logging method.
         *
         * @param string $message Log message.
         * @param string $level Optional. Default 'info'. Possible values:
         *                      emergency|alert|critical|error|warning|notice|info|debug.
         */
        public static function log( $message, $level = 'info' ) {
            if ( self::$log_enabled ) {
                if ( empty( self::$log ) ) {
                    self::$log = wc_get_logger();
                }
                self::$log->log( $level, $message, array( 'source' => 'unipay' ) );
            }
        }

 		/**
     	* Initialise settings form fields.
     	*
     	* Add an array of fields to be displayed
     	* on the gateway's settings screen.
     	*
     	* @since  1.0.0
     	*/
	   	public function init_form_fields(){
			$this->form_fields = include( 'includes/settings.php' );
      	}

		/**
		 * Process the payment and return the result.
		 *
		 * return the success and redirect in an array. e.g:
		 *
		 *        return array(
		 *            'result'   => 'success',
		 *            'redirect' => $this->get_return_url( $order )
		 *        );
		 *
		 * @param int $order_id
		 * @return array
		 */
	  	public function process_payment( $order_id ) {

			/**
			 * Create new order object.
			 * @var string $order_id
			 */
			$order = new WC_Order( $order_id );

			if(strlen($this->merchantid) >= 13){
				return $this->runCreateOrderv2($order);
			}else{
				return $this->runCreateOrder($order);
			}

		}

		protected function runCreateOrder($order)
		{
			global $woocommerce;

			$responseItem = $this->item( $order, $woocommerce->cart->get_cart() );
			$responseCurl = $this->curl(array(
				'environmentUrl' => esc_url($this->liveurl), 
				'merchantId' => $this->merchantid, 
				'password' => $responseItem['password'], 
				'opts' => $responseItem['opts']
			));			

			if(empty($responseCurl)){
				wc_add_notice( sprintf( __( 'Incorect some parameters', "unipay" ) ) ,'error' );
			}

			/**
			 * Done payment and return array if @var $responseCurl->Errorcode not zero and isset @var $responseCurl->data->Checkout
			 */
			if ( $responseCurl->Errorcode === 0 && !empty($responseCurl->Data->Checkout) ){

				add_post_meta($order->get_id(), 'UnipayOrderID', $responseCurl->Data->UnipayOrderHashID);
				$order->add_order_note( __( $responseCurl->Data->UnipayOrderHashID, 'unipay' ) );
				$order->reduce_order_stock();
				$this->log( 'Success order '.$order->get_id().' and redirect UniPAY checkout page', 'success' );
				//print_r($responseCurl);
				//die();
				return array(
					'result'   => 'success',
					//'redirect' => esc_url($responseCurl->data->Checkout),
					'redirect' => $responseCurl->Data->Checkout,
				);
			}else{
				$order->add_order_note( esc_html(__( 'Unipay payment failed. Payment declined. Please Check your Admin settings', 'unipay' )) );
				$this->log( 'Payment Error: ' . $responseCurl->Message, 'error' );
				wc_add_notice( sprintf( __( $responseCurl->Message, "unipay" ) ) ,'error' );
			}
		}

		protected function runCreateOrderv2($order)
		{
			global $woocommerce;

			$responseItem = $this->itemv2( $order, $woocommerce->cart->get_cart() );

			$responseCurl = $this->curl(array(
				'environmentUrl' => esc_url($this->liveurlv2), 
				'merchantId' => $this->merchantid, 
				'password' => $responseItem['password'], 
				'opts' => $responseItem['opts']
			));

			if(empty($responseCurl)){
				wc_add_notice( sprintf( __( 'Incorect some parameters', "unipay" ) ) ,'error' );
			}

			/**
			 * Done payment and return array if @var $responseCurl->Errorcode not zero and isset @var $responseCurl->data->Checkout
			 */
			if ( $responseCurl->errorcode === 0 && !empty($responseCurl->data->Checkout) ){

				add_post_meta($order->get_id(), 'UnipayOrderID', $responseCurl->data->UnipayOrderHashID);
				$order->add_order_note( __( $responseCurl->data->UnipayOrderHashID, 'unipay' ) );
				$order->reduce_order_stock();
				$this->log( 'Success order '.$order->get_id().' and redirect UniPAY checkout page', 'success' );
				//print_r($responseCurl);
				//die();
				return array(
					'result'   => 'success',
					//'redirect' => esc_url($responseCurl->data->Checkout),
					'redirect' => $responseCurl->data->Checkout,
				);
			}else{
				$order->add_order_note( esc_html(__( 'Unipay payment failed. Payment declined. Please Check your Admin settings', 'unipay' )) );
				$this->log( 'Payment Error: ' . $responseCurl->message, 'error' );

				wc_add_notice( sprintf( __( $responseCurl->message, "unipay" ) ) ,'error' );
			}
		}

		/**
		 * Parse each items.
		 * @var object $order
		 * @var object $items
		 * @return array hash and opts
		 */
		protected function item( $order, $items )
		{
			/**
			 * count total items.
			 * @var $totalItems
			 */
			$totalItems = count($items);

			/**
			 * Get cart page url for backLink.
			 * @var string $cartPageUrl
			 */
			$cartPageUrl = get_permalink( wc_get_page_id( 'cart' ) );

            is_user_logged_in() ? $merchantUser = get_current_user_id() : $merchantUser = 'GUEST';
			get_locale() == "ka_GE" ? $language = "GE" : $language = "EN";

			$backLink = base64_encode(add_query_arg(array('wc-api'=>'WC_Gateway_Unipay_Order','ordercode'=>$order->id),home_url('/')) . "|" . $cartPageUrl);

			/**
			 * Get each item and assemble array for crypt.
			 * If $totalItems > 1 @var array $itemsArray
			 */
			foreach ( $items as $item ){

                $this->orderDescriptionStatus == 'yes' ?  $productDescription = preg_replace( '/[^\p{L}0-9^_\s]+/u', ' ',substr( get_post($item['product_id'])->post_content, 0,250 )) : $productDescription = '';
                $productTitle =  preg_replace('/[^\p{L}0-9^_\s]+/u',' ',$item['data']->post->post_title);

				if($totalItems > 1){
                    $this->orderQuantityStatus == 'yes' ? $quantity = $item['quantity'] : $quantity = '';
					$price = $item['line_subtotal'];
					$itemsArray[] = $price.'|'.$quantity.'|'.$productTitle.'|'.$productDescription;
				}
			}
			
			$total_amount = $order->order_total * 100;

			$arr = array(
				"MerchantID"       => $this->merchantid,
				"MerchantUser"     => $merchantUser,
				"MerchantOrderID"  => uniqid('UN') . '-' . $order->id,
				"OrderPrice"       => $total_amount,
				"OrderCurrency"	   => 'GEL',
				"BackLink"         => $backLink,
				"Mlogo"            => base64_encode($this->logo),
				"Mslogan"		   => preg_replace( '/[^\p{L}0-9^_\s]+/u', '',substr($this->slogan,0,70)),
				"Language"		   => $language
			);

			$orderItems = array();

			if($totalItems == 1){
				$arr['OrderName'] = $productTitle;
				$arr['OrderDescription'] = trim($productDescription);
			} else {
				$orderItems['Items'] = $itemsArray;
		}														

			$hash = array('Hash'=> md5($this->secretkey.'|'.implode('|',$arr)));

			$hash = !is_null($orderItems) ? array_merge($hash, $orderItems) : $hash;				
			$result = array_merge($hash, $arr);


			// Return thank you redirect
			return array(
				'password' => $hash['Hash'],
				'opts'     => json_encode($result)
			);
		}

		

		/**
		 * Parse each items.
		 * @var object $order
		 * @var object $items
		 * @return array hash and opts
		 */
		protected function itemv2( $order, $items )
		{
			/**
			 * count total items.
			 * @var $totalItems
			 */
			$totalItems = count($items);

			/**
			 * Get cart page url for backLink.
			 * @var string $cartPageUrl
			 */
			$cartPageUrl = get_permalink( wc_get_page_id( 'cart' ) );

			if($cartPageUrl == ''){
                $cartPageUrl = home_url('/');
            }

            is_user_logged_in() ? $merchantUser = get_current_user_id() : $merchantUser = 'GUEST';
			get_locale() == "ka_GE" ? $language = "GE" : $language = "EN";

			// $backLink = base64_encode(add_query_arg(array('wc-api'=>'WC_Gateway_Unipay_Order','ordercode'=>$order->id),home_url('/')) . "|" . $cartPageUrl);
			$successRedirectUrl = base64_encode(add_query_arg(array('wc-api'=>'WC_Gateway_Unipay_Order','ordercode'=>$order->id),home_url('/')));
			$cancelRedirectUrl = base64_encode($cartPageUrl);



			/**
			 * Get each item and assemble array for crypt.
			 * If $totalItems > 1 @var array $itemsArray
			 */
			foreach ( $items as $item ){

                $this->orderDescriptionStatus == 'yes' ?  $productDescription = preg_replace( '/[^\p{L}0-9^_\s]+/u', ' ',substr( get_post($item['product_id'])->post_content, 0,250 )) : $productDescription = '';
                $productTitle =  preg_replace('/[^\p{L}0-9^_\s]+/u',' ',$item['data']->post->post_title);

				if($totalItems > 1){
                    $this->orderQuantityStatus == 'yes' ? $quantity = $item['quantity'] : $quantity = '';
					$price = $item['line_subtotal'];
					// $itemsArray[] = $price.'|'.$quantity.'|'.$productTitle.'|'.$productDescription;
					$itemsArray[] = (object) [
                        'price' => $price,
                        'quantity' => $quantity,
                        'title' => $productTitle,
                        'description' => $productDescription,
                    ];
				}
			}
			
			$total_amount = $order->order_total;

			$arr = array(
				"MerchantID"       => $this->merchantid,
				"MerchantUser"     => $merchantUser,
				"MerchantOrderID"  => $order->id,
				"OrderPrice"       => $total_amount,
				"OrderCurrency"	   => $order->get_currency(),
				// "BackLink"         => $backLink,
				"SuccessRedirectUrl"         => $successRedirectUrl,
				"CancelRedirectUrl"         => $cancelRedirectUrl,
				"Mlogo"            => base64_encode($this->logo),
				"Mslogan"		   => preg_replace( '/[^\p{L}0-9^_\s]+/u', '',substr($this->slogan,0,70)),
				"Language"		   => $language
			);

			$orderItems = array();

			if($totalItems == 1){
				$arr['OrderName'] = base64_encode($productTitle);
				$arr['OrderDescription'] = base64_encode(trim($productDescription));
			} else {
				$orderItems['Items'] = $itemsArray;
		}														

			$hash = array('Hash'=> hash('sha256', $this->secretkey.'|'.implode('|',$arr)));

			$hash = !is_null($orderItems) ? array_merge($hash, $orderItems) : $hash;				
			$result = array_merge($hash, $arr);


			// Return thank you redirect
			return array(
				'password' => $hash['Hash'],
				'opts'     => json_encode($result)
			);
		}

		/**
		 * Curl
		 * @param array $params
		 * @return curl $response
		 */
		protected function curl(array $params){
						
			$curl = curl_init($params['environmentUrl']);
			curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($curl, CURLOPT_USERPWD, "$params[merchantId]:$params[password]"); //Your credentials goes here
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $params['opts']); 
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); //IMP if the url has https and you don't want to verify source certificate
			curl_setopt($curl, CURLOPT_HTTPHEADER, array(                                                                        
					'Content-Type: application/json',                                                                                
					'Content-Length: ' . strlen($params['opts']))                                                                       
				);  
			curl_setopt($curl, CURLOPT_HEADER, false);
	
			$curlResponse = curl_exec($curl);	
			
			$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

			if ( $status != 200 ) {
			    $this->log( "Error: call to URL failed with status $status, response $curlResponse, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl), 'error' );
				// die("Error: call to URL failed with status $status, response $curlResponse, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
			}
			
			$response = $curlResponse;			
			
			curl_close($curl);		
			
			return json_decode($response);
		}

		/**
		 * Cancel page
		 * @param $oder_id
		 */
		public function cancel_page($order_id){
		    $this->log( 'Cancell order '.$order_id.' and redirect cart page', 'info' );
			echo sprintf(__($this->failed_message, 'unipay'), 'error');
		}
		
	}
}


/**
  * Add this Gateway to WooCommerce
**/
function woocommerce_add_unipay_gateway($methods) {
	
      $methods[] = 'WC_Gateway_Wocommerce_Unipay';
      return $methods;
}
add_filter('woocommerce_payment_gateways', 'woocommerce_add_unipay_gateway' );

add_filter('manage_edit-shop_order_columns', 'ST4_columns_head', 10);
add_action('manage_shop_order_posts_custom_column', 'ST4_columns_shop_order_content', 10, 2);
//ADD TWO NEW COLUMNS

function ST4_columns_head($defaults) {
    $defaults['UnipayOrderID']  = 'UnipayOrderID';
    return $defaults;
}
function ST4_columns_shop_order_content($column_name, $post_ID) {
    if ($column_name == 'UnipayOrderID') {
        echo get_post_meta($post_ID,'UnipayOrderID',true);
    }
}

// function filter_gateways($gateways){
// 	global $woocommerce;        
	
// 	$currenctCurrency = get_woocommerce_currency();

// 	if ($currenctCurrency != "GEL") {		
// 		unset($gateways['unipay']);
// 	}
	
// 	return $gateways;
// }

// add_filter('woocommerce_available_payment_gateways','filter_gateways');

add_filter( 'woocommerce_currencies', 'add_my_currency' );
function add_my_currency( $currencies ) {
     $currencies['GEL'] = __( 'Georgia', 'woocommerce' );
     return $currencies;
}
