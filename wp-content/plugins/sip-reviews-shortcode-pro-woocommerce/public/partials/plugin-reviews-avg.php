<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://shopitpress.com
 * @since      1.0.9
 *
 * @package    Sip_Reviews_Shortcode_Woocommerce
 * @subpackage Sip_Reviews_Shortcode_Woocommerce/public/partials
 */


/**
 * Add hook for shortcode tag
 *
 * @since    	1.1.9
 */
add_shortcode ('woocommerce_reviews_avgstars', 'sip_reviews_avgstars_callback' );
add_shortcode ('woocommerce_reviews_avgtext', 'sip_reviews_avgtext_callback' );
add_shortcode ('woocommerce_reviews_number', 'sip_reviews_number_callback' );

/**
 * callback function of woocommerce_reviews_avgstars
 *
 * @since    	1.1.9
 */
function sip_reviews_avgstars_callback( $atts ) {

	//It calculate the rating for each star
	global $post, $wpdb, $product;
	extract( shortcode_atts(
		array(
			'id' => ''
		), $atts )
	);

	if ( $id === '' || $id === 0 ) {
		$id = method_exists( $product, 'get_id' ) ? $product->get_id() : $product->id;
	} elseif ( $id == "all" ) {
		
		$id = sip_rswc_get_all_ids( );
	} else {
		
		$id = sip_rswc_get_explode( $id );
	}

	$id  = str_replace( " ", "", $id );
	$ids = explode( ",", strval( $id ) );

	ob_start();
	//It calculate the rating for each star
	
	$result	= 0;
	$comments_count = 1;
	foreach ( $ids as $key => $post_id ) {

		$get_average_rating = $get_average_rating = sip_get_avg_rating( $post_id );

		$result = $result + $get_average_rating;
		$comments_count = 1 + $key;
	}

	$result = number_format( $result / $comments_count, 2, '.', '' );

	ob_start();
		?><span class="sip-score-star"><span style="width: <?php echo ( $result * 20 ) ?>%"></span></span><?php
	return  ob_get_clean();

}

/**
 * callback function of woocommerce_reviews_avgtext
 *
 * @since    	1.1.9
 */
function sip_reviews_avgtext_callback( $atts ) {

	global $post, $wpdb, $product;
	extract( shortcode_atts(
		array(
			'id' => ''
		), $atts )
	);

	if ( $id === "" || $id === 0 ) {
		$id = method_exists( $product, 'get_id' ) ? $product->get_id() : $product->id;
	} elseif ( $id == "all" ) {

		$id = sip_rswc_get_all_ids( );
	} else {

		$id = sip_rswc_get_explode( $id );
	}

	$id  = str_replace( " ", "", $id );
	$ids = explode( ",", strval( $id ) );

	ob_start();
	//It calculate the rating for each star

	$result	= 0;
	$comments_count = 1;
	foreach ( $ids as $key => $post_id ) {

		$get_average_rating = sip_get_avg_rating( $post_id );

		$result = $result + $get_average_rating;
		$comments_count = 1 + $key;
	}

	$result = number_format( $result / $comments_count, 2, '.', '' );

	echo  $result . __(" out of 5 stars" , "sip-reviews-shortcode");

	return  ob_get_clean();
}

/**
 * callback function of woocommerce_reviews_number
 *
 * @since    	1.1.9
 */
function sip_reviews_number_callback( $atts ) {

	global $post, $wpdb, $product;
	extract( shortcode_atts(
		array(
			'id' => ''
		), $atts )
	);

	if ( $id === "" || $id === 0 ) {
		$id = method_exists( $product, 'get_id' ) ? $product->get_id() : $product->id;
	} elseif ( $id == "all" ) {

		$id = sip_rswc_get_all_ids( );
	} else {
		
		$id = sip_rswc_get_explode( $id );
	}

	$id  = str_replace( " ", "", $id );
	$ids = explode( ",", strval( $id ) );

	$comments_approved_sum = 0;

	foreach ( $ids as $key => $post_id ) {
		$comments_approved 	= sip_get_review_count( $post_id );
		$comments_approved_sum = $comments_approved_sum + $comments_approved;
	}

	return $comments_approved_sum;
}

function sip_get_review_count( $product_id = '' ) {
	return get_post_meta( $product_id, '_wc_review_count', true );
}

function sip_get_avg_rating( $product_id = '' ) {
	return get_post_meta( $product_id, '_wc_average_rating', true );	
}

function sip_get_rating_count( $product_id = '' ) {
	return get_post_meta( $product_id, '_wc_rating_count', true );	
}

function sip_get_price( $product_id = '' ) {
	return get_post_meta( $product_id, '_price', true );	
}

function sip_rswc_get_all_ids( $id = "all" ) {
	global $wpdb;
	$ids = $wpdb->get_results( "SELECT ID FROM $wpdb->posts WHERE post_type = 'product' AND post_status = 'publish' AND comment_count > 0 ", ARRAY_A );
	$id_ = "";
	$loop = 0;

	foreach ( $ids as $id ) {

		$comments_count = wp_count_comments( $id['ID'] );
		if ( $comments_count->approved ) {

			if( $loop!= 0 ) {
				$id_ .= ",";
			}

			$id_ .= $id['ID'];
			$loop++;
		}
	}

	return $id_;
}

function sip_rswc_get_explode( $id = 0 ) {
	$id  = str_replace( " ", "", $id );
	$ids = explode( ",", strval( $id ) );
	$id_ = "";
	$loop = 0;

	foreach ( $ids as $id ) {
		$comments_count = wp_count_comments( $id );
		if ( $comments_count->approved ) {

			if( $loop!= 0 ) {
				$id_ .= ",";
			}

			$id_ .= $id ;
			$loop++;
		}
	}

	return $id_;
}


add_action( 'wp_head', function() {

	global $post;
	if( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'woocommerce_reviews_avgstars') ) {

		$options 	= get_option( 'color_options' );
	  	$star_color = ( isset( $options['star_color'] ) ) ? sanitize_text_field( $options['star_color'] ) : '#d1c51d';
	  	?>
			<style>
				.sip-score-star span { color: <?php echo $star_color; ?>; }
			</style>
	  	<?php
	}
}, 10 );