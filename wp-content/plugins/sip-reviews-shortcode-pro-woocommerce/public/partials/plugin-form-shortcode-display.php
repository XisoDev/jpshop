<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://shopitpress.com
 * @since      1.0.0
 *
 * @package    Sip_Reviews_Shortcode_Woocommerce
 * @subpackage Sip_Reviews_Shortcode_Woocommerce/public/partials
 */

/**
 * Sortcode function Template
 *
 * @since    	1.0.0
 */
function submit_woocommerce_review_form( $atts ) {

	global $post,$wpdb,$product;
	// Attributes
	extract( shortcode_atts(
		array(
			'id' => '',
		), $atts )
	);

	if( $id == 0 || $id == "" ) {
		if ( isset( $product->id ) ) {
			$id = $product->id;
		} else {
			return;
		}
	}

	$current_user = wp_get_current_user( );

	if( !wc_customer_bought_product( $current_user->email, $current_user->ID, $id ) && get_option( 'sip-rswc-setting-customer-review' ) ) {
		return;
	}

	?>
	<script>
		jQuery(document).ready(function($){
			
			var rating = "";
			$(".stars a").click(function() {
				rating = $(this).text();
			});

			$('.sip-comment-form-<?php echo $id ?> .submit').unbind('click').click(function(e){
				
				var id = $(this).data('id');
				if (<?php echo $current_user->ID ?> == 0) {
					var name = $('#author-'+id ).val(); //$current_user->display_name
					var email = $('#email-' + id ).val(); //$current_user->user_email
				} else {
					var name = 0; //$current_user->display_name
					var email = 0; //$current_user->user_email
				}

				var comment = $('#comment-' + id ).val();

				var emailReg = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
				$(document).unbind('.submit');

				if (<?php echo $current_user->ID ?> == 0) {
					if(name == ""){alert("please fill the required field (name)");return false;}
					if(email == ""){alert("please fill the required field (email)");return false;}
					if( !emailReg.test( email ) ) { return false; }
				}
				if(rating == ""){alert("Please select a rating");return false;}
				if(comment == ""){alert("Please type a comment");return false;}

				// $('.sip-comment-form-'+id).slideUp();
				$('.sip-comment-form-<?php echo $id ?>').html('<div class="sip-success"><p align="center"><img src="<?php echo SIP_RSWC_URL; ?>public/img/ajax-loader.gif" ></p></div>');

				e.preventDefault();

				var data = {
					'action': 'submit_comment',
					'security' : '<?php $ajax_nonce = wp_create_nonce( "sip-rswc-form-submit" ); echo $ajax_nonce; ?>',
					'id': id,
					'name' : name,
					'email' : email,
					'comment' : comment,
					'rating' : rating
				};

				$.post( sip_rswc_ajax.ajax_url, data ).done(function( html ) {

					$('.sip-comment-form-'+id).slideUp();
					$('.sip-comment-'+id+'-success').slideDown();
				});
			});
		});
	</script>
	<?php
		$display_form = "";
		$current_user = wp_get_current_user();
		
		$comment_approved = __('Thank you for your submission. Your review is awaiting moderation and will be visible after approval.', 'sip-reviews-shortcode');
		$options = get_option('sip-rswc-settings-radio');
		if( '1' == $options['option_aproved'] ) { 
			$comment_approved = __('Thank you! Your review was successfully posted.', 'sip-reviews-shortcode');
		}
		$display_form .=  '<div class="woocommerce sip-rswc-form sip-comment-form-'. $id .'">
							<div id="review_form_wrapper-'. $id .'" class="review_form_wrapper">
								<div id="review_form-'. $id .'"  class="review_form">
									<div class="comment-respond sip-respond" id="sip-respond-'. $id .'">
										<form class="comment-form" id="commentform-'. $id .'" method="post" action="#">';

											if ( !is_user_logged_in() ) {
							$display_form .=  '<p class="comment-form-author">
													<label for="author-'. $id .'">'. __('Name' , 'sip-reviews-shortcode') .'<span class="required"> *</span></label> 
													<input type="text" aria-required="true" size="30" value="" name="author" id="author-'. $id .'">
												</p>
												<p class="comment-form-email">
													<label for="email-'. $id .'">'.__('Email' , 'sip-reviews-shortcode' ).'<span class="required"> *</span></label> 
													<input type="email" aria-required="true" size="30" value="" name="email" id="email-'. $id .'">
												</p>';
											}

							$display_form .= '<div class="comment-form-rating">
												<label for="rating-'. $id .'">'.__('Your Rating' , 'sip-reviews-shortcode').'</label>
												<p class="stars">
													<span>
														<a href="#" class="star-1">'.__('1', 'sip-reviews-shortcode').'</a>
														<a href="#" class="star-2">'.__('2', 'sip-reviews-shortcode').'</a>
														<a href="#" class="star-3">'.__('3', 'sip-reviews-shortcode').'</a>
														<a href="#" class="star-4">'.__('4', 'sip-reviews-shortcode').'</a>
														<a href="#" class="star-5">'.__('5', 'sip-reviews-shortcode').'</a>
													</span>
												</p>
												<select id="rating-'. $id .'" name="rating" style="display: none;">
													<option value="">'.__('Rate…', 'sip-reviews-shortcode').'</option>
													<option value="5">'.__('Perfect', 'sip-reviews-shortcode').'</option>
													<option value="4">'.__('Good', 'sip-reviews-shortcode').'</option>
													<option value="3">'.__('Average', 'sip-reviews-shortcode').'</option>
													<option value="2">'.__('Not that bad', 'sip-reviews-shortcode').'</option>
													<option value="1">'.__('Very Poor', 'sip-reviews-shortcode').'</option>
												</select>
											</div>
											<p class="comment-form-comment">
												<label for="comment-'. $id .'">'.__('Your Review' , 'sip-reviews-shortcode').'</label>
												<textarea aria-required="true" rows="8" cols="45" name="comment" id="comment-'. $id .'"></textarea>
											</p>						
											<p class="form-submit">
												<input type="submit" value="'.__('Submit' , 'sip-reviews-shortcode').'" class="submit" id="submit-'. $id .'" data-id="'. $id .'" name="submit">
												<input type="hidden" id="comment_post_ID-'. $id .'" value="'. $id .'" name="comment_post_ID">
												<input type="hidden" value="0" id="comment_parent-'. $id .'" name="comment_parent">
											</p>
										</form>
									</div><!-- #respond -->
								</div>
							</div>
						</div>';
	$display_form .= '<div class="sip-comment-'. $id .'-success sip-success" style="display:none;"><strong>'.__( $comment_approved , 'sip-reviews-shortcode').'</strong></div>';

	return $display_form;
}
add_shortcode( 'woocommerce_review_form', 'submit_woocommerce_review_form' );

// returns true if theme is Salient or Salient Child Theme
function sip_rswc_contains( $sub_string, $super_string ) {

	$super_string = strtolower( $super_string );
	$sub_string	= strtolower( $sub_string );

	return strpos($super_string, $sub_string) !== false;
}

add_action( 'wp_head', function() {

	global $post;
	if( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'woocommerce_review_form') ) {

		$theme_name = wp_get_theme();
		$salient_name = sip_rswc_contains( "salient", $theme_name->get('Name') );
		$options = get_option( 'color_options' );
		$star_color = ( isset( $options['star_color'] ) ) ? sanitize_text_field( $options['star_color'] ) : '#d1c51d';
		$bar_color 	= ( isset( $options['bar_color'] ) ) ? sanitize_text_field( $options['bar_color'] ) : '#AD74A2';
		$icon = ( (get_option('sip-rswc-setting-rated-icon') ) ? get_option('sip-rswc-setting-rated-icon') : "star" );

		$SIP_Reviews_Shortcode_Public = new SIP_Reviews_Shortcode_Public( SIP_RSWC_NAME, SIP_RSWC_VERSION );

		$icon = $SIP_Reviews_Shortcode_Public->sip_rswc_font_awesome( $icon );

		?>
		<?php if ( isset( $options['star_color'] ) || $salient_name ) { ?>
			<style>
				<?php if ( isset( $options['star_color'] ) ) { ?>

					.woocommerce p.stars.selected a.active::before,
					.woocommerce p.stars:hover a::before,
					.woocommerce p.stars a::before,
					.woocommerce p.stars a:hover ~ a::before,
					.woocommerce p.stars.selected a:not(.active)::before,
					.woocommerce p.stars.selected a.active ~ a::before {
						content: "<?php echo $icon; ?>"
					}

					.woocommerce p.stars.selected a:not(.active)::before,
					.woocommerce p.stars.selected a.active::before,
					.woocommerce p.stars a.star-1:hover:before,
					.woocommerce p.stars a.star-2:hover:before,
					.woocommerce p.stars a.star-3:hover:before,
					.woocommerce p.stars a.star-4:hover:before,
					.woocommerce p.stars a.star-5:hover:before {
						color: <?php echo $star_color; ?>;
					}

				<?php } ?>

				<?php if ( $salient_name ) { ?>
					.woocommerce.sip-rswc-form p.stars a.star-1, .woocommerce-page p.stars a {
						width: 1em;
					}
				<?php } ?>

			</style>
			<?php 
		}
	}
}, 10 );