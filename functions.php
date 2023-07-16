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

/*** Rename “Place Order” Button Dynamically Based on Selected Payment Gateway @ WooCommerce Checkout ***/
add_filter( 'woocommerce_available_payment_gateways', 'bbloomer_rename_place_order_button' );
 
function bbloomer_rename_place_order_button( $gateways ) {
    if ( $gateways['bacs'] ) {
        $gateways['bacs']->order_button_text = 'View Bank Details';
    } elseif ( $gateways['cod'] ) {
        $gateways['cod']->order_button_text = 'Confirm Cash on Delivery';
    } 
    return $gateways;
}
//

/*** Redirect WooCommerce Product Category Page to Single Product Page ***/
add_action( 'wp', 'bbloomer_redirect_cat_to_product' );
  
function bbloomer_redirect_cat_to_product() {
   if ( is_product_category( 'tables' ) ) {
      wp_safe_redirect( get_permalink( 123 ) ); // PRODUCT ID
      exit;
   }
}
//

/*** Disable Payment Gateway For Specific Shipping Method – WooCommerce ***/
#In this example, I will disable “COD” payment gateway for all “local pickup” shipping rates in whatever shipping zone. You can also target a specific shipping rate (in a single zone).
add_filter( 'woocommerce_available_payment_gateways', 'bbloomer_gateway_disable_for_shipping_rate' );
  
function bbloomer_gateway_disable_for_shipping_rate( $available_gateways ) {
   if ( ! is_admin() && WC()->session ) {
      $chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
      $chosen_shipping = $chosen_methods[0];
      if ( isset( $available_gateways['cod'] ) && 0 === strpos( $chosen_shipping, 'local_pickup' ) ) {
         unset( $available_gateways['cod'] );
      }
   }
   return $available_gateways;
}
//

/*** Set Default Billing Country or State @ WooCommerce Checkout Page ***/
// Example 1: default state to OREGON
 
add_filter( 'default_checkout_billing_state', 'bbloomer_change_default_checkout_state' );
  
function bbloomer_change_default_checkout_state() {
  return 'OR'; // state code
}
 
// Example 2: default country to United States
 
add_filter( 'default_checkout_billing_country', 'bbloomer_change_default_checkout_country' );
 
function bbloomer_change_default_checkout_country() {
  return 'US'; 
}
//

/*** Add Sale Price End Date to Sale Badge @ WooCommerce Shop, Archive, Product Pages ***/
add_filter( 'woocommerce_sale_flash', 'bbloomer_sale_end_date', 9999, 3 );
 
function bbloomer_sale_end_date( $html, $post, $product ) {
   if ( $product->get_date_on_sale_to() ) return $html . ' (ends ' . gmdate( 'd M', $product->get_date_on_sale_to()->getTimestamp() ) . ')'; 
   return $html;
}
//

/*** Display Additional Content @ WooCommerce Single Product Additional Information Tab ***/
add_action( 'woocommerce_product_additional_information', 'bbloomer_add_content_additional_information_tab', 11 );
 
function bbloomer_add_content_additional_information_tab( $product ) {
   echo '<p>Test</p>';
}
//

/*** Rename “Related products” Title @ WooCommerce Single Product Page ***/
add_filter( 'woocommerce_product_related_products_heading', 'bbloomer_rename_related_products' );
 
function bbloomer_rename_related_products() {
   return "Customers also viewed";
}
//

/*** Capitalize Product Titles @ Shop & Single Product Pages ***/
add_filter( 'the_title', 'bbloomer_capitalize_single_prod_title', 9999, 2 );
 
function bbloomer_capitalize_single_prod_title( $post_title, $post_id ) {
   if ( ! is_admin() && 'product' === get_post_type( $post_id ) ) {
      $post_title = ucwords( strtolower( $post_title ) );
   }
   return $post_title;
}
//

/*** Additional Product Description @ WooCommerce Single Product Page ***/
#There are times when the “long description” and the “short description” are not enough on the WooCommerce Single Product page. What if you need to add another HTML content section – say – at the very bottom of the page (and maybe, because of the longer page, add another add to cart button there as well)?
#In this simple snippet, we will add another “WYSIWYG” text editor in the Edit Product page, and display the output at the bottom of the single product page. Enjoy!
add_action( 'add_meta_boxes', 'bbloomer_new_meta_box_single_prod' );
 
function bbloomer_new_meta_box_single_prod() {
   add_meta_box(
      'custom_product_meta_box',
      'Product third description',
      'bbloomer_add_custom_content_meta_box',
      'product',
      'normal',
      'default'
   );
}
 
function bbloomer_add_custom_content_meta_box( $post ){
   $third_desc = get_post_meta( $post->ID, '_third_desc', true ) ? get_post_meta( $post->ID, '_third_desc', true ) : '';   
   wp_editor( $third_desc, '_third_desc' );
}
 
add_action( 'save_post_product', 'bbloomer_save_custom_content_meta_box', 10, 1 );
 
function bbloomer_save_custom_content_meta_box( $post_id ) {
   if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
   if ( ! isset( $_POST['_third_desc'] ) ) return;
   update_post_meta( $post_id, '_third_desc', $_POST['_third_desc'] );
}
 
add_action( 'woocommerce_after_single_product_summary' , 'bbloomer_third_desc_bottom_single_product', 99 );
   
function bbloomer_third_desc_bottom_single_product() {
   global $product;
   $third_desc = get_post_meta( $product->get_id(), '_third_desc', true ) ? get_post_meta( $product->get_id(), '_third_desc', true ) : '';
   if ( ! $third_desc ) return;
   echo '<div>';
   echo $third_desc;
   echo '</div>';
}
//

/*** Count External Product “Buy Product” Button Clicks & Display Counter @ Product Admin **/
#Lots to learn today:
#First, we remove the default external product add to cart button, and code ours instead
#Some JS triggers the ‘increment_counter’ Ajax function on button click
#The ‘increment_counter’ Ajax function counts and stores the number of clicks
#The ‘manage_edit-product_columns’ and ‘manage_product_posts_custom_column’ display a new column in the Products admin page, and place the counter value in it
add_action( 'woocommerce_external_add_to_cart', 'bblomer_new_external_add_to_cart', 1 );
 
function bblomer_new_external_add_to_cart() {
   remove_action( 'woocommerce_external_add_to_cart', 'woocommerce_external_add_to_cart', 30 );
   add_action( 'woocommerce_external_add_to_cart', 'bbloomer_external_add_to_cart', 30 );
}
 
function bbloomer_external_add_to_cart() {
   global $product;
   if ( ! $product->add_to_cart_url() ) return;
   echo '<p><a href="' . $product->add_to_cart_url() . '" class="single_add_to_cart_button button alt countable" data-pid="' . $product->get_id() . '">' . $product->single_add_to_cart_text() . '</a></p>';
   wc_enqueue_js( "
      $('a.countable').click(function(e){
         e.preventDefault();
         $.post( '" . '/wp-admin/admin-ajax.php' . "', { action: 'increment_counter', pid: $(this).data('pid') } );
         window.open($(this).attr('href'));
      });
   " );
}
 
add_action( 'wp_ajax_increment_counter', 'bbloomer_increment_counter' );
add_action( 'wp_ajax_nopriv_increment_counter', 'bbloomer_increment_counter' );
 
function bbloomer_increment_counter() {
   $pid = $_POST['pid'];
   $clicks = get_post_meta( $pid, '_click_counter', true ) ? (int) get_post_meta( $pid, '_click_counter', true ) + 1 : 1;
   update_post_meta( $pid, '_click_counter', $clicks );
   wp_die();
}
 
add_filter( 'manage_edit-product_columns', 'bbloomer_admin_products_views_column', 9999 );
 
function bbloomer_admin_products_views_column( $columns ){
   $columns['clicks'] = 'Clicks';
   return $columns;
}
 
add_action( 'manage_product_posts_custom_column', 'bbloomer_admin_products_views_column_content', 9999, 2 );
 
function bbloomer_admin_products_views_column_content( $column, $product_id ){
   if ( $column == 'clicks' ) {
      echo get_post_meta( $product_id, '_click_counter', true );
    }
}
//

/*** Hide Specific “Additional Information” Tab Attribute @ Single Product ***/
add_filter( 'woocommerce_display_product_attributes', 'bbloomer_exclude_attribute_from_attribute_table', 9999, 2 );
 
function bbloomer_exclude_attribute_from_attribute_table( $product_attributes, $product ) {
   if ( isset( $product_attributes[ 'attribute_pa_color' ] ) ) unset( $product_attributes[ 'attribute_pa_color' ] );
   return $product_attributes;
}
//

/*** Automatically Add Tag To Purchased Products ***/
#Auto-tag Products Upon Purchase @ WooCommerce Thank You Page
#This functionality can be helpful to those who need to differentiate purchased products from non-purchased ones. Think about a way to automatically discount non-tagged products, in order to entice more sales; or a function that only shows purchased products via a custom shortcode.
#No matter the application, “tagging” products upon purchase is super easy. Of course, make sure to create a custom product tag first, and get its ID, so that you can use this in the code below. Enjoy!
add_action( 'woocommerce_thankyou', 'bbloomer_auto_tag_product' );
 
function bbloomer_auto_tag_product( $order_id ) {
   $order = wc_get_order( $order_id );
   $auto_tag_id = array( 12345 ); // YOUR TAG ID HERE
   foreach ( $order->get_items() as $item_id => $item ) {
      $product = $item->get_product();
      $tags = $product->get_tag_ids();
      if ( ! array_intersect( $tags, $auto_tag_id ) ) {
         $product->set_tag_ids( array_merge( $tags, $auto_tag_id ) );
         $product->save();
      }
   }
}
//

/*** Allow Customers To Define the Product Price ***/
#Pick Your Product Price @ WooCommerce Single Product Page
#This is a great customization for those WooCommerce store owners who are willing to accept donations, custom amounts, or need anyway that the customer enters a custom price on the product page for paying an invoice or a bill.
#This is as simple as creating a simple product with $0 price, and after that using the snippet below to display an input field on the single product page, where customers can enter their custom amount. Enjoy!
#Note: you first need to create a simple product called anything you like e.g. “Donation” or “Pay Your Bill”, give it a $0.00 price, and get its product ID so that this can be used in the code below (ID 241982 in my case).
add_action( 'woocommerce_before_add_to_cart_button', 'bbloomer_product_price_input', 9 );
  
function bbloomer_product_price_input() {
   global $product;
   if ( 241982 !== $product->get_id() ) return;
   woocommerce_form_field( 'set_price', array(
      'type' => 'text',
      'required' => true,
      'label' => 'Set price ' . get_woocommerce_currency_symbol(),
   ));
}
  
add_filter( 'woocommerce_add_to_cart_validation', 'bbloomer_product_add_on_validation', 9999, 3 );
  
function bbloomer_product_add_on_validation( $passed, $product_id, $qty ) {
   if ( isset( $_POST['set_price'] ) && sanitize_text_field( $_POST['set_price'] ) == '' ) {
      wc_add_notice( 'Set price is a required field', 'error' );
      $passed = false;
   }
   return $passed;
}
  
add_filter( 'woocommerce_add_cart_item_data', 'bbloomer_product_add_on_cart_item_data', 9999, 2 );
  
function bbloomer_product_add_on_cart_item_data( $cart_item, $product_id ) {
   if ( 241982 !== $product_id ) return $cart_item;    
   $cart_item['set_price'] = sanitize_text_field( $_POST['set_price'] );
   return $cart_item;
}
 
add_action( 'woocommerce_before_calculate_totals', 'bbloomer_alter_price_cart', 9999 );
  
function bbloomer_alter_price_cart( $cart ) {
   if ( is_admin() && ! defined( 'DOING_AJAX' ) ) return;
   if ( did_action( 'woocommerce_before_calculate_totals' ) >= 2 ) return;
   foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
      $product = $cart_item['data'];
      if ( 241982 !== $product->get_id() ) continue;
      $cart_item['data']->set_price( $cart_item['set_price'] );
   } 
}
//

/*** Additional “Store Address” @ General Settings ***/
#Second Store Address @ WooCommerce > Settings > General
#So, we all know that the “Store Address” fields under WooCommerce > Settings > General are used by other WooCommerce functions such as the initial setup wizard, currency switchers, language plugins as well as taxes and shipping calculations. Also, it may display on PDF invoices, WooCommerce emails and static pages.
#This is all good and easy, but as usual businesses are not made equal. It could be that you need to show an additional address; for example, the “Warehouse Address”.
#In this tutorial, we will add a new “Warehouse Address” section and address fields under the “Store Address” settings, and also see how we can easily retrieve this custom address so that you can display it anywhere. Enjoy!
add_filter( 'woocommerce_general_settings', 'bbloomer_additional_store_addresses_admin', 9999 );
 
function bbloomer_additional_store_addresses_admin( $settings ) {
    
   $new_settings = array(
    
      array(
         'title' => 'Warehouse Address',
         'type'  => 'title',
         'id'    => 'wh_address',
      ),
 
      array(
         'title'    => __( 'Address line 1', 'woocommerce' ),
         'id'       => 'woocommerce_wh_address',
         'type'     => 'text',
      ),
 
      array(
         'title'    => __( 'Address line 2', 'woocommerce' ),
         'id'       => 'woocommerce_wh_address_2',
         'type'     => 'text',
      ),
 
      array(
         'title'    => __( 'City', 'woocommerce' ),
         'id'       => 'woocommerce_wh_city',
         'type'     => 'text',
      ),
 
      array(
         'title'    => __( 'Country / State', 'woocommerce' ),
         'id'       => 'woocommerce_wh_country',
         'type'     => 'single_select_country',
      ),
 
      array(
         'title'    => __( 'Postcode / ZIP', 'woocommerce' ),
         'id'       => 'woocommerce_wh_postcode',
         'type'     => 'text',
      ),
 
      array(
         'type' => 'sectionend',
         'id'   => 'wh_address',
      ),
 
   );
    
   return array_merge( array_slice( $settings, 0, 7 ), $new_settings, array_slice( $settings, 7 ) );
    
}

#You’re wondering how I came up with the code above? Well, I simply found out how the General Settings are output by WooCommerce, found the handy woocommerce_general_settings filter, and copied the whole “Store Address” section after changing the fields’ ID.
#Finally, I used a combination of array_slice and array_merge to position the new section exactly after the Store Address one.
#The great thing is that no code is needed to “save” these new fields, WooCommerce does it already for you. Which means you can retrieve the new address in this way:

$warehouse_address = get_option( 'woocommerce_wh_address', '' );
$warehouse_address_2 = get_option( 'woocommerce_wh_address_2', '' );
$warehouse_city = get_option( 'woocommerce_wh_city', '' );
$warehouse_country = get_option( 'woocommerce_wh_country', '' );
$warehouse_zip = get_option( 'woocommerce_wh_postcode', '' );
//

/*** Redirect Specific Product Search To Custom URL ***/
#Redirect Specific Search Term Result to a Custom URL @ WooCommerce Frontend
add_action( 'template_redirect', 'bbloomer_redirect_search_results' );
 
function bbloomer_redirect_search_results() {
    if ( isset( $_GET['s'] ) && strcasecmp( $_GET['s'], 'tables' ) == 0 ) {
        wp_redirect( 'https://example.com' );
        exit();
    }
}
//

/*** Add to Cart Quantity Suffix ***/
#Add to Cart Quantity Input Suffix @ WooCommerce Single Product Page
add_action( 'woocommerce_after_quantity_input_field', 'bbloomer_echo_qty_front_add_cart' );
  
function bbloomer_echo_qty_front_add_cart() {
   echo '<span class="qty-suff">liters</span>'; 
}
//

/*** Display Product ACF Value @ Shop Page ***/
#Show Product ACF @ WooCommerce Shop / Loop Pages
#Note: please change the ACF field ID inside the get_field function to your own custom field ID. In my case I’ve used a custom field ID called “warranty“.
add_action( 'woocommerce_before_shop_loop_item_title', 'bbloomer_acf_loop' );
 
function bbloomer_acf_loop() {
   global $product;
   $warranty = get_field( 'warranty', $product->get_id() );
   if ( ! $warranty ) return;
   echo '<div><i>' . $warranty . '</i></div>';
}
//

/*** Disable ajax add-to-cart on product loop and redirect to single product page ***/
#This code removes the default "Add to Cart" button from the product archive and category pages by removing the woocommerce_template_loop_add_to_cart action. Then, it adds a new action custom_redirect_to_single_product that generates a custom button linking to the single product page using the product URL obtained from get_permalink().
#The disable_add_to_cart_redirect_single_product function is hooked to the wp action, which ensures that the functions are executed when the WordPress environment is fully loaded.
#You can add this code to your theme's functions.php file or create a custom plugin file in your WordPress installation to implement this functionality.
function disable_add_to_cart_redirect_single_product() {
        remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
        add_action( 'woocommerce_after_shop_loop_item', 'custom_redirect_to_single_product', 10 );
}

function custom_redirect_to_single_product() {
    global $product;
    $product_url = get_permalink( $product->get_id() );
    ?>
    <a href="<?php echo esc_url( $product_url ); ?>" class="button"><?php _e( 'View Product', 'text-domain' ); ?></a>
    <?php
}

add_action( 'wp', 'disable_add_to_cart_redirect_single_product' );


// OR if Specific page//

function disable_add_to_cart_redirect_single_product() {
    if ( is_archive() || is_product_category() ) {
        remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
        add_action( 'woocommerce_after_shop_loop_item', 'custom_redirect_to_single_product', 10 );
    }
}

function custom_redirect_to_single_product() {
    global $product;
    $product_url = get_permalink( $product->get_id() );
    ?>
    <a href="<?php echo esc_url( $product_url ); ?>" class="button"><?php _e( 'View Product', 'text-domain' ); ?></a>
    <?php
}

add_action( 'wp', 'disable_add_to_cart_redirect_single_product' );
//

/*** Add to Cart Pre-defined Quantity Selectors ***/
#I seriously spent more than usual trying to write a decent title. Still, I’m not 100% sure I’ve explained it well – so here’s some more context.
#The WooCommerce Single Product Page add to cart form features a quantity input and an add to cart button. Super simple. Customers can define a quantity and add the current product to the cart.
#Now, let’s imagine you want to change this experience based on your business requirements, and instead of the quantity input and add to cart button you want to show 3 buttons: “Add 1 to the cart“, “Add 2 to the cart“, “Add 3 to the cart“.
#And if you can match this with a bulk quantity discount functionality, you can even change the messaging to e.g. “Add 1 to the cart“, “Add 2 to the cart and save $X“, “Add 3 to the cart and save $Y“…
#So, let’s see how to hide the default add to cart form, and instead show buttons that allow the customer to add to cart a pre-defined product quantity (for simple products). As per this screenshot:
add_action( 'woocommerce_before_single_product', 'bbloomer_123_quantity_selectors', 1 );
 
function bbloomer_123_quantity_selectors() {
   global $product;
   add_action( 'woocommerce_single_product_summary', 'bbloomer_quantity_selectors', 17 );
   add_action( 'woocommerce_single_product_summary', 'bbloomer_quantity_selectors_css', 18 );
   remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
}
 
function bbloomer_quantity_selectors() {
   global $product;
   ?>
   <div class="quantities-wrapper">
        <div class="quantity-wrapper">
            <a class="single_add_to_cart_button button alt qtyselector" href="/?add-to-cart=<?php echo $product->get_id(); ?>&quantity=1">Add 1 <?php echo $product->get_name(); ?> to the cart</a>
        </div>
        <div class="quantity-wrapper">
            <a class="single_add_to_cart_button button alt qtyselector" href="/?add-to-cart=<?php echo $product->get_id(); ?>&quantity=2">Add 2 <?php echo $product->get_name(); ?> to the cart</a>
        </div>
        <div class="quantity-wrapper">
            <a class="single_add_to_cart_button button alt qtyselector" href="/?add-to-cart=<?php echo $product->get_id(); ?>&quantity=3">Add 3 <?php echo $product->get_name(); ?> to the cart</a>
        </div>
    </div>
   <?php
}
 
function bbloomer_quantity_selectors_css() {
   ?>
   <style>
       a.single_add_to_cart_button.qtyselector { display: block; border-radius: 8px; text-transform: uppercase; margin-bottom: 1em; text-align: center; }
   </style>
   <?php
}
//

/*** WooCommerce: Email Admin Upon Fatal Error ***/
#WooCommerce has a nice feature when it comes to WordPress Error 500 / Fatal Error – it logs the error and all the information regarding it inside the WooCommerce Status > Logs > Fatal Errors area.
#My problem is that sometimes these errors occur in the backend, so they may not trigger the WordPress built-in email that notifies the admin about the problem.
#What I want to try (please test it on your development website first, and not on your live website), is a way to get an email each time WooCommerce logs an error, so that I can go in and fix it immediately. Enjoy!
#Email Admin Each Time a WordPress Fatal Error Occurs
add_action( 'woocommerce_shutdown_error', 'bbloomer_email_fatal_errors' );
 
function bbloomer_email_fatal_errors( $error ) {
   $email_subject = "Critical Error On Your WooCommerce Site";
   $email_content = sprintf( __( '%1$s in %2$s on line %3$s', 'woocommerce' ), $error['message'], $error['file'], $error['line'] );
   wp_mail( get_option( 'admin_email' ), $email_subject, $email_content );
}
//

/*** Disable Checkout Field Autocomplete ***/
#By default, WooCommerce adds the “autocomplete” attribute to almost all checkout fields. For example, “billing_phone” has “autocomplete=tel”, “billing_country” has “autocomplete=country” and so on.
#When logged out or if the logged in user has never done a purchase before, the WooCommerce Checkout page fields are possibly autofilled by the browser based on saved data / addresses.
#Disable Autocomplete For Billing Phone @ WooCommerce Checkout
add_filter( 'woocommerce_checkout_fields', 'bbloomer_disable_autocomplete_checkout_fields' );
   
function bbloomer_disable_autocomplete_checkout_fields( $fields ) {
    $fields['billing']['billing_phone']['autocomplete'] = false;
    return $fields;
}

#You can target any of these checkout fields:

#Billing
// billing_last_name
// billing_company
// billing_address_1
// billing_address_2
// billing_city
// billing_postcode
// billing_country
// billing_state
// billing_email
// billing_phone

#Shipping
// shipping_first_name
// shipping_last_name
// shipping_company
// shipping_address_1
// shipping_address_2
// shipping_city
// shipping_postcode
// shipping_country
// shipping_state

#Account
// account_username
// account_password
// account_password-2

#Order
// order_comments

//

/*** Add Product To Order After Purchase ***/
#On Business Bloomer I sell a bundle of products, and I use no Bundles plugin for that. So the challenge was to programmatically add a list of products to the order upon purchase, once the bundle product is purchased.
#This is an amazing way to save time for the customer, as they don’t need to manually add each product to the cart. In the background, after a successful purchase, some magic code (that you find below) adds products to the order, sets their price to $0.00 (so that the order total is not altered), and saves the order. Enjoy!
#Programmatically Add Product To a Paid WooCommerce Order
#Please note – the code below searches through the order items to see if product ID = “123” is present. This is the “bundle” product.
#When that is purchased, my code triggers and adds to cart product ID = “456”. You can of course add multiple products by adding as many $order->add_product lines as you wish.
add_action( 'woocommerce_payment_complete', 'bbloomer_add_products_to_order', 9999 );
 
function bbloomer_add_products_to_order( $order_id ) {
   $order = wc_get_order( $order_id );
   foreach ( $order->get_items() as $item_id => $item ) {
      $product_id = $item->get_product_id();
      if ( $product_id && $product_id == 123 ) {
         $order->add_product( wc_get_product( 456 ), 1, array( 'subtotal' => 0, 'total' => 0 ) );   
         $order->save();         
         //wc_downloadable_product_permissions( $order_id, true ); ADD THIS IF ADDED PRODUCT IS DOWNLOADABLE
         //wc_update_product_stock( wc_get_product( 456 ), 1, 'decrease' ); ADD THIS IF YOU WANT TO REDUCE PRODUCT STOCK BY 1
         break;
      }
   }
}
//

/*** Add Text Under Each Product @ Shop Page ***/
#A client asked me to add a “Free Shipping” notice under each WooCommerce product on the Shop Page. This can increase your click-through rate and hence your sales conversion rate. Here are a couple of PHP and CSS snippets so that you can implement this helpful edit.
#show “Free Shipping” under each product @ WooCommerce Shop
add_action( 'woocommerce_after_shop_loop_item', 'bbloomer_show_free_shipping_loop', 5 );
 
function bbloomer_show_free_shipping_loop() {
   echo '<p class="shop-badge">Free Shipping</p>';
}
//

/*** Show Number Of Products Sold @ Product Page ***/
#WooCommerce database already stores the number of products sold for you.
#Therefore, you may want to show such number on the product page, close to the Add To Cart button. As we’ve seen in my book Ecommerce and Beyond, showing the number of sales for each product can increase your sales conversion rate.
#All you need is pasting the following code in your functions.php. Enjoy!
#Show Total Number of Sales @ WooCommerce Single Product Page
add_action( 'woocommerce_single_product_summary', 'bbloomer_product_sold_count', 11 );
  
function bbloomer_product_sold_count() {
   global $product;
   $units_sold = $product->get_total_sales();
   if ( $units_sold ) echo '<p>' . sprintf( __( 'Units Sold: %s', 'woocommerce' ), $units_sold ) . '</p>';
}
//

/*** How to add custom currency symbol in WooCommerce ***/
#Sometimes you need your own currency symbol in your store. For example, if your currency is Singapore Dollar, the WooCommerce shows it as “$”. Your site visitors will be confused about whether it is a US Dollar or which currency. In this article, we will see how to add a custom currency symbol in WooCommerce for products, carts, and checkout using the filter hook method.
#To make your currency symbol precise and more informative you can introduce your own symbol which helps users to understand the currency.
#You need to add the following snippet in functions.php of your child theme:
// add custom currency in WooCommerce settings
add_filter( 'woocommerce_currencies', 'add_c_currency' );
function add_c_currency( $c_currency ) {
    $c_currency['SING_DOLLAR'] = __( 'Singaporian Dollar ', 'woocommerce' );
    return $c_currency;
}

// define custom currency symbol
add_filter('woocommerce_currency_symbol', 'add_c_currency_symbol', 10, 2);
function add_c_currency_symbol( $custom_currency_symbol, $custom_currency ) {
    switch( $custom_currency ) {
	case 'SING_DOLLAR': $custom_currency_symbol = 'SG';        
	break;
    }
    return $custom_currency_symbol;
}
//

?>
