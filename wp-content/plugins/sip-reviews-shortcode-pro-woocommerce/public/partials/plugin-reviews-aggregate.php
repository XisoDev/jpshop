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
	add_shortcode ('woocommerce_reviews_aggregate', 'sip_get_reviews_aggregate' );
	
	function sip_get_reviews_aggregate( $atts ) {
	
		global $post, $wpdb, $product;
		extract( shortcode_atts(
			array(
				'id' 			=> '',
				'cat'			=> '',
				'style'			=> '',
				'icon'			=> '',
				'no_of_reviews' => '',
				'product_title' => '',
				'product_name' 	=> '',
				'filter' 		=> 'no',
				'thumbs' 		=> '',
				'sku'			=> '',
				'start_date' 	=> '0',
				'end_date'		=> '0',
			), $atts )
		);

		wp_enqueue_style( 'sip-rswc-jquery-ui-css' );
		wp_enqueue_script( 'sip-rswc-jquery-ui' );

		$id = strtolower($id);
		$cat = strtolower($cat);
		$thumbs = strtolower( $thumbs );

		if( $cat != 0 && $cat != "" && $cat != "all" ) {

			$cat 	= str_replace(" ", "", $cat);
			$cats 	= explode(",", strval($cat));

			$id 	= "";
			$loop 	= 0;

			foreach ( $cats as $cat ) {

				$args = array(
					'post_type'				=> 'product',
					'post_status'			=> 'publish',
					'ignore_sticky_posts'	=> 1,
					'posts_per_page'		=> '-1',
					'meta_query'			=> array(
						array(
							'key'			=> '_visibility',
							'value'			=> array('catalog', 'visible'),
							'compare'		=> 'IN'
						)
					),
					'tax_query'	=> array(
						array(
							'taxonomy'	=> 'product_cat',
							'field'		=> 'term_id', //This is optional, as it defaults to 'term_id'
							'terms'		=> $cat,
							'operator'	=> 'IN' // Possible values are 'IN', 'NOT IN', 'AND'.
						)
					)
				);

				$products = new WP_Query( $args );
				foreach ($products->posts as $key => $value) {
					if( $value->ID != 0 && $loop != 0 ) {
						$id .= ",". $value->ID;
					} else {
						$loop++;
						$id .= $value->ID;
					}
				}
			}
		}

		if ( $id == "" || $id == 0 ) {
			if ( isset( $product->id ) ) {
				
				$id = $product->id;
			}
		}

		$loop = 0;
		$id_ = "";

		if ( $sku != "" ) {

			$sku = str_replace( " ", "", $sku );
			$sku = explode( ",", strval( $sku ) );

			foreach ( $sku as $val ) {
				$id = wc_get_product_id_by_sku( $val );
				$comments_count = wp_count_comments( $id );
				if ( $comments_count->approved ) {

					if( $loop!= 0 ) {
						$id_ .= ",";
					}

					$id_ .= $id ;
					$loop++;
				}
			}
			$id = $id_;
		}

		$loop = 0;
		$id_ = "";

		if ( $id == "all" || $cat == "all" ) {
			$ids = $wpdb->get_results( "SELECT ID FROM $wpdb->posts WHERE post_type = 'product' AND post_status = 'publish' AND comment_count > 0 ", ARRAY_A );

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
		} else {

			$id  = str_replace( " ", "", $id );
			$ids = explode( ",", strval( $id ) );

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
		}

		$id  = str_replace( " ", "", $id_ );
		$ids = explode( ",", strval( $id ) );

		// if number of review not mention in shor coode then defaul value will be assign

		if( $no_of_reviews == "" ) {
			$no_of_reviews = 5;
		}

		// if product title is not mention by user in shortcode then get default value

		if ( empty( $ids[0] ) || empty( $ids ) ) {
			ob_start();
			return ob_get_clean();
		}

		if( $product_title == "" ) {
			foreach ( $ids as $id ) {
				$product_title 	= get_the_title( $id );
			}
		}

		$options = get_option( 'color_options' );
		$star_color = ( isset( $options['star_color'] ) ) ? sanitize_text_field( $options['star_color'] ) : '#d1c51d';
		$bar_color = ( isset( $options['bar_color'] ) ) ? sanitize_text_field( $options['bar_color'] ) : '#AD74A2';

		if( $star_color != "" ) {
			$star_color = "style='color:". $star_color .";'";
		}

		if( $bar_color != "" ) {
			$bar_color = "background-color:".$bar_color .";";
		}

		ob_start();

		// To check that post id is product or not
		if( get_post_type( $ids[0] ) == 'product' ) {

			// to get the detail of the comments etc aproved and panding status
			$comments_count = wp_count_comments( $ids[0] );
			?>
			<!--Wrapper: Start -->
			<div class="sip-rswc-wrapper <?php echo $icon; ?>"> 
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
									<span itemprop="ratingValue"><?php
									$total_avg_rating = 0;
									$total_key = 0;
									foreach ($ids as $key => $id) {
										$total_avg_rating = $total_avg_rating + sip_get_avg_rating( $id );
										$total_key = $key;
									}
									$total_key++;
									$total_avg_rating = number_format( $total_avg_rating / $total_key, 2, '.', '' );
									echo $total_avg_rating;
								 ?></span>
									<span itemprop="bestRating">5</span>
									<span itemprop="ratingCount"><?php echo $comments_approved_sum ?></span>
									<span itemprop="reviewcount"><?php echo $comments_approved_sum ?></span>
								</div>

								<div itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
									<span itemprop="priceCurrency" content="<?php $currency = get_woocommerce_currency(); echo $currency; ?>"><?php echo get_woocommerce_currency_symbol($currency) ?></span>

									<?php
										$min_max_price = 'a';
										foreach ($ids as $id) {
											$get_price = get_post_meta( $id , '_price' );
											$min_max_price = $min_max_price . "," . $get_price[0];
										}
										$min_max_price = str_replace( "a,", "", $min_max_price );
										$min_max_price = explode( ",", $min_max_price );

									?>
									<span itemprop="price" content="<?php echo min($min_max_price); ?>"><?php echo get_woocommerce_currency_symbol(); echo min($min_max_price); ?></span>
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
								<div class="big-text">
								<?php
								$total_avg_rating = 0;
								$total_key = 0;
								foreach ($ids as $key => $id) {
									$total_avg_rating = $total_avg_rating + sip_get_avg_rating( $id );
									$total_key = $key;
								}
								$total_key++;
								$total_avg_rating = number_format( $total_avg_rating / $total_key, 2, '.', '' );
								echo $total_avg_rating;

								?> <?php _e('out of 5 stars', 'sip-reviews-shortcode'); ?></div>
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
													<td class="rating-number sip-stars-rating" data-number="<?php echo $i; ?>">
														<a href="javascript:void(0);" <?php echo $star_color; ?>><?php echo $i; ?> <span class="fa fa-<?php echo ( (empty($icon)) ? $rated_icons : $icon ); ?>"></span></a>
													</td>
													<td class="rating-graph sip-stars-rating" data-number="<?php echo $i; ?>">
														<a style="float:left; <?php echo $bar_color; ?> width: <?php echo $percentage; ?>%" class="bar" href="javascript:void(0);" title="<?php printf( '%s%%', $percentage ); ?>"></a>
													</td>
													<td class="rating-count sip-stars-rating" data-number="<?php echo $i; ?>">
														<a href="javascript:void(0);" <?php echo $star_color; ?>><?php echo $get_aggregated_rating_count[$i]; ?></a>
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
							<?php
								$options = get_option( 'color_options' );
								$load_more_text = ( isset( $options['load_more_text'] ) ) ? sanitize_text_field( $options['load_more_text'] ) : '#ffffff';
								$load_more_button = ( isset( $options['load_more_button'] ) ) ? sanitize_text_field( $options['load_more_button'] ) : '#dddddd';
								$button = 'style="';
								if( $load_more_button != "" ) {
									$button .= 'background:'. $load_more_button .' !important;';
									$button .= 'border:'. $load_more_button .' !important;';
								}

								if( $load_more_text != "" ) {
									$button .= 'color:'. $load_more_text .';';
								}

								$button .= '"';
							?>
							<?php if ( $filter == "yes" ) { ?>
								<input type="button" <?php echo $button; ?> class="rating_filter" value="Filter" name="rating_filter">
							<?php } ?>

							<form action="#" class="rating_filter_form" style="display: none">
								<div>
									<label for="star_1"><input type="checkbox" id="star_1" class="star_1" value="1" name="rating_filter_stars"><?php _e('1 Star', 'sip-reviews-shortcode'); ?></label>
									<label for="star_2"><input type="checkbox" id="star_2" class="star_2" value="2" name="rating_filter_stars"><?php _e('2 Star', 'sip-reviews-shortcode'); ?></label>
									<label for="star_3"><input type="checkbox" id="star_3" class="star_3" value="3" name="rating_filter_stars"><?php _e('3 Star', 'sip-reviews-shortcode'); ?></label>
									<label for="star_4"><input type="checkbox" id="star_4" class="star_4" value="4" name="rating_filter_stars"><?php _e('4 Star', 'sip-reviews-shortcode'); ?></label>
									<label for="star_5"><input type="checkbox" id="star_5" class="star_5" value="5" name="rating_filter_stars"><?php _e('5 Star', 'sip-reviews-shortcode'); ?></label>
								</div>
								<div>
									<label for="datepicker-start"><?php _e('Date from', 'sip-reviews-shortcode'); ?><input type="text" id="datepicker-start" class="date_start" name="date_start"></label>
									<label for="datepicker-end"><?php _e('to', 'sip-reviews-shortcode'); ?><input type="text" id="datepicker-end" class="date_end" name="date_end"></label>
								</div>
								<div>
									<label for="verified_owners"><?php _e('Show reviews by verified owners only', 'sip-reviews-shortcode'); ?><input type="checkbox" id="verified_owners" class="verified_owners" name="verified_owners"></label>
								</div>
								<div>
									<label for="number_of_reviews"><?php _e('Show', 'sip-reviews-shortcode'); ?><input type="number" min="0" id="number_of_reviews" class="number_of_reviews" name="number_of_reviews"><?php _e('reviews at the time', 'sip-reviews-shortcode'); ?></label>
								</div>
								<div>
									<label>
										<select name="multiple_products_ids" class="multiple_products_ids" multiple style="background: transparent; height: initial;">
											<?php foreach ($ids as $key => $value) {
												if ( $key == 0 ) {
													echo '<option value="'.$value.'" selected="selected">'.get_the_title ( $value ).'</option>';
												} else {
													echo '<option value="'.$value.'">'.get_the_title ( $value ).'</option>';
												}
											}
											?>
										</select><?php _e('Hold down the Ctrl (windows) / Command (Mac) button to select multiple options.', 'sip-reviews-shortcode'); ?>
									</label>
								</div>
								<div>
									<input type="submit" <?php echo $button; ?> name="filter-submit" class="filter-submit" value="Filter">
									<input type="reset" <?php echo $button; ?> class="rating_filter_reset" value="Reset" name="rating_filter_reset">
								</div>
							</form>
						</div>

						<!--Tabs: Start -->
						<aside class="tabs-wrap">
							<div class="page-wrap">
								<div class="tabs-content">
								<?php woocommerce_print_aggregate_reviews( $ids , $product_title , $no_of_reviews, $style, $product_name, $thumbs, $start_date, $end_date ); ?>
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
			<div class="jquery_number_review_remove">
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
			</div>
			<div class="jquery_number_review"><script></script></div>
			<?php
			}// end of post id is product or not

		return ob_get_clean();
	}


	/**
	 * To give complete list of comments in ul tag, it ie printing the all data of li
	 *
	 * @since    	1.0.0
	 * @return 		string , mixed html string in $out_reviews
	 */

	function woocommerce_print_aggregate_reviews( $ids = "" , $title = "" , $no_of_reviews = 5 , $style = "" , $product_names = false , $thumbs = false, $start_date = 0, $end_date = 0 ) {

		global $wpdb, $post;
		$title = filter_var ( $title, FILTER_SANITIZE_MAGIC_QUOTES );
		$colleted_id = 0;
		foreach ($ids as $id) {
			$colleted_id .= $id;
		}

		$colleted_id = substr($colleted_id, 0, 20);
		?>
			<script>
				jQuery(document).ready(function($){
					$( "#datepicker-start" ).datepicker();
					$( "#datepicker-end" ).datepicker();
					$(".rating_filter").click(function(event) {
						/* Act on the event */
						$(this).hide('slow');
						$(".rating_filter_form").fadeIn('slow');
					});

					$('form.rating_filter_form').submit(function(event) {
						var checkboxValues = [];
						/* Act on the event */
						$this = $(this);
						$('input[name=rating_filter_stars]:checked').map(function() {
							checkboxValues.push($(this).val());
						});

						$date_start = $this.find(".date_start").val();
						$date_end = $this.find(".date_end").val();

						$verified_owners = $this.find(".verified_owners:checkbox:checked").val();

						$number_of_reviews = $this.find(".number_of_reviews").val();
						$multiple_products_ids = $this.find(".multiple_products_ids").val();

						var style	= "<?php echo $style; ?>";
						var title 	= "<?php echo $title; ?>";
						var thumbs	= "<?php echo $thumbs; ?>";
						var product_names = "<?php echo $product_names; ?>";

						$('.sip-rswc-more').html('<p align="center"><img src="<?php echo SIP_RSWC_URL; ?>public/img/ajax-loader.gif" ></p>');
						var data = {
							'action': 'filter_post_aggregate_rating', 
							'security' : '<?php $ajax_nonce = wp_create_nonce( "sip-rswc-filter-post-aggregate-rating" ); echo $ajax_nonce; ?>',
							'rating_filter_stars': checkboxValues,
							'title' : title, 
							'thumbs' : thumbs, 
							'style' : style ,
							'number_of_reviews' : $number_of_reviews,
							'verified_owners' : $verified_owners, 
							'id' : $multiple_products_ids,
							'end_date' : $date_end,
							'start_date' : $date_start,
							'product_names' : product_names
						};

						$.post( sip_rswc_ajax.ajax_url, data ).done(function( html ) {

							$('.show-everthing').hide();
							// $('.sip-rswc-more').hide();
							$(".jquery_number_review_remove").remove();
							// $('.jquery_number_review script').html("jQuery(document).ready(function () { size_li_<?php echo $colleted_id; ?> = jQuery('#comments_list_<?php echo $colleted_id; ?> li').size(); x_<?php echo $colleted_id; ?> = $number_of_reviews; jQuery('#comments_list_<?php echo $colleted_id; ?> li:lt('+x_<?php echo $colleted_id; ?>+')').show(); if( size_li_<?php echo $colleted_id; ?> <= x_<?php echo $colleted_id; ?> ){ jQuery('#sip-rswc-more-<?php echo $colleted_id; ?>').hide(); } jQuery('#sip-rswc-more-<?php echo $colleted_id; ?>').click(function () { x_<?php echo $colleted_id; ?> = ( x_<?php echo $colleted_id; ?> + $number_of_reviews <= size_li_<?php echo $colleted_id; ?> ) ? x_<?php echo $colleted_id; ?> + $number_of_reviews : size_li_<?php echo $colleted_id; ?> ; jQuery('#comments_list_<?php echo $colleted_id; ?> li:lt('+ x_<?php echo $colleted_id; ?> +')').show(); if( x_<?php echo $colleted_id; ?> == size_li_<?php echo $colleted_id; ?> ){ jQuery('#sip-rswc-more-<?php echo $colleted_id; ?>').hide(); } }); });");
							$('.tabs-content').html(html);
							$("img.avatar").addClass("thumb");
							$(".sip-star-rating").each(function () {
								var value = $(this).text();
								$(".rating-readonly-"+value).barrating({theme: "fontawesome-stars", readonly:true, initialRating: value });
							});
						});

						$(".rating_filter_form").hide("slow");
						$(".rating_filter").show("slow");

						event.preventDefault();
					});

					$('.sip-stars-rating').click(function(){

						var number 	= $(this).data("number");
						var style	= "<?php echo $style; ?>";
						var id		= '<?php echo json_encode($ids); ?>';
						var title 	= "<?php echo $title; ?>";
						var thumbs	= "<?php echo $thumbs; ?>";
						var start_date	= "<?php echo $start_date; ?>";
						var end_date	= "<?php echo $end_date; ?>";
						var product_names = "<?php echo $product_names; ?>";
						$('.sip-rswc-more').html('<p align="center"><img src="<?php echo SIP_RSWC_URL; ?>public/img/ajax-loader.gif" ></p>');

						var data = {
							'action': 'more_post_aggregate_rating', 
							'security' : '<?php $ajax_nonce = wp_create_nonce( "sip-rswc-more-post-aggregate-rating" ); echo $ajax_nonce; ?>',
							'title' : title,
							'thumbs' : thumbs,
							'number': number,
							'style' : style,
							'id' : id,
							'end_date' : end_date,
							'start_date' : start_date,
							'product_names' : product_names
						};

						$.post( sip_rswc_ajax.ajax_url, data ).done(function( html ) {

							$('.show-everthing').hide();
							$('.sip-rswc-more').hide();
							$('ul.comments_list').html(html);
							$("img.avatar").addClass("thumb");
							$(".sip-star-rating").each(function () {
								var value = $(this).text();
								$(".rating-readonly-"+value).barrating({theme: "fontawesome-stars", readonly:true, initialRating: value });
							});
						});
					});
				});
			</script>
		<?php

		$out_reviews = "";
		$options = get_option( 'color_options' );
	  	$star_color = ( isset( $options['star_color'] ) ) ? sanitize_text_field( $options['star_color'] ) : '#d1c51d';
		$load_more_text = ( isset( $options['load_more_text'] ) ) ? sanitize_text_field( $options['load_more_text'] ) : '#ffffff';
		$load_more_button = ( isset( $options['load_more_button'] ) ) ? sanitize_text_field( $options['load_more_button'] ) : '#dddddd';
		$review_body_text_color = ( isset( $options['review_body_text_color'] ) ) ? sanitize_text_field( $options['review_body_text_color'] ) : '#000000';
		$review_background_color = ( isset( $options['review_background_color'] ) ) ? sanitize_text_field( $options['review_background_color'] ) : '#f2f2f2';
		$review_title_color = ( isset( $options['review_title_color'] ) ) ? sanitize_text_field( $options['review_title_color'] ) : '#000000';

		$button = 'style="';
		if( $load_more_button != "" ) {
			$button .= 'background-color:'. $load_more_button .';';
		}

		if( $load_more_text != "" ) {
			$button .= 'color:'. $load_more_text .';';
		}

		$button .= '"';
		if( $review_title_color != "" ) {
			$review_title_color = "style='color:". $review_title_color .";'";
		}

		$review_background = 'style="';
		if( $review_background_color != "" ) {
			$review_background .= 'background-color:'. $review_background_color .';';
		}

		if( $review_body_text_color != "" ) {
			$review_background .= 'color:'. $review_body_text_color .';';
		}

		if( $style == 2 ) {
			$review_background .= 'margin-left: 40px;';
		}

		$review_background .= '"';
		sort($ids);
		$aggregate_ids = 0 ;

		foreach ($ids as $id) { 
			if( $aggregate_ids != 0 ){
				$aggregate_ids .= ','.$id;
			} else {
				$aggregate_ids = $id; 
			}
		}

		$query = "SELECT c.* FROM {$wpdb->prefix}posts p, {$wpdb->prefix}comments c WHERE p.ID IN ($aggregate_ids) AND p.ID = c.comment_post_ID AND c.comment_approved > 0 AND p.post_type = 'product' AND p.post_status = 'publish' AND p.comment_count > 0 ".($start_date ? " AND c.comment_date >= \"$start_date\" " : "" ) . ($end_date ? " AND c.comment_date <= \"$end_date\" " : "" ) ." AND c.comment_parent = 0 ORDER BY c.comment_ID DESC";
		$comments_products = $wpdb->get_results($query, OBJECT);
			if ( $comments_products ) {
				foreach ( $comments_products as $comment_product ) {
					$id_ 			= $comment_product->comment_post_ID;

                    $author = $comment_product->comment_author;

                    $author_len = strlen($author) / 2;
                    $author = substr($author,0,3);
                    for($i=1; $i<=$author_len; $i++){
                        $author .= "*";
                    }

					$name_author 	= $author;
					$comment_id  	= $comment_product->comment_ID;
					$comment_parent = $comment_product->comment_parent;
					$comment_date_ 	= get_comment_date( 'c', $comment_id );
					$comment_date  	= get_comment_date( wc_date_format(), $comment_id );
					$_product 		= wc_get_product( $id_ );
					$rating 		= intval( get_comment_meta( $comment_id, 'rating', true ) );
					$user_id	 	= $comment_product->user_id;
					$product_title 	= $title;
					$votes 			= "";
					$avatar 		= "";
					$comment_chield	= "";
					$product_image	= "";
					$comment_author_email = $comment_product->comment_author_email;
					$verified_customer = "";

					if( $product_names == true ) {
						$product_title = get_the_title( $id_ );
					}

					if( $thumbs == true ) {

						if ( has_post_thumbnail( $id_ ) ) {
							$pro = new WC_Product($id_);
							$pro = $pro->get_image($size = 'shop_thumbnail');  //Get Image
							$pro = explode('src="', $pro);
							$pro = explode('"', $pro[1]);
							$product_image = $pro[0];
						}
					}

					$args = array(
						'status'  => 'approve',
						'number'  => '100',
						'post_id' => $id_,
						'order'   => 'ASC',
						'parent'  => $comment_id
					);

					$comments = get_comments($args);
					$comments_length = count($comments);
					$iteration = -1;
					$comment_parent_id = $comment_id;
					$sub_comment_ID = "";

					do {
						if( $style == 2 ) {

							if ( function_exists( 'get_avatar' ) ) {
								$avatar = get_avatar(get_comment_author_email($comment_id), 60,  null, null, array( 'class' => array( 'thumb' ) ) );
							} else {
								$grav_url = "http://www.gravatar.com/avatar.php?gravatar_id=
							" . md5($email) . "&default=" . urlencode($default) . "&size=" . $size;
								$avatar = "<img src='$grav_url' height='60px' width='60px' />";
							}
						}

					if( $comment_parent  > 0 ) {
						$comment_chield = " show-everthing-sub";
					}

					if( wc_customer_bought_product( $comment_author_email, $user_id, $id ) && get_option( 'sip-rswc-setting-verified-customer' ) ){
						$verified_customer = ' <em class="verified">'.__('(verified customer)' , 'sip-reviews-shortcode').'</em>';
					}

					if( user_can( $user_id, 'administrator' ) ) {
						$verified_customer = ' <em class="verified">'.__('(verified owner)' , 'sip-reviews-shortcode').'</em>';
					} else if ( user_can( $user_id, 'shop_manager' ) ) {
						$verified_customer = ' <em class="verified">'.__('(verified shop manager)' , 'sip-reviews-shortcode').'</em>';
					}

					$out_reviews 	.= '<li '.( ($rating > 0 ) ? "itemprop='review' itemscope='' itemtype='http://schema.org/Review'" : "" ) .' style="display:none;" id="li-comment-'.$id.'-'.$comment_parent_id.$sub_comment_ID.'" class="show-everthing ShowEve '.$comment_chield.'">';

											if ( get_option( 'sip-rswc-setting-link-to-comments' ) ) {
												$out_reviews 	.= '<a href="'.get_permalink($id_).'#comment-'.$comment_parent_id.'" target="_blank">';
											}

								$out_reviews 	.= '<div class="comment-borderbox" '.$review_background.'>
													'.$avatar;
									$out_reviews .=	'<div class="sip-reviews-up-box">
														<div class="sip-reviews-left-box">';

															if ( get_option( 'sip-rswc-setting-show-product-name' ) ) {

																$out_reviews 	.= '<h3 class="rswc-product-name" '.$review_title_color.'>'.get_the_title($id_).'</h3>';
															}

															if( $comment_parent == 0 ) {
																$out_reviews .=	'<div class="br-wrapper br-theme-fontawesome-stars"><p class="sip-star-rating" style="display:none;">'.$rating.'</p><select class="rating-readonly-'.$rating.'"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option></select></div>';
																$out_reviews .=	'<div '.( ($rating > 0 ) ? "itemprop='reviewRating' itemscope itemtype='http://schema.org/Rating'" : "" ) .' style="display:none;">
																					<span '.( ($rating > 0 ) ? "itemprop='ratingValue'" : "" ) .'>'.$rating.'</span>
																				</div>';
															}

											$out_reviews .=	'<p class="sip-author" '.$review_title_color.'>
																<strong '.( ($rating > 0 ) ? "itemprop='author'" : "" ) .'>'.$name_author.'</strong> – <time '.( ($rating > 0 ) ? "itemprop='datePublished'" : "" ) .' datetime="'.$comment_date_.'">'.$comment_date.'</time>
															</p>';
										$out_reviews .=	'</div>
												 		 <div class="sip-reviews-right-box">';

															if( $thumbs == true ) {
																$out_reviews .= '<div class="sip-reviews-right-box-img old-style"><img src="'.$product_image.'" alt="'.$product_title.'" title="'.$product_title.'"></div>';
															}

															if( $product_names == true ) {
																$out_reviews .= '<div class="sip-reviews-right-box-title"><h2 '.$review_title_color.'>'.$product_title.'</h2></div>';
															}

										$out_reviews .=	'</div>
													</div>
													<div style="clear:both;"></div>';

								if ( !get_option('sip-rswc-setting-limit-review-characters') ) {
									$out_reviews .='<div itemprop="description">
														<p style="color:'.$review_body_text_color.'">'.nl2br( get_comment_text( $comment_id ) ).'</p>
													</div>';
								} else {

									$out_reviews .=	'<div itemprop="description">
														<div class="hide-'.$id_."-".$comment_id.'">
															<p style="color:'.$review_body_text_color.'">'.nl2br( get_comment_excerpt_trim( $id_ , $comment_id ) ).'</p>
														</div>
														<p class="comment-'.$id_."-".$comment_id.'-full" style="display:none;color:'.$review_body_text_color.'">'.nl2br( get_comment_text( $comment_id ) ).'</p>
													</div>';
								}

						$out_reviews 	.=	'</div>';
											if ( get_option( 'sip-rswc-setting-link-to-comments' ) ) {
												$out_reviews 	.= '</a>';
											}

							$out_reviews .=	'</li>';
							$out_reviews .= '<script>
												jQuery(".comment-'.$id_."-".$comment_id.'").click(function(){
													jQuery(".comment-'.$id_."-".$comment_id.'-full").show();
													jQuery(".hide-'.$id_."-".$comment_id.'").hide();
												});
											</script>';
					++$iteration;
					++$comment_parent;
					if( $comments_length > 0 ) {
						if( !empty($comments[$iteration]->comment_author) ) {
							$name_author = $comments[$iteration]->comment_author;
						}

						if( !empty( $comments[$iteration]->comment_ID ) ) {
							$sub_comment_ID = "-".$comments[$iteration]->comment_ID;
							$comment_date_ 	= get_comment_date( 'c', $comments[$iteration]->comment_ID );
							$comment_date  	= get_comment_date( wc_date_format(), $comments[$iteration]->comment_ID );	
						}

						if( !empty($comments[$iteration]->comment_ID )) {
							$comment_id = $comments[$iteration]->comment_ID;
						}
					}
				} while ( $comments_length > $iteration );
			}//end of lop

		} //end of if condition

		if ( $out_reviews != '' ) {
			$colleted_id = 0;
			foreach ($ids as $id) {
				$colleted_id .= $id;
			}

			$colleted_id = substr($colleted_id, 0, 20);
			$out_reviews  = '<ul id="comments_list_'.$colleted_id.'" class="commentbox comments_list commentlist commentlist-'. $id .' commentlist_'. $id .'">' . $out_reviews . '</ul><button '. $button .' class="sip-rswc-more" id="sip-rswc-more-'.$colleted_id.'" type="button">'.__('Load More' , 'sip-reviews-shortcode').'</button>';

		} else {
			$out_reviews = '<ul class="commentlist"><li><p class="commentbox content-comment">'. __('No products reviews.' , 'sip-reviews-shortcode') . '</p></li></ul>';
		}

		echo $out_reviews;
	}


add_action( 'wp_head', function() {

	global $post;
	if( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'woocommerce_reviews_aggregate') ) {

		$options 	= get_option( 'color_options' );
	  	$star_color = ( isset( $options['star_color'] ) ) ? sanitize_text_field( $options['star_color'] ) : '#d1c51d';
		$bar_color 		= ( isset( $options['bar_color'] ) ) ? sanitize_text_field( $options['bar_color'] ) : '#AD74A2';
		$accent_color 	= ( isset( $options['accent_color'] ) ) ? sanitize_text_field( $options['accent_color'] ) : '#32ddee';
		$review_body_text_color = ( isset( $options['review_body_text_color'] ) ) ? sanitize_text_field( $options['review_body_text_color'] ) : '';
		$review_background_color = ( isset( $options['review_background_color'] ) ) ? sanitize_text_field( $options['review_background_color'] ) : '#f2f2f2';
	  	?>
	  		<style>
				.star-rating:before, .woocommerce-page .star-rating:before, .star-rating span:before, .br-theme-fontawesome-stars .br-widget a.br-selected:after {color: <?php echo $star_color; ?>;}
	  		</style>
	  	<?php
	}
}, 10 );