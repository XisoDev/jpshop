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

class Sip_Reviews_Shortcode_Woocommerce_Widget extends WP_Widget {

	public function __construct() {

		parent::__construct(
			'sip_reviews_shortcode_widget',
			__( 'SIP reviews for WooCommerce', 'sip-reviews-shortcode' ),
			array(
				'classname'   => 'sip_reviews_shortcode_widget',
				'description' => __( 'Display product reviews in any post/page with a widget..', 'sip-reviews-shortcode' )
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
		
		echo $before_widget;

		if ( $title ) {
			echo $before_title . $title . $after_title;
		}
		 
	  	// if number of review not mention in shor coode then defaul value will be assign
		if( $no_of_reviews == "" ) {
			$no_of_reviews = 5;
		}

		$product_title 	= get_the_title( $id );
		$options 	= get_option( 'color_options' );
	  	$star_color = ( isset( $options['star_color'] ) ) ? sanitize_text_field( $options['star_color'] ) : '#d1c51d';
	  	$bar_color 	= ( isset( $options['bar_color'] ) ) ? sanitize_text_field( $options['bar_color'] ) : '#AD74A2';

	  	if( $star_color != "")
	  		$star_color = "style='color:". $star_color .";'";

	  	if( $bar_color != "")
	  		$bar_color = "background-color:".$bar_color .";";

		// To check that post id is product or not
		if( get_post_type( $id ) == 'product' ) {
			wp_enqueue_style( 'sip-rswc-jquery-ui-css' );
			wp_enqueue_script( 'sip-rswc-jquery-ui' );
			// to get the detail of the comments etc aproved and panding status
			$comments_count = wp_count_comments( $id );
			?>
			<?php $get_avg_rating = sip_get_avg_rating( $id ); ?>
			<?php $get_review_count = sip_get_review_count( $id ); ?>
			<?php $get_price = sip_get_price($id); ?>

		<!--Wrapper: Start -->
		<div class="sip-rswc-wrapper"> 
		  	<!--Main Container: Start -->
		  	<div class="main-container">
		    	<aside class="page-wrap" itemscope itemtype="http://schema.org/Product" id="product-<?php echo $id; ?>">
		      		<div class="share-wrap">
						<?php $image = ""; ?>
						<?php if (has_post_thumbnail( $id ) ): ?>
							<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'single-post-thumbnail' ); ?>
						<?php endif; ?>
						
						<!-- it is not for display it is only to generate schema for goolge search result -->
						<div style="display:none;">
							<?php if (isset( $image[0] )) { ?>
								<a href="<?php echo $image[0]; ?>" itemprop="image"><?php echo $product_title; ?></a>
							<?php } ?>							
							<span itemprop="name"><?php echo $product_title; ?></span>
							<meta itemprop="url" content="<?php echo get_permalink( $id ); ?>">
							<div class="star_container" itemprop="aggregateRating" itemscope="" itemtype="http://schema.org/AggregateRating">
								<span itemprop="ratingValue"><?php echo $get_avg_rating; ?></span>
								<span itemprop="bestRating">5</span>
								<span itemprop="ratingCount"><?php echo $get_review_count ?></span>
								<span itemprop="reviewcount" style="display:none;"><?php echo $get_review_count ?></span>
							</div>
							<div itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
								<span itemprop="priceCurrency" content="<?php $currency = get_woocommerce_currency(); echo $currency; ?>"><?php echo get_woocommerce_currency_symbol($currency) ?></span>
								<span itemprop="price" content="<?php echo $get_price; ?>"><?php echo get_woocommerce_currency_symbol(); echo $get_price; ?></span>
								<link itemprop="availability" href="http://schema.org/InStock">
							</div>
							<?php
								$content_post = get_post( $id );
								$content = $content_post->post_content;
								$product_ = wc_get_product( $id );
	  							$sku_ = $product_->get_sku();
							?>
							<span style="display:none;" itemprop="sku"><?php echo $sku_; ?></span>
							<span itemprop="description"><?php echo $content ?></span>
						</div><!-- end itemscope -->

		        		<div class="share-left">
		          			<div class="big-text"><?php echo $get_avg_rating; ?> <?php _e('out of 5 stars' , 'sip-reviews-shortcode');?></div>
		          			<div class="sm-text"><?php echo $get_review_count ?> 
			          			<span class="review-icon-image"><?php _e('reviews' , 'sip-reviews-shortcode'); ?>		
									<?php if(get_option('sip-rswc-affiliate-check-box') == "true") { ?>
										<?php $options = get_option('sip-rswc-affiliate-radio'); ?>
										<?php if( 'value1' == $options['option_three'] ) { $url = "https://shopitpress.com/?utm_source=referral&utm_medium=credit&utm_campaign=sip-reviews-shortcode-woocommerce" ; } ?>
										<?php if( 'value2' == $options['option_three'] ) { $url = "https://shopitpress.com/?offer=". esc_attr( get_option('sip-rswc-affiliate-affiliate-username')) ; } ?>
										<a class="sip-rswc-credit" href="<?php echo $url ; ?>" target="_blank" data-tooltip="<?php _e('These reviews were created with SIP Reviews Shortcode Plugin' , 'sip-reviews-shortcode'); ?>"></a>
									<?php } ?>
								</span>
							</div>
		        		</div><!-- .share-left -->

		        		<div class="share-right">
		          			<div class="product-rating-details">
		          				<table>
			            			<tbody>
										<?php $get_rating_count = sip_get_rating_count( $id ); ?>
										<?php for ( $i = 5; $i > 0 ; $i-- ) {
											if ( !isset( $get_rating_count[$i] ) ) {
												$get_rating_count[$i] = 0;
											}

											$percentage = 0 ;
											if ( $get_rating_count[$i] > 0 ) {
												$percentage = ($get_rating_count[$i] / $get_review_count) * 100;
											}
											$url = get_permalink();

											$rated_icons = ( (get_option('sip-rswc-setting-rated-icon') ) ? get_option('sip-rswc-setting-rated-icon') : "star" );
											?>
											<tr>
												<td class="rating-number sip-stars-rating" data-number="<?php echo $i; ?>">
													<a href="javascript:void(0);" <?php echo $star_color; ?>><?php echo $i; ?> <span class="fa fa-<?php echo $rated_icons; ?>"></span></a>
												</td>

												<td class="rating-graph sip-stars-rating" data-number="<?php echo $i; ?>">
													<a style="float:left; <?php echo $bar_color; ?> width: <?php echo $percentage; ?>%" class="bar" href="javascript:void(0);" title="<?php printf( '%s%%', $percentage ); ?>"></a>
												</td>

												<td class="rating-count sip-stars-rating" data-number="<?php echo $i; ?>">
													<a href="javascript:void(0);" <?php echo $star_color; ?>><?php echo $get_rating_count[$i]; ?></a>
												</td>

												<td class="rating-count sip-stars-rating" data-number="<?php echo $i; ?>">
													<a href="<?php echo $url; ?>#comments" <?php echo $star_color; ?>></a>
												</td>
											</tr>
										<?php } ?>            
		            				</tbody>
	          					</table>
	          				</div>
	        			</div><!-- .share-right -->
      				</div>
					<!--Tabs: Start -->
					<aside class="tabs-wrap">
						<div class="page-wrap">
							<div class="tabs-content">
							<?php $style = 1; ?>							
							<?php woocommerce_print_reviews( $id , $product_title , $no_of_reviews , $style , $product_name , $avatar ); ?> 
								
							</div>
						</div>
					</aside><!-- .tabs-wrap -->
					<!--Tabs: Start -->				
	    		</aside>
	  		</div><!--Main Container: End --> 
		</div><!--Wrapper: End --> 			
		<div style="clear:both"></div>
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

		$product_name = false;
		if (isset($instance['product_name'])) {
			$product_name = esc_attr( $instance['product_name'] );
		}

		$no_of_reviews = 5;
		if (isset($instance['no_of_reviews'])) {
			$no_of_reviews = esc_attr( $instance['no_of_reviews'] ) ? esc_attr( $instance['no_of_reviews'] ) : 5 ;
		}

		$products_id = array(
			'post_type' => 'product',
			'posts_per_page' => -1
		);
		?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'sip-reviews-shortcode' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('id'); ?>"><?php _e('ID:', 'sip-reviews-shortcode' ); ?></label>
			<?php 
				$res='';
				foreach(get_posts($products_id) as $val){
					$selected = "";
					if ( $id == $val->ID ) {
						$selected = 'selected ="selected"';
					}

					$res .= '<option value="'.$val->ID.'" '.$selected.'>'.$val->post_title.'</options>';
				}
				echo '<select id="'.$this->get_field_id('id').'" name="'.$this->get_field_name('id').'" class="sip-value" style="width: 100%;">'.$res.'</select>';
			?>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('no_of_reviews'); ?>"><?php _e('No of reviews:', 'sip-reviews-shortcode' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id('no_of_reviews'); ?>" name="<?php echo $this->get_field_name('no_of_reviews'); ?>" type="number" value="<?php echo $no_of_reviews; ?>" />
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php  checked( $avatar, 'on' ); ?> id="<?php echo $this->get_field_id( 'avatar' ); ?>" name="<?php echo $this->get_field_name( 'avatar' ); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'avatar' ); ?>"><?php _e('Allow avatar', 'sip-reviews-shortcode' );?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $product_name, 'on' ); ?> id="<?php echo $this->get_field_id( 'product_name' ); ?>" name="<?php echo $this->get_field_name( 'product_name' ); ?>" /> 
			<label for="<?php echo $this->get_field_id( 'product_name' ); ?>"><?php _e('Show Product Name', 'sip-reviews-shortcode' ); ?></label>
		</p>
		<?php 
	}

}

/* Register the widget */
add_action( 'widgets_init', function(){
	register_widget( 'Sip_Reviews_Shortcode_Woocommerce_Widget' );
});


add_action( 'wp_head', function() {

    if ( empty ( $GLOBALS['wp_widget_factory'] ) )
        return;

    $widgets = $GLOBALS['wp_widget_factory']->widgets;


	foreach ($widgets as $key => $widget) {
		if ( is_active_widget( false, false, $widget->id_base, true ) && ( $widget->id_base == 'sip_reviews_shortcode_widget' || $widget->id_base == 'sip_reviews_shortcode_widget_aggregate') ) {

			$options 	= get_option( 'color_options' );
			$star_color = ( isset( $options['star_color'] ) ) ? sanitize_text_field( $options['star_color'] ) : '#d1c51d';
			?>
				<style>
					.star-rating:before,
					.woocommerce-page .star-rating:before,
					.star-rating span:before,
					.br-theme-fontawesome-stars .br-widget a.br-selected:after,
					.sip-score-star span,
					.col-style-4 .sip-score-star span {
						color: <?php echo $star_color; ?>;
					}
				</style>
			<?php
		}
	}

}, 10 );