<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wc_enqueue_js( "
	jQuery( function( $ ) {
		var ppec_mark_fields	= '#woocommerce_unipay_image-color, #woocommerce_unipay_image-type, #woocommerce_unipay_image-size';				

		$( '#woocommerce_unipay_image-status' ).change(function(){
			if ( $( this ).is( ':checked' ) ) {
				$( ppec_mark_fields ).closest( 'tr' ).show();
			} else {
				$( ppec_mark_fields ).closest( 'tr' ).hide();
			}
		}).change();

	});
" );

$settings =  array(
	'enabled'			=> array(
			'title'        => __('Enable/Disable', 'unipay'),
			'type'         => 'checkbox',						
			'label'        => __('Enable UniPAY Payment Module.', 'unipay'),
			'default'      => 'yes',
	),
	'merchantid'    	=> array(
			'title'        => __('MerchantID', 'unipay'),
			'type'         => 'text',		
			'desc_tip'     => __('The unique wallet number assigned to the Merchant during registration','unipay'),
  	),
	'secretkey' 	    => array(
			'title'        => __('SecretKey', 'unipay'),
			'type'         => 'password',
			'desc_tip'     => __('Activation parameter which  provides payment platform integration','unipay'),
	),
    'debug'                 => array(
            'title'        => __( 'Log', 'woocommerce' ),
            'type'         => 'checkbox',
            'label'        => __( 'Enable log', 'woocommerce' ),
            'desc_tip'     => __('This controls to log messages of contextual information', 'unipay'),
            'description'  => sprintf( __( '%s', 'unipay' ), '<code>' . WC_Log_Handler_File::get_log_file_path( 'unipay' ) . '</code>' ),
    ),
	'checkout-page' 	=> array(
            'title'       => __( 'Checkout Page', 'unipay' ),
            'type'        => 'title',
            'description' => __('Advanced options of Checkout page','unipay'),
	),
    'title'		 		=> array(
			'title'        => __('Title:', 'unipay'),
			'type'         => 'text',
			'default'      => __('Credit Card (UniPAY)', 'unipay'),
			'desc_tip'     => __('This controls  the title which the user sees during checkout', 'unipay'),
  	),
    'description'		=> array(
			'title'        => __('Description:', 'unipay'),
			'type'         => 'textarea',
			'default'      => __('Pay with your credit card via UniPAY payment gateway', 'unipay'),
			'css'		   => 'max-width:400px;',
			'desc_tip'     => __('This controls  the description  which the user sees during checkout','unipay'),		
	),
	'button-title'		=> array(
			'title'        	=> __('Button', 'unipay'),
			'type'    		=> 'text',
			'default'      	=> __('Pay Now', 'unipay'),
			'desc_tip'     	=> __('This controls  the button  text   which the user sees during checkout', 'unipay'),
	  ),
	'image-status'  	=> array(
			'title'        => __('Image', 'unipay'),
			'type'         => 'checkbox',						
			'label'        => __('Enable payment logo', 'unipay'),
			'default'      => 'yes',
			'desc_tip' => __('Optionally Enable  image  is displayed as your payment logo  of  the checkout page','unipay'),
	),
	'image-color' 		=> array(
		'title'        => __('Logo color', 'unipay'),
		'type'         => 'select',						
		'description'  =>  __('This controls  logo color  which the user sees during checkout', 'unipay'),
		'default'      => 'color',		
		'options'      => array(
			'color'  		=> __('Color','unipay'), 	
			'black'			=> __('Black','unipay'), 
			'white' 		=> __('White','unipay'),					
		)
	),		
	'image-size' 		=> array(
		'title'        => __('Choose logo size', 'unipay'),
		'type'         => 'select',					
		'description'  =>  __('This controls  the payment logo  size   which the user sees during checkout.', 'unipay'),
		'default'      => 'large',
		'options'      => array(			
			'medium'		=> __('Medium','unipay'),
			'small'  		=> __('Small','unipay'), 							
			'large' 		=> __('Large','unipay'),					
		)
	),		
	'image-type' 	    => array(
		'title'        => __('Payment logo type', 'unipay'),
		'type'         => 'multiselect',
		'class'			=> 'wc-enhanced-select',			
		'default'      => 'unipay',			
		'description'  =>  __('This controls payment logo type which the user sees during checkout', 'unipay'),
		'options'      => array(
			'unipay'  			=> __('UniPAY','unipay'),
			'visa'				=> __('Visa','unipay'),
			'mastercard'  		=> __('Mastercard','unipay'),
			'amex'  			=> __('Amex','unipay'),
			// 'qrcode'  			=> __('QrPAY','unipay'),
		  )
	),	
	'process-payment' 	=> array(
		'title'       => __( 'Payment status', 'unipay' ),
		'type'        => 'title',
		'description' => __('UniPAY  process status','unipay'),
	),
  	'completed' 		=> array(
			'title'        => __('UniPAY Completed status', 'unipay'),
			'type'         => 'select',						
			'description'  =>  __('Set status, when UniPAY order is "Completed"', 'unipay'),
			'options'      => array(
				'completed'  	=> __('completed','unipay'), 
				'processing'	=> __('processing','unipay'),
				'cancelled'  	=> __('cancelled','unipay'), 
				'pending'    	=> __('pending','unipay'), 
				'on-hold'    	=> __('on-hold','unipay'), 
				'refuned'    	=> __('refuned','unipay'), 
				'failed'     	=> __('failed', 'unipay'),
      		)
  	),				
  	'processing' 		=> array(
			'title'        => __('UniPAY Processing Status', 'unipay'),
			'type'         => 'select',						
			'description'  =>  __('Set status, when UniPAY order is "Processing"', 'unipay'),
			'options'      => array(
				'processing'	=> __('processing','unipay'),
				'completed'  	=> __('completed','unipay'), 
				'cancelled'  	=> __('cancelled','unipay'), 
				'pending'    	=> __('pending','unipay'), 
				'on-hold'    	=> __('on-hold','unipay'), 
				'refuned'    	=> __('refuned','unipay'), 
				'failed'     	=> __('failed', 'unipay'),
      		)
  	),
    'success_message_en_US'=> array(
			'title'        => __('Success Message (English)', 'unipay'),
			'type'         => 'textarea',
			'desc_tip'  =>  __('Message to be displayed on successful transaction.', 'unipay'),
			'default'      => __('Transaction has been processed successfully.', 'unipay'),
			'css'		   => 'max-width:400px;',
  	),            	
	'failed_message_en_US' => array(
			'title'        => __('Failed Message (English)', 'unipay'),
			'type'         => 'textarea',
			'desc_tip'  =>  __('Message to be displayed on failed transaction.', 'unipay'),
			'default'      => __('Transaction has been declined.', 'unipay'),
			'css'		   => 'max-width:400px;',
	),
  	'success_message_ka_GE'=> array(
			'title'        => __('Success Message (Georgian)', 'unipay'),
			'type'         => 'textarea',
			'desc_tip'  =>  __('Message to be displayed on successful transaction.', 'unipay'),
			'default'      => __('ტრანზაქცია წარმატებით განხორციელდა.', 'unipay'),
			'css'		   => 'max-width:400px;',
	),            	
	'failed_message_ka_GE' => array(
			'title'        => __('Failed Message (Georgian)', 'unipay'),
			'type'         => 'textarea',
			'desc_tip'  =>  __('Message to be displayed on failed transaction.', 'unipay'),
			'default'      => __('ტრანზაქცია უარყოფილია.', 'unipay'),
			'css'		   => 'max-width:400px;',
	),
	'unipay-checkout-page' => array(
            'title'       => __( 'UniPAY Checkout page', 'unipay' ),
            'type'        => 'title',
            'description' => __('Advanced options of UniPAY Checkout page','unipay'),
	),
	'order-description' => array(
			'title'        => __('Order Description', 'unipay'),
			'type'         => 'checkbox',						
			'label'        => __('Enable Order Description', 'unipay'),
			'default'      => 'yes',
			'desc_tip'  =>  __('This controls  the description  which the user sees during UniPAY  checkout page', 'unipay'),
	),	
	'order-quantity' 	=> array(
			'title'        => __('Order Quantity', 'unipay'),
			'type'         => 'checkbox',						
			'label'        => __('Enable Order Quantity', 'unipay'),
			'default'      => 'yes',
			'desc_tip'     =>  __('This controls  the order quantity  which the user sees during UniPAY  checkout page', 'unipay'),
	),																					
  	'slogan'    		=> array(
			'title'        => __('Slogan', 'unipay'),
			'type'         => 'text',
			'desc_tip'     => __('Add text is  displayed as your slogan of  the UniPAY checkout page (max. 70 symbols)', 'unipay'),
			'placeholder'  => 'Optional',
  ),
	'logo_image_url'	 => array(
			'title'         => __( 'Logo Image (150x80)', 'unipay' ),
			'type'          => 'image',
			'desc_tip'      => __('Upload image is displayed as your logo in the upper left corner of  the UniPAY checkout page', 'unipay'),
            'description'   => __( 'The image must be PNG or JPG format.', 'unipay' ),
			
	),
);

return apply_filters( 'woocommerce_unipay_checkout_settings', $settings );