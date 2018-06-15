<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       http://shopitpress.com
 * @since      1.1.3
 *
 * @package    Sip_Reviews_Shortcode_Woocommerce
 * @subpackage Sip_Reviews_Shortcode_Woocommerce/public/partials
 */

class Sip_Reviews_Shortcode_Woocommerce_Widget_Aggregate extends WP_Widget {

	public function __construct() {

		parent::__construct(
			'sip_reviews_shortcode_widget_aggregate',
			__( 'SIP Aggregate reviews for WooCommerce', 'sip-reviews-shortcode' ),
			array(
				'classname'   => 'sip_reviews_shortcode_widget_aggregate',
				'description' => __( 'Display products aggregate reviews in any post/page with a widget..', 'sip-reviews-shortcode' )
			)
		);
	}

	/**  
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		
		global $post, $wpdb, $product;
		$product_title = "";
		$style = 1;
		
		extract( $args );

		if (isset($instance['title'])) {
			$title = apply_filters( 'widget_title', $instance['title'] );
		} else {
			$title = "";
		}

		if (isset($instance['avatar'])) {
			$avatar = $instance['avatar'] ? true : false;
		} else {
			$avatar = false;
		}

		if (isset($instance['product_name'])) {
			$product_name = $instance['product_name'] ? true : false;	
		} else {
			$product_name = false;
		}

		if (isset($instance['id'])) {
			$id = $instance['id'];
		} else {
			return;
		}

		if (isset($instance['no_of_reviews'])) {
			$no_of_reviews = $instance['no_of_reviews'];
		} else {
			$no_of_reviews = 5;
		}

		if (isset($instance['radio_buttons'])) {
			$radio_buttons = $instance['radio_buttons'];
		} else {
			return;
		}
		
		echo $before_widget;

		if ( $title ) {
			echo $before_title . $title . $after_title;
		}

  		if( $radio_buttons == "category" ) {

			$id 	= str_replace(" ", "", $id);
			$cats 	= explode(",", strval($id));
			$id 	= "";
			$loop 	= 0;

			foreach ($cats as $cat) {
				$sql = $wpdb->prepare("SELECT tr.object_id  FROM {$wpdb->prefix}term_relationships tr	WHERE tr.term_taxonomy_id=%d", $cat);
				$results = $wpdb->get_results( $sql );
				foreach ($results as $key => $value) {
					if( $key != 0 || $loop!= 0 ){
						$id .= ",";
					}
					$id .= $value->object_id ;
				}
				$loop++;
			}
		}

		if ( $id == "" || $id == 0 ) {
			return;
		}

		$id  = str_replace( " ", "", $id );
		$ids = explode( ",", strval( $id ) );

		$loop = 0;
		$id_ = "";
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

		$id  = str_replace( " ", "", $id_ );
		$ids = explode( ",", strval( $id ) );

	  	// if number of review not mention in shor coode then defaul value will be assign

		if( $no_of_reviews == "" ) {
			$no_of_reviews = 5;
		}

  		// if product title is not mention by user in shortcode then get default value

		if( $product_title == "" ) {
			foreach ( $ids as $id ) {
				$product_title 	= get_the_title( $id );
			}
		}

		$options 	= get_option( 'color_options' );
	  	$star_color = ( isset( $options['star_color'] ) ) ? sanitize_text_field( $options['star_color'] ) : '#d1c51d';
	  	$bar_color 	= ( isset( $options['bar_color'] ) ) ? sanitize_text_field( $options['bar_color'] ) : '#AD74A2';


	  	if( $star_color != "")
	  		$star_color = "style='color:". $star_color .";'";

	  	if( $bar_color != "")
	  		$bar_color = "background-color:".$bar_color .";";


		// To check that post id is product or not
		if( get_post_type( $ids[0] ) == 'product' ) {

			wp_enqueue_style( 'sip-rswc-jquery-ui-css' );
			wp_enqueue_script( 'sip-rswc-jquery-ui' );

			// to get the detail of the comments etc aproved and panding status
			$comments_count = wp_count_comments( $ids[0] );
			?>

			<!--Wrapper: Start -->
			<div class="sip-rswc-wrapper"> 
			  	<!--Main Container: Start -->
			  	<div class="main-container">
			    	<aside class="page-wrap" itemscope itemtype="http://schema.org/Product" id="product-<?php echo $ids[0]; ?>">
					    <div class="share-wrap">

							<?php 

								$comments_approved_sum = 0;
								$ids = array_unique($ids);

								foreach ($ids as $id) {
									$comments_approved 	= sip_get_review_count( $id );
									$comments_approved_sum = $comments_approved_sum + $comments_approved ;
								}

								$get_avg_rating = 0;
								$get_avg_rating_count = 0;
								foreach ($ids as $key => $id) {
									$get_avg_rating = $get_avg_rating + sip_get_avg_rating( $id );
									$get_avg_rating_count = $key;
								}
								$get_avg_rating_count++;
								$get_avg_rating = $get_avg_rating / $get_avg_rating_count;
							?>

							<!-- it is not for display it is only to generate schema for goolge search result -->

							<div style="display:none;">
								<?php if (has_post_thumbnail( $ids[0] ) ) { ?>
										<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $ids[0] ), 'single-post-thumbnail' ); ?>
										<a href="<?php echo $image[0]; ?>" itemprop="image"><?php echo $product_title; ?></a>
										<meta itemprop="url" content="<?php echo get_permalink( $ids[0] ); ?>">
								<?php } //end if ?> 

								<span itemprop="name"><?php echo $product_title; ?></span>
								<div class="star_container" itemprop="aggregateRating" itemscope="" itemtype="http://schema.org/AggregateRating">
									<span itemprop="ratingValue"><?php echo $get_avg_rating; ?></span>
									<span itemprop="bestRating">5</span>
									<span itemprop="ratingCount"><?php echo $comments_approved_sum; ?></span>
									<span itemprop="reviewcount"><?php echo $comments_approved_sum; ?></span>
								</div>

								<div itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
									<span itemprop="priceCurrency" content="<?php $currency = get_woocommerce_currency(); echo $currency; ?>"><?php echo get_woocommerce_currency_symbol($currency) ?></span>
									<span itemprop="price" content="<?php $get_price = get_post_meta( $id , '_price' ); echo $get_price[0]; ?>"><?php echo get_woocommerce_currency_symbol(); echo $get_price[0]; ?></span>
									<link itemprop="availability" href="http://schema.org/InStock">
								</div>

								<?php
									$product_ = wc_get_product( $ids[0] );
		  							$sku_ = $product_->get_sku();
								?>
								<span style="display:none;" itemprop="sku"><?php echo $sku_; ?></span>
							</div><!-- end itemscope -->
							<div class="share-left-right">
								<div class="share-left">
									<div class="big-text"><?php echo $get_avg_rating; ?> <?php _e('out of 5 stars' , 'sip-reviews-shortcode'); ?></div>
									<div class="sm-text"><?php echo $comments_approved_sum ?> 
										<span class="review-icon-image"><?php _e('reviews' , 'sip-reviews-shortcode') ?>
											<?php if(get_option('sip-rswc-affiliate-check-box') == "true") { ?>
												<?php $options = get_option('sip-rswc-affiliate-radio'); ?>
												<?php if( 'value1' == $options['option_three'] ) { $url = "https://shopitpress.com/?utm_source=referral&utm_medium=credit&utm_campaign=sip-reviews-shortcode-woocommerce" ; } ?>
												<?php if( 'value2' == $options['option_three'] ) { $url = "https://shopitpress.com/?offer=". esc_attr( get_option('sip-rswc-affiliate-affiliate-username')) ; } ?>
												<a class="sip-rswc-credit" href="<?php echo $url ; ?>" target="_blank" data-tooltip="<?php _e('These reviews were created with SIP Reviews Shortcode Plugin' , 'sip-reviews-shortcode'); ?>"></a>
											<?php } ?>
										</span>
									</div><!-- .sm-text -->
								</div>

								<div class="share-right">
									<div class="product-rating-details">
										<table>
											<tbody>
												<?php

												$get_aggregated_rating_count = array( 1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0 );
												foreach ( $ids as $key => $id ) {
												
													$get_rating_count = sip_get_rating_count( $id );
													foreach ($get_rating_count as $key => $value) {
														$get_aggregated_rating_count[$key] = $get_aggregated_rating_count[$key] + $value;
													}
												}

												for ( $i = 5; $i > 0 ; $i-- ) {
													if ( !isset( $get_aggregated_rating_count[$i] ) ) {
														$get_aggregated_rating_count[$i] = 0;
													}

													$percentage = 0 ;
													if ( $get_aggregated_rating_count[$i] > 0 ) {
														$percentage = ($get_aggregated_rating_count[$i] / $comments_approved_sum) * 100;
													}
													$url = get_permalink();

													$rated_icons = ( (get_option('sip-rswc-setting-rated-icon') ) ? get_option('sip-rswc-setting-rated-icon') : "star" );
													?>
													<tr>
														<td class="rating-number">
															<a href="javascript:void(0);" <?php echo $star_color; ?>><?php echo $i; ?> <span class="fa fa-<?php echo $rated_icons; ?>"></span></a>
														</td>
														<td class="rating-graph">
															<a style="float:left; <?php echo $bar_color; ?> width: <?php echo $percentage; ?>%" class="bar" href="javascript:void(0);" title="<?php printf( '%s%%', $percentage ); ?>"></a>
														</td>
														<td class="rating-count">
															<a href="javascript:void(0);" <?php echo $star_color; ?>><?php echo $get_aggregated_rating_count[$i]; ?></a>
														</td>
														<td class="rating-count">
															<a href="<?php echo $url; ?>#comments" <?php echo $star_color; ?>></a>
														</td>
													</tr>
												<?php } ?>
											</tbody>
										</table>
									</div>
								</div><!-- .share-right -->
							</div><!-- .share-left-right -->
						</div>

						<!--Tabs: Start -->
						<aside class="tabs-wrap">
							<div class="page-wrap">
								<div class="tabs-content">
								<?php woocommerce_print_aggregate_reviews( $ids , $product_title , $no_of_reviews, $style, $product_name, $avatar ); ?>
								</div>
							</div>
						</aside>

						<!--Tabs: Start -->
					</aside>
				</div><!--Main Container: End -->
			</div><!-- .sip-rswc-wrapper -->

			<?php sort($ids); ?>
			<?php $colleted_id = 0; ?>
			<?php foreach ($ids as $id) {
				$colleted_id .= $id;
			} ?>

			<?php $colleted_id = substr($colleted_id, 0, 20); ?>
			<!--Wrapper: End -->
			<div style="clear:both"></div>
			<script>
				jQuery(document).ready(function () {
					size_li_<?php echo $colleted_id; ?> = jQuery("#comments_list_<?php echo $colleted_id; ?> li").size();
					x_<?php echo $colleted_id; ?> = <?php echo $no_of_reviews; ?>;
					jQuery('#comments_list_<?php echo $colleted_id; ?> li:lt('+x_<?php echo $colleted_id; ?>+')').show();
					if( size_li_<?php echo $colleted_id; ?> <= x_<?php echo $colleted_id; ?> ){
						jQuery('#sip-rswc-more-<?php echo $colleted_id; ?>').hide();
					}
					jQuery('#sip-rswc-more-<?php echo $colleted_id; ?>').click(function () {
						x_<?php echo $colleted_id; ?> = ( x_<?php echo $colleted_id; ?> + <?php echo $no_of_reviews; ?> <= size_li_<?php echo $colleted_id; ?> ) ? x_<?php echo $colleted_id; ?> + <?php echo $no_of_reviews; ?> : size_li_<?php echo $colleted_id; ?> ;
						jQuery('#comments_list_<?php echo $colleted_id; ?> li:lt('+ x_<?php echo $colleted_id; ?> +')').show();
						if( x_<?php echo $colleted_id; ?> == size_li_<?php echo $colleted_id; ?> ){
							jQuery('#sip-rswc-more-<?php echo $colleted_id; ?>').hide();
						}
					});
				});
			</script>
			<?php
			}// end of post id is product or not

		echo $after_widget;
	}


	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {        

		$instance = $old_instance;
		$instance['radio_buttons'] = ( isset($new_instance['radio_buttons']) ? strip_tags( $new_instance['radio_buttons'] ) : "" );
		$instance['title'] = ( isset($new_instance['title']) ? strip_tags( $new_instance['title'] ) : "" );
		$instance['avatar'] = ( isset($new_instance['avatar']) ? strip_tags( $new_instance['avatar'] ) : "" );
		$instance['id'] = ( isset($new_instance['id']) ? strip_tags( $new_instance['id'] ) : "" );
		$instance['product_name'] = ( isset($new_instance['product_name']) ? strip_tags( $new_instance['product_name'] ) : "" );
		$instance['no_of_reviews'] = ( isset($new_instance['no_of_reviews']) ? strip_tags( $new_instance['no_of_reviews'] ) : "" );

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {    

		$id = "";
		if (isset($instance['id'])) {
			$id = esc_attr( $instance['id'] );
		}

		$title = "";
		if (isset($instance['title'])) {
			$title = esc_attr( $instance['title'] );
		}

		$avatar = false;
		if (isset($instance['avatar'])) {
			$avatar = esc_attr( $instance['avatar'] );
		}

		$product_name = "";
		if (isset($instance['product_name'])) {
			$product_name = esc_attr( $instance['product_name'] );
		}

		$no_of_reviews = 5;
		if (isset($instance['no_of_reviews'])) {
			$no_of_reviews = esc_attr( $instance['no_of_reviews'] ) ? esc_attr( $instance['no_of_reviews'] ) : 5 ;
		}

		$radio_buttons = false;
		if (isset($instance['radio_buttons'])) {
			$radio_buttons = esc_attr( $instance['radio_buttons'] );
		}
		?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'sip-reviews-shortcode' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
		    <label for="<?php echo $this->get_field_id('text_area'); ?>">
		        <?php _e('Select the type', 'sip-reviews-shortcode' ); ?>
		    </label><br>
		    <label for="<?php echo $this->get_field_id('multiple_ids'); ?>">
		        <?php _e('Multiple Ids:', 'sip-reviews-shortcode' ); ?>
		        <input class="" id="<?php echo $this->get_field_id('multiple_ids'); ?>" name="<?php echo $this->get_field_name('radio_buttons'); ?>" type="radio" value="multiple_ids" <?php if($radio_buttons === 'multiple_ids'){ echo 'checked="checked"'; } ?> />
		    </label><br>
		    <label for="<?php echo $this->get_field_id('category'); ?>">
		        <?php _e('Category:', 'sip-reviews-shortcode' ); ?>
		        <input class="" id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('radio_buttons'); ?>" type="radio" value="category" <?php if($radio_buttons === 'category'){ echo 'checked="checked"'; } ?> />
		    </label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('id'); ?>"><?php _e('ID:', 'sip-reviews-shortcode' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('id'); ?>" name="<?php echo $this->get_field_name('id'); ?>" type="text" value="<?php echo $id; ?>" placeholder="12,45,78" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('no_of_reviews'); ?>"><?php _e('No of reviews:', 'sip-reviews-shortcode' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id('no_of_reviews'); ?>" name="<?php echo $this->get_field_name('no_of_reviews'); ?>" type="number" value="<?php echo $no_of_reviews; ?>" />
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $avatar, 'on' ); ?> id="<?php echo $this->get_field_id( 'avatar' ); ?>" name="<?php echo $this->get_field_name( 'avatar' ); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'avatar' ); ?>"><?php _e('Allow avatar', 'sip-reviews-shortcode' ); ?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $product_name, 'on' ); ?> id="<?php echo $this->get_field_id( 'product_name' ); ?>" name="<?php echo $this->get_field_name( 'product_name' ); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'product_name' ); ?>"><?php _e('Show Product Name', 'sip-reviews-shortcode' );?></label>
		</p>
		<?php 
	}

}

/* Register the widget */
add_action( 'widgets_init', function(){
	register_widget( 'Sip_Reviews_Shortcode_Woocommerce_Widget_Aggregate' );
});