=== UniPAY Payment Gateway For WooCommerce ===
Contributors: unipaydev
Tags: payment, unipay, woocommerce, unipay checkout, credit card, payments, payment gateway, qrpay, georgia, tbc bank, bank of Georgia, bog
Requires at least: 3.3
Tested up to: 6.6
Requires PHP: 5.6
Stable tag: 1.0.4
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Extends WooCommerce 6.2.0 to Process Payments with UniPAY gateway

== Description ==

In a few simple steps you can start accepting credit card payments with UniPAY Checkout on your WordPress site.

UniPAY Payment Gateway For WooCommerce plugin is the official [UniPAY.com](https://www.unipay.com) plugin. with easy integration setup, it integrates all UniPAY payment methods into your woocommerces shop: UniPAY wallet, Visa, MasterCard, AMEX and QR PAYMENTS

== ABOUT UniPAY.com ==

you can get more informaiton about checkout solution on [UniPAY.com](https://www.unipay.com) pages.

== SUPPORT ==
You can contact support for this plugin thourght our [contact page](https://www.unipay.com/en/contact).

== Installation ==

= Minimum Requirements =

* WooCommerce 3.0 or greater
* PHP version 5.6 or greater
* cURL

1. Upload `woocommerece-unipay` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to: WooCommerce > Settings > Payments > UniPAY Checkout.
4. Enter your Live MerchantID and Live SecretKey (obtained from your UniPAY account).
5. Enter a Title (required) , Description (optional) and Button (required) seen by the customer during checkout. 
6. Tick the Enable  payment logo. Untick to disable.
7. Define logo color, size and type.
8. Set statuses for your comfort which will accorded on unipay statuses.
7. Enter Transaction Success and Failed messages in fields named "Success Message" and "Failed Message".
8. Tick the Enable Order Description. Untick to disable.
9. Tick the Enable  Order Quantity. Untick to disable.
10. Enter Slogan which will appeared on our checkout page (max. 70symbols).
11. Upload your company logo (150 X 80, PNG or JPG) which will appeared on our checkout page.

== Changelog ==

= 2018.08.02 - version 1.0.0 =
*add - Enable or disable Payment logo on  Checkout page;
*add - Payment logo control on Checkout page (you can define logo size, color and type);
*add - Payment button text control on Checkout page;
*add - Manage Payment status;
*add - Control to Enable or disable log;
*add - Possibility to upload logo on our checkout page;

= 2018.08.20 - version 1.0.1 =
*add - Show error message, when merchant ip address is incorect;
*fix - Fix product description and slogan regex;

= 2018.12.01 - version 1.0.2 =
*fix - Corrected redirect process on UniPAY checkout page;

= 2018.12.26 - version 1.0.3 =
*add - New currency parameter has been added to the payment system, which defines the UniPAY payment method in national currncy (GEL) at the checkout;

= 2019.02.19 - version 1.0.4 =
*add Feature "Payment logo control on Checkout page" is disabled and there is only one size in update version.

= 2019.12.26 - version 1.0.4 =
*tested - Plugin is fully compatible for WordPress version 5.3.2
*update - Screenshots, logos and Description

= 2021.02.23 - version 1.0.5 =
*tested - Plugin is fully compatible for WordPress version 5.6.2
*tested - Plugin is fully compatible for PHP version 7.4
*tested - Plugin is fully compatible for Woocommerce version 5.0.0

= 2021.10.08 - version 1.0.6 =
*fix - Performance and stability improvements.

= 2022.02.08 - version 2.1 =
*fix - Performance and stability improvements.

= 2022.03.18 - version 2.2 =
*fix - Performance and stability improvements.

== Frequently asked questions ==


== Screenshots ==

1. First of all click the settings.
2. Choose payment method.
3. Choose UniPay Checkout and manage it.
4. Click Enable UniPAY Payment module.
5. Enter Merchand ID and secret key.
6. Control logo size,color, type.
7. Control quantity-description-slogan-logo on UniPAY Checkout page.
8. Choose UniPAY Checkout.


== Upgrade notice ==

= 1.0.0 =
* Feature - Enable or disable Payment logo on  Checkout page, Payment logo control on Checkout page (you can define logo size, color and type),
 Payment button text control on Checkout page, Manage Payment status, Control to Enable or disable log, Possibility to upload logo on our checkout page.
 
= 1.0.1 =
* Feature - Show error message, when merchant ip address is incorect. Fix product description and slogan regex.

= 1.0.2 =
* Corrected redirect process on UniPAY checkout page

= 1.0.3 =
* New currency parameter has been added to the payment system, which defines the UniPAY payment method in national currncy (GEL) at the checkout.

= 1.0.4 =
* Feature "Payment logo control on Checkout page" is disabled and there is only one size in update version.

= 1.0.5 =
* Performance and stability improvements.

= 1.0.6 =
* Performance and stability improvements.

= 2.1 =
* Performance and stability improvements.

= 2.2 =
* Performance and stability improvements.