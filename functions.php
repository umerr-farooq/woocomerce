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

// Add product description under "Add to Cart" button
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

// To change add to cart text on product archives(Collection) pages globaly
add_filter( 'woocommerce_product_add_to_cart_text', 'woocommerce_custom_product_add_to_cart_text' );  
function woocommerce_custom_product_add_to_cart_text() {
	return __( 'Shop Now', 'woocommerce' );
}
//

?>
