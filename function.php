<?php

// Add scripts to wp_head()






add_filter( 'woocommerce_billing_fields', 'wc_change_required_fields');

function wc_change_required_fields($address_fields) {
    $packages = WC()->shipping->get_packages();
    foreach ( $packages as $i => $package ) {
        $chosen_method = isset( WC()->session->chosen_shipping_methods[ $i ] ) ? WC()->session->chosen_shipping_methods[ $i ] : '';
    }
	var_dump( $chosen_method );
    if ($chosen_method == 'FermoPoint_Shipping_Method') {
        $address_fields['billing_address_1']['required'] = true;
        // place your changes that depend on the shipping method here...
    }
}








?>

