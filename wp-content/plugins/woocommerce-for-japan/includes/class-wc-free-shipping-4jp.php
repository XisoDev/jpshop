<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * WooCommerce For Japan

 * @package woocommerce-for-japan
 * @author Artisan Workshop
 */

/**
 * Free Shipping display for Japan, if free shipping situation, only display free shipping
 */

add_filter( 'woocommerce_package_rates', 'hide_shipping_when_free_is_available', 100 );

/**
* Hide shipping rates when free shipping is available.
* Updated to support WooCommerce 2.6 Shipping Zones.
*
* @param array $rates Array of rates found for the package.
* @return array
*/
function hide_shipping_when_free_is_available ( $rates ) {
	$free = array();
	foreach ( $rates as $rate_id => $rate ) {
		if ( 'free_shipping' === $rate->method_id ) {
			$free[ $rate_id ] = $rate;
			break;
		}
	}
	return ! empty( $free ) ? $free : $rates;
}
