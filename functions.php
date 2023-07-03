<?php
/*** Remove product data tabs ***/
add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tabs', 98 );

function woo_remove_product_tabs( $tabs ) {
    unset( $tabs['description'] );      	// Remove the description tab
    unset( $tabs['reviews'] ); 			// Remove the reviews tab
    unset( $tabs['additional_information'] );  	// Remove the additional information tab
    return $tabs;
}
//

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );

/*** Add product description under "Add to Cart" button ***/
function woocommerce_product_description() {
    global $product;

    if ( $product->get_description() ) {
        echo '<div class="woocommerce-product-details__short-description woocommerce-product-description">';
        echo '<h2>Product Description</h2>';
        echo '<p>' . $product->get_description() . '</p>';
        echo '</div>';
    }
}
add_action( 'woocommerce_after_add_to_cart_button', 'woocommerce_product_description', 10 );
//

/*** To change add to cart text on product archives(Collection) pages globaly ***/
add_filter( 'woocommerce_product_add_to_cart_text', 'woocommerce_custom_product_add_to_cart_text' );  
function woocommerce_custom_product_add_to_cart_text() {
	return __( 'Shop Now', 'woocommerce' );
}
//

/*** disable shipping rates methods based on variations (local-pickup == 0) ***/

// define the woocommerce_cart_shipping_packages callback 
function filter_woocommerce_cart_shipping_packages( $package ) { 
    
    $new_cart = [];
    global $woocommerce;
    foreach($woocommerce->cart->get_cart() as $cart_item) {
       
      // check for desired shipping method
      // cart items not checking for this property, will not be accounted for shipping costs
   
         if( isset($cart_item['variation']['attribute_pa_select-delivery-option']) && $cart_item['variation']['attribute_pa_select-delivery-option'] == "delivery"){
            //  print_r( $cart_item['variation']['attribute_pa_select-delivery-option'] ); 
         array_push($new_cart, $cart_item); 
      } 
    }

    if(!empty($new_cart)) $package[0]['contents'] = $new_cart;
    return $package; 
}; 
         
// add the filter 
add_filter( 'woocommerce_cart_shipping_packages', 'filter_woocommerce_cart_shipping_packages', 10, 1 ); 
//


/*** Display Stock Status on Woocommerce Product Page ***/
#Today I am sharing the solution for Display Stock Status on Woocommerce Product Page (In Stock or Out of Stock), Add following function to your theme function php file. Please change the text ‘Available!’,’Sold Out’ to your own text like In Stock or Out of Stock something like that what you want to display.
/** For more wordpress tips visit www.creativetweets.com**/
add_filter( 'woocommerce_get_availability', 'wcs_custom_get_availability', 1, 2);
function wcs_custom_get_availability( $availability, $_product ) {
    
    // Change In Stock Text
    if ( $_product->is_in_stock() ) {
        $availability['availability'] = __('Available!', 'woocommerce');
    }
    // Change Out of Stock Text
    if ( ! $_product->is_in_stock() ) {
        $availability['availability'] = __('Sold Out', 'woocommerce');
    }
    return $availability;
}
//

/*** Add Continue Shopping Button on WooCommerce Cart Page ***/
#Hi All, WooCommerce is not included continue shopping button on cart page, use the following code for adding continue shopping button on WooCommerce Cart Page. Add this function to your theme functions.php file.
add_action( 'woocommerce_before_cart_table', 'woo_add_continue_shopping_button_to_cart' );
function woo_add_continue_shopping_button_to_cart() {
 $shop_page_url = get_permalink( woocommerce_get_page_id( 'shop' ) );
 
 echo '<div class="woocommerce-message">';
 echo ' <a href="'.$shop_page_url.'" class="button">Continue Shopping →</a> Would you like some more goods?';
 echo '</div>';
}
//

/*** Rename WooCommerce Order Status ***/
#Got the solution for Rename WooCommerce Order Status, Means you can change WooCommerce default order status like “Processing”, “Completed” etc to your own custom status. Here I changed WooCommerce order status “Processing” to “In progress” and “Completed” to “Delivered”. Add the following code to your theme functions.php.
function wc_renaming_order_status( $order_statuses ) {
    foreach ( $order_statuses as $key => $status ) {
        $new_order_statuses[ $key ] = $status;
        if ( 'wc-processing' === $key ) {
            $order_statuses['wc-processing'] = _x( 'In progress', 'Order status', 'woocommerce' );
        }
		if ( 'wc-completed' === $key ) {
            $order_statuses['wc-completed'] = _x( 'Delivered', 'Order status', 'woocommerce' );
        }
    }
    return $order_statuses;
}
add_filter( 'wc_order_statuses', 'wc_renaming_order_status' );
//

/*** How to add an additional charges for cash on delivery payment method (cod) in Woocommerce ? ***/
#We can add an additional charge for only cash on delivery (COD) in woocommerce shopping cart website. Please add the below function code in your theme folder function.php file. In this code i have added 25 as Handling Charges( ‘$fee = 25;’ ) you can change this.
// Add a custom fee based o cart subtotal
add_action( 'woocommerce_cart_calculate_fees', 'custom_handling_fee', 10, 1 );
function custom_handling_fee ( $cart ) {
    if ( is_admin() && ! defined( 'DOING_AJAX' ) )
        return;

    if ( 'cod' === WC()->session->get('chosen_payment_method') ) {
        $fee = 25;
        $cart->add_fee( 'Handling Charges', $fee, true );
    }
}

// jQuery - Update checkout on methode payment change
add_action( 'wp_footer', 'custom_checkout_jqscript' );
function custom_checkout_jqscript() {
    if ( is_checkout() && ! is_wc_endpoint_url() ) :
    ?>
    <script type="text/javascript">
    jQuery( function($){
        $('form.checkout').on('change', 'input[name="payment_method"]', function(){
            $(document.body).trigger('update_checkout');
        });
    });
    </script>
    <?php
    endif;
}
//

/*** Change Woocommerce Default User Registration Role ***/
#Hi, got the solution for changing the Woocommerce default user registration role from “customer” to any other role. Add following function to your theme function php file and replace “subscriber” to your own user roles like Administrator, Editor, shop manager, or your custom user role.
/** Here changing 'customer' role to 'subscriber'.**/
add_filter('woocommerce_new_customer_data', 'wc_assign_custom_role', 10, 1);

function wc_assign_custom_role($args) {
  $args['role'] = 'subscriber';
  
  return $args;
}
//

/*** Change SKU text label in woocommerce to Product Code ***/
#Add the following code to your theme functions.php file for changing SKU text label in woocommerce to Product Code.
function translate_woocommerce($translation, $text, $domain) {
    if ($domain == 'woocommerce') {
        switch ($text) {
            case 'SKU':
                $translation = 'Product Code';
                break;
            case 'SKU:':
                $translation = 'Product Code:';
                break;
        }
    }
    return $translation;
}
add_filter('gettext', 'translate_woocommerce', 10, 3);
//

/*** Conditional Tags for Woocommerce ***/
#For pages which use Woocommerce templates (cart and checkout pages are not included)
if(is_woocommerce() ) { ?>	
<div class="joint_btn">Only for pages which use woocommerce templates (cart and checkout pages are not included)</div>
<?php }

#Only for woocommerce shop page
if(is_shop() ) { ?>	
<div class="joint_btn">Only for woocommerce shop page</div>
<?php }
	
#Only for woocommerce product category archive page
if(is_product_category() ) { ?>	
<div class="joint_btn">Only for woocommerce product category archive page </div>
<?php }

#Only for woocommerce single product page
if(is_product() ) { ?>	
<div class="joint_btn">Only for woocommerce single product page</div>
<?php }

#Only for woocommerce cart page
if(is_cart() ) { ?>	
<div class="joint_btn">Only for woocommerce cart page</div>
<?php }

#Only for woocommerce checkout page
if(is_checkout() ) { ?>	
<div class="joint_btn">Only for woocommerce checkout page</div>
<?php }

#Only for woocommerce customer’s account page
if(is_account_page() ) { ?>	
<div class="joint_btn">Only for woocommerce customer’s account page</div>
<?php }
//

?>
