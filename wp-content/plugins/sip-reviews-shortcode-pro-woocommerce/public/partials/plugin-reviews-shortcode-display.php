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
// time format YYYY-MM-DD 11:46:54
/**
 * Sortcode function Template
 *
 * @since    	1.0.0
 */
function sip_review_shortcode_wc( $atts ) {

	global $post, $wpdb, $product;
	extract( shortcode_atts(
		array(
			'id' 			=> '',
			'style'			=> '1',
			'icon'			=> '',
			'no_of_reviews' => '5',
			'product_title' => '',
			'product_names' => '',
			'description' => '',
			'total_review_score' => '',
			'offers'		=> '',
			'review'		=> '',
			'thumbs' 		=> '',
			'sku'			=> '',
			'review_id'		=> '0',
			'start_date' 	=> '0',
			'end_date'		=> '0',
			'limit'			=> '',
		), $atts )
	);

	if (empty($icon)) {
		$icon = ( (get_option('sip-rswc-setting-rated-icon') ) ? get_option('sip-rswc-setting-rated-icon') : "star" );
	}

	if ( $review_id != 0 ) {
		$review_id = strtolower( $review_id );
		$review_id = str_replace( " ", "", $review_id );
		$review_id = explode( ",", strval( $review_id ) );

		if ( $style == 3 ) {
			return sip_review_id_style_3( $review_id, $product_title, $product_names, $thumbs, $description, $total_review_score, $offers, $review, $sku, $icon );
		} elseif ( $style == 2 || $style == 1 ) {

			return sip_review_id_style_all( $review_id, $style, $product_title, $product_names, $thumbs, $description, $total_review_score, $offers, $review, $sku, $icon );
		} else {
			wp_enqueue_style( 'sip-rswc-carousel-theme' );
			wp_enqueue_style( 'sip-rswc-carousel' );
			wp_enqueue_script( 'sip-rswc-carousel' );

			return sip_review_id_carousel_style( $review_id, $product_title, $product_names, $thumbs, $description, $total_review_score, $offers, $review, $sku, $icon );
		}
	}

	if ( $id == "" || $id == 0 ) {
		if ( isset( $product->id ) ) {
			$id = $product->id;
		}
	}

	if ( $sku != "" ) {
		$id = wc_get_product_id_by_sku( $sku );
	}

	// if product title is not mention by user in shortcode then get default value
	if( $product_title == "" ) {
		$product_title 	= get_the_title( $id );
	}

	if ( $style == 4 ) {
		
		wp_enqueue_style( 'sip-rswc-carousel-theme' );
		wp_enqueue_style( 'sip-rswc-carousel' );
		wp_enqueue_script( 'sip-rswc-carousel' );

		return sip_review_carousel_style( $id, $product_title, $icon, $product_names, $thumbs, $start_date, $end_date, $limit );
	} elseif ( $style == 3 ) {
		return sip_review_shortcode_style_3( $id, $no_of_reviews, $icon, $product_title, $product_names, $thumbs, $start_date, $end_date);
	}

	$options 	= get_option( 'color_options' );
	$star_color = ( isset( $options['star_color'] ) ) ? sanitize_text_field( $options['star_color'] ) : '#d1c51d';
	$bar_color 	= ( isset( $options['bar_color'] ) ) ? sanitize_text_field( $options['bar_color'] ) : '#AD74A2';

	if( $star_color != "")
		$star_color = "style='color:". $star_color .";'";

	if( $bar_color != "")
		$bar_color = "background-color:".$bar_color .";";

	// To check that post id is product or not
	if( get_post_type( $id ) == 'product' ) {
		ob_start();?>
		<?php $get_avg_rating = sip_get_avg_rating( $id ); ?>
		<?php $get_review_count = sip_get_review_count( $id ); ?>
		<?php $get_price = sip_get_price($id); ?>

		<!--Wrapper: Start -->
		<div class="sip-rswc-wrapper <?php echo $icon; ?>">
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
							<a href="<?php echo $image[0]; ?>" itemprop="image"><?php echo $product_title; ?></a>
							<span itemprop="name"><?php echo $product_title; ?></span>
							<meta itemprop="url" content="<?php echo get_permalink( $id ); ?>">
							<div class="star_container" itemprop="aggregateRating" itemscope="" itemtype="http://schema.org/AggregateRating">
								<span itemprop="ratingValue"><?php echo $get_avg_rating; ?></span>
								<span itemprop="bestRating">5</span>
								<span itemprop="ratingCount"><?php echo $get_review_count; ?></span>
								<span itemprop="reviewcount" style="display:none;"><?php echo $get_review_count; ?></span>
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
							<div class="sm-text"><?php echo $get_review_count; ?> 
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
													<a href="javascript:void(0);" <?php echo $star_color; ?>><?php echo $i; ?> <span class="fa fa-<?php echo ( (empty($icon)) ? $rated_icons : $icon ); ?>"></span></a>
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
							
							<?php woocommerce_print_reviews( $id, $product_title, $no_of_reviews, $style, $product_names, $thumbs, $start_date, $end_date ); ?> 
								
							</div>
						</div>
					</aside><!-- .tabs-wrap -->
					<!--Tabs: Start -->	
				</aside>
			</div><!--Main Container: End -->
		</div><!--Wrapper: End -->
		<div style="clear:both"></div>
		<?php
		return ob_get_clean();
	}// end of post id is product or not
}


/**
 * To give complete list of comments in ul tag, it ie printing the all data of li
 *
 * @since    	1.0.0
 * @return 		string , mixed html string in $out_reviews
 */
function woocommerce_print_reviews( $id = "" , $title = "" , $no_of_reviews = 5 , $style = "" , $product_names = false , $thumbs = false , $start_date = "", $end_date = "" ) {

	global $wpdb, $post;
	$title = filter_var ( $title, FILTER_SANITIZE_MAGIC_QUOTES );
	$comments_approved	= sip_get_review_count( $id ); ?>

	<script>

		jQuery(document).ready(function($){

			var comments_approved = <?php echo $comments_approved ?>;
			var no_of_reviews	= <?php echo $no_of_reviews; ?>;

			if ( comments_approved <= no_of_reviews ) {
				$('#sip-rswc-more-<?php echo $id ?>').hide();
			}

			$('#sip-rswc-more-<?php echo $id ?>').click(function(){

				var get_last_post_display = $("[id*='li-comment-<?php echo $id ?>-']").last().attr('id'); //get ip last <li>
				var limit	= <?php echo $no_of_reviews; ?>;
				var id 		= <?php echo $id ?>;
				var title 	= "<?php echo $title; ?>";
				var style	= "<?php echo $style; ?>";
				var thumbs	= "<?php echo $thumbs; ?>";
				var start_date	= "<?php echo $start_date; ?>";
				var end_date	= "<?php echo $end_date; ?>";
				var product_names = "<?php echo $product_names; ?>";

				var data = {
					'action': 'more_post_default_style',
					'security' : '<?php $ajax_nonce = wp_create_nonce( "sip-rswc-more-post-default-style" ); echo $ajax_nonce; ?>',
					'last_id_post': get_last_post_display, 
					'limit': limit, 
					'id' : id, 
					'title' : title, 
					'style' : style, 
					'product_names' : product_names, 
					'thumbs' : thumbs,
					'end_date' : end_date,
					'start_date' : start_date
				};
				
				$.post( sip_rswc_ajax.ajax_url, data ).done(function( html ) {
					
					$('ul.commentlist-<?php echo $id ?>').append(html);
					$("img.avatar").addClass("thumb");

					$(".sip-star-rating").each(function () {
						var value = $(this).text();
					 	$(".rating-readonly-"+value).barrating({theme: "fontawesome-stars", readonly:true, initialRating: value });
					});

					$('#sip-rswc-more-<?php echo $id ?>').text("<?php _e('Load More' , 'sip-reviews-shortcode');?>"); //add text "Load More Post" to button again
					if( html == "" ) {
						$('#sip-rswc-more-<?php echo $id ?>').text("<?php _e('No more comments' , 'sip-reviews-shortcode');?>"); // when last record add text "No more posts to load" to button.
					}
					if (!html.trim()) {
						// is empty or whitespace
						$('#sip-rswc-more-<?php echo $id ?>').text("<?php _e('No more comments' , 'sip-reviews-shortcode');?>");
					// $('#sip-rswc-more').remove();
					}
				});
			});

			$('.sip-stars-rating').click(function(){

				var number 	= $(this).data("number");
				var style	= "<?php echo $style; ?>";
				var id		= "<?php echo $id; ?>";
				var title 	= "<?php echo $title; ?>";
				var style	= "<?php echo $style; ?>";
				var thumbs	= "<?php echo $thumbs; ?>";
				var product_names = "<?php echo $product_names; ?>";
				var start_date	= "<?php echo $start_date; ?>";
				var end_date	= "<?php echo $end_date; ?>";

				$('#sip-rswc-more-<?php echo $id ?>').html('<p align="center"><img src="<?php echo SIP_RSWC_URL; ?>public/img/ajax-loader.gif" ></p>');

				var data = {
					'action': 'more_post_rating',
					'security' : '<?php $ajax_nonce = wp_create_nonce( "sip-rswc-more-post-rating" ); echo $ajax_nonce; ?>',
					'title' : title, 
					'thumbs' : thumbs, 
					'number': number, 
					'style' : style , 
					'id' : id, 
					'product_names' : product_names,
					'end_date' : end_date,
					'start_date' : start_date
				};

				$.post( sip_rswc_ajax.ajax_url, data ).done(function( html ) {

					$('.show-everthing').hide();
					$('.sip-rswc-more').hide();
					$('ul.commentlist-<?php echo $id ?>').append(html);
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

		$options = get_option( 'color_options' );
		$star_color = ( isset( $options['star_color'] ) ) ? sanitize_text_field( $options['star_color'] ) : '#d1c51d';
		$load_more_button = ( isset( $options['load_more_button'] ) ) ? sanitize_text_field( $options['load_more_button'] ) : '#dddddd';
		$load_more_text = ( isset( $options['load_more_text'] ) ) ? sanitize_text_field( $options['load_more_text'] ) : '#ffffff';
		$review_body_text_color = ( isset( $options['review_body_text_color'] ) ) ? sanitize_text_field( $options['review_body_text_color'] ) : '#000000';
		$review_background_color = ( isset( $options['review_background_color'] ) ) ? sanitize_text_field( $options['review_background_color'] ) : '#f2f2f2';
		$review_title_color = ( isset( $options['review_title_color'] ) ) ? sanitize_text_field( $options['review_title_color'] ) : '#000000';

		$button = 'style="';
		if( $load_more_button != "" )
			$button .= 'background-color:'. $load_more_button .';';
		if( $load_more_text != "" )
			$button .= 'color:'. $load_more_text .';';
		$button .= '"';

		if( $review_title_color != "" )
			$review_title_color = "style='color:". $review_title_color .";'";

			$review_background = 'style="';
		if( $review_background_color != "" )
			$review_background .= 'background-color:'. $review_background_color .';';
		if( $review_body_text_color != "" )
			$review_background .= 'color:'. $review_body_text_color .';';
		if( $style == 2 )
			$review_background .= 'margin-left: 40px;';
			$review_background .= '"';

		$query = $wpdb->prepare( "SELECT c.* FROM {$wpdb->prefix}posts p, {$wpdb->prefix}comments c WHERE p.ID = %d AND p.ID = c.comment_post_ID AND c.comment_approved > 0 AND p.post_type = 'product' AND p.post_status = 'publish' AND p.comment_count > 0  ".($start_date ? " AND c.comment_date >= \"$start_date\" " : "" ) . ($end_date ? " AND c.comment_date <= \"$end_date\" " : "" ) ." AND c.comment_parent = 0 ORDER BY c.comment_ID DESC limit %d",$id ,$no_of_reviews );
		$comments_products 	= 	$wpdb->get_results($query, OBJECT);

		$out_reviews = "";
		if ( $comments_products ) {
			foreach ( $comments_products as $comment_product ) {

				$id_			= $comment_product->comment_post_ID;
				$name_author	= $comment_product->comment_author;
				$comment_id		= $comment_product->comment_ID;
				$comment_parent = $comment_product->comment_parent;
				$comment_date_	= get_comment_date( 'c', $comment_id );
				$comment_date	= get_comment_date( wc_date_format(), $comment_id );
				$_product		= wc_get_product( $id_ );
				$rating			= intval( get_comment_meta( $comment_id, 'rating', true ) );
				$user_id		= $comment_product->user_id;
				$product_title	= "";
				$votes			= "";
				$avatar			= "";
				$comment_chield	= "";
				$product_image	= "";
				$comment_author_email = $comment_product->comment_author_email;
				$verified_customer = "";

				if( $product_names == true )
					$product_title = get_the_title( $id_ );

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
					'status'	=> 'approve', 
					'number'	=> '100',
					'post_id'	=> $id_,
					'order'		=> 'ASC',
					'parent'	=> $comment_id
				);
				$comments			= get_comments($args);
				$comments_length	= count($comments);
				$iteration			= -1;
				$comment_parent_id	= $comment_id;
				$sub_comment_ID		= "";

				do {

					if( $style == 2 ) {
						if ( function_exists( 'get_avatar' ) ) {
							$avatar = get_avatar(get_comment_author_email($comment_id), 60,  null, null, array( 'class' => array( 'thumb' ) ) );
						} else {
							$grav_url = "http://www.gravatar.com/avatar.php?gravatar_id=".md5($email)."&default=".urlencode($default)."&size=".$size;
							$avatar = "<img src='$grav_url' height='60px' width='60px' />";
						}
					}

					if( $comment_parent  > 0 ) {
						$comment_chield = " show-everthing-sub";
					}

					if( wc_customer_bought_product( $comment_author_email, $user_id, $id ) && get_option( 'sip-rswc-setting-verified-customer' ) ) {
						$verified_customer = ' <em class="verified">'.__('(verified customer)' , 'sip-reviews-shortcode').'</em>';
					}

					if( user_can( $user_id, 'administrator' ) ) {
						$verified_customer = ' <em class="verified">'.__('(verified owner)' , 'sip-reviews-shortcode').'</em>';
					} else if ( user_can( $user_id, 'shop_manager' ) ) {
						$verified_customer = ' <em class="verified">'.__('(verified shop manager)' , 'sip-reviews-shortcode').'</em>';
					}

					$out_reviews 	.= '<li itemprop="review" itemscope itemtype="http://schema.org/Review" id="li-comment-'.$id.'-'.$comment_parent_id.$sub_comment_ID.'" class="show-everthing ShowEve '.$comment_chield.'">';

										if ( get_option( 'sip-rswc-setting-link-to-comments' ) ) {
											$out_reviews .= '<a href="'.get_permalink($id_).'#comment-'.$comment_parent_id.'" target="_blank">';
										}

						$out_reviews 	.=	'<div class="comment-borderbox" '.$review_background.'> 
												'.$avatar ;

							$out_reviews 	.=	'<div class="sip-reviews-up-box">
													<div class="sip-reviews-left-box">';

														if( $comment_parent == 0 ) {
															$out_reviews .=	'<div class="br-wrapper br-theme-fontawesome-stars"><p class="sip-star-rating" style="display:none;">'.$rating.'</p><select class="rating-readonly-'.$rating.'"> <option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option></select>
															</div>';
															$out_reviews .=	'<div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" style="display:none;">
																				<span itemprop="ratingValue">'.$rating.'</span>
																			</div>';
														}
								$out_reviews 	.=	'<p class="sip-author" '.$review_title_color.'>
														<strong itemprop="author">'.$name_author.'</strong> – <time itemprop="datePublished" datetime="'.$comment_date_.'">'.$comment_date.'</time>
													</p>';

							$out_reviews 	.=	'</div>
												 <div class="sip-reviews-right-box">';

													if( $thumbs == true ) {
														$out_reviews 	.= '<div class="sip-reviews-right-box-img old-style"><img src="'.$product_image.'" alt="'.$product_title.'" title="'.$product_title.'"></div>';
													}

													if( $product_names == true ) {
														$out_reviews 	.= '<div class="sip-reviews-right-box-title"><h2 '.$review_title_color.'>'.$product_title.'</h2></div>';
													}

							$out_reviews 	.=	'</div>
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
				$out_reviews 	.= '</li>';

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
						$name_author 	= $comments[$iteration]->comment_author;
					}
					if( !empty( $comments[$iteration]->comment_ID ) ) {
						$sub_comment_ID = "-".$comments[$iteration]->comment_ID;
						$comment_date_ 	= get_comment_date( 'c', $comments[$iteration]->comment_ID );
						$comment_date  	= get_comment_date( wc_date_format(), $comments[$iteration]->comment_ID );
					}
					if( !empty($comments[$iteration]->comment_ID )) {
						$comment_id 	= $comments[$iteration]->comment_ID;	
					}	
				}
			} while ( $comments_length > $iteration );
		}//end of lop
	} //end of if condition
	if ( $out_reviews != '' ) {
		$out_reviews  = '<ul id="sip-commentlist-id-'. $id .'-'.$no_of_reviews.'" class="commentbox commentlist commentlist-'. $id .' commentlist_'. $id .'">' . $out_reviews . '</ul><button '. $button .' class="sip-rswc-more" id="sip-rswc-more-'. $id .'" type="button">'.__('Load More' , 'sip-reviews-shortcode').'</button>';
	} else {
		$out_reviews = '<ul class="commentlist"><li><p class="commentbox content-comment">'. __('No products reviews.' , 'sip-reviews-shortcode') . '</p></li></ul>';
	}
	echo $out_reviews;
}

function sip_review_shortcode_style_3( $id, $no_of_reviews, $icon, $product_title, $product_names = false , $thumbs = false, $start_date, $end_date ) {

	ob_start();
	$sip_post_thumbnail = "";
	
	if (has_post_thumbnail( $id ) ):
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'single-post-thumbnail' );
		$sip_post_thumbnail = 'url('.$image[0].') no-repeat center center';
	endif; 
	?>

	<script>	

		var tick = function ($el, duration) {
			'use strict';
			$el.attr('data-duration', duration);
			$el.find('.duration').html(duration);
		};

	(function ($) {
		'use strict';
		$.fn.timer = function (options) {
			var settings = $.extend({ }, options);
			return this.each(function () {
				var duration = settings.duration;
				var unit = settings.unit;
				var $$ = $(this);
				$$.html('<div class="sip-rswc-timer-bg"><small class="unit"></small></div>' +
				'<div class="sip-rswc-timer-half-container right"><div class="sip-rswc-timer-half right"></div></div>' +
				'<div class="sip-rswc-timer-half-container left"><div class="sip-rswc-timer-half left"></div></div>');
				$$.addClass('sip-rswc-timer');
				$$.find('.unit').html(unit);
				tick($$, duration);
			});
		};
	}(jQuery));
	</script>
	<?php

	$options = get_option( 'color_options' );
	$star_color = ( isset( $options['star_color'] ) ) ? sanitize_text_field( $options['star_color'] ) : '#d1c51d';
	$bar_color = ( isset( $options['bar_color'] ) ) ? sanitize_text_field( $options['bar_color'] ) : '#AD74A2';
	$accent_color = ( isset( $options['accent_color'] ) ) ? sanitize_text_field( $options['accent_color'] ) : '#32ddee';

	if( $star_color != "" )
		$star_color = "style='color:". $star_color .";'";

	if( $bar_color != "" )
		$bar_color = "background-color:".$bar_color .";";

	// To check that post id is product or not
	if( get_post_type( $id ) == 'product' ) { ?>

		<?php $get_avg_rating = sip_get_avg_rating( $id ); ?>
		<?php $get_review_count = sip_get_review_count( $id ); ?>
		<?php $get_price = sip_get_price($id); ?>

		<!-- it is not for display it is only to generate schema for goolge search result -->
		<div itemscope itemtype="http://schema.org/Product" id="product-<?php echo $id; ?>">
			<div style="display:none;">
				<span itemprop="name"><?php echo $product_title; ?></span>
				<a href="<?php echo $image[0]; ?>" itemprop="image"><?php echo $product_title; ?></a>
				<meta itemprop="url" content="<?php echo get_permalink( $id ); ?>">
				<div class="star_container" itemprop="aggregateRating" itemscope="" itemtype="http://schema.org/AggregateRating">
					<span itemprop="ratingValue"><?php echo $get_avg_rating; ?></span>
					<span itemprop="bestRating">5</span>
					<span itemprop="reviewcount" style="display:none;"><?php echo $get_review_count; ?></span>
					<span itemprop="ratingCount"><?php echo $get_review_count; ?></span>
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

			<script>
				jQuery(document).ready(function ($) {

					$('.woo-review-overlay-<?php echo $id; ?>').css('background', '<?php echo $sip_post_thumbnail; ?>');

					$('#sip-review-circle-<?php echo $id; ?>').timer({duration: <?php echo intval(( $get_avg_rating*12 )); ?>, unit: '<span class="sip-score-star"><span style="width: <?php echo ( $get_avg_rating*20 ) ?>%"></span></span>'});
				})
			</script>
		
			<div class="woo-review-container sip-fa3-<?php echo $icon; ?>">
				<h2 class="woo-review-h2"><?php echo $product_title; ?></h2>
				<!-- review top div start -->
				<div class="woo-review-overlay woo-review-overlay-<?php echo $id; ?>">
					<div class="woo-review-overlay-image">
						<div class="woo-review-result-col-right">
							<div class="woo-review-star-block">
								<div class="woo-review-star-block-inn">
									<div id="sip-review-circle-<?php echo $id; ?>"></div>
								</div>
							</div>
							<div class="woo-review-larg-hed"><?php echo $get_avg_rating; ?> <?php _e('out of 5 stars' , 'sip-reviews-shortcode');?></div>
							<div class="woo-review-small-hed"><?php echo $get_review_count; ?>  <?php _e('reviews' , 'sip-reviews-shortcode'); ?></div>
						</div>
						<div class="woo-review-result-col-left">
							<?php $get_rating_count = sip_get_rating_count( $id ); ?>
							<?php for ( $i = 5; $i > 0 ; $i-- ) {
								if ( !isset( $get_rating_count[$i] ) ) {
									$get_rating_count[$i] = 0;
								}
								
								$percentage = 0 ;
								if ( $get_rating_count[$i] > 0 ) {
									$percentage = ($get_rating_count[$i] / $get_review_count ) * 100;
								}
								$url = get_permalink(); ?>
								<?php 
									$rated_icons = ( (get_option('sip-rswc-setting-rated-icon') ) ? get_option('sip-rswc-setting-rated-icon') : "star" );
								?>

								<div class="woo-review-num-row">
									<div class="woo-review-count-img sip-stars-rating" data-number="<?php echo $i; ?>">
										<a href="javascript:void(0);">
											<span class="woo-review-count-big" <?php echo $star_color; ?> ><?php echo $i; ?> <span class="fa fa-<?php echo ( (empty($icon)) ? $rated_icons : $icon ); ?>"></span></span>
										</a>
									</div>
									<div class="woo-review-progress-sect sip-stars-rating" data-number="<?php echo $i; ?>">
										<a href="javascript:void(0);">
											<div class="woo-review-progress" style="<?php echo $bar_color; ?> width: <?php echo $percentage; ?>%" ></div>
										</a>
									</div>
									<div class="woo-review-count-only sip-stars-rating" data-number="<?php echo $i; ?>">
										<a href="javascript:void(0);">
											<span class="woo-review-count-sml" <?php echo $star_color; ?> ><?php echo $get_rating_count[$i]; ?></span>
										</a>
									</div>
								</div>
						<?php } ?>

					</div> 
				</div>
			</div>

			<!-- review top div end -->
			<div class="woo-clearfix"></div>
			<!-- review bottom div start -->

			<?php woocommerce_reviews_print_style3( $id, $product_title, $no_of_reviews, $product_names, $thumbs, $start_date, $end_date ); ?>
			</div>
			<div class="woo-clearfix"></div>
		</div><!-- /.woo-review-container -->
		<?php 
	}
	return ob_get_clean();
}//end function

/**
 * To get limited text comments to dispaly
 *
 * @since    	1.0.0
 * @return 		string 	it is return the 35 chracters of comments 
 */
function get_comment_excerpt_trim( $p_id = 0, $comment_ID = 0 ) {
    $comment 		= get_comment( $comment_ID );
    $comment_text 	= strip_tags($comment->comment_content);
   	$blah 			= strlen( $comment_text );

    if ( get_option('sip-rswc-setting-limit-review-characters') > 0 ) {
        if ( $blah < get_option('sip-rswc-setting-limit-review-characters') ) {
	        $use_dotdotdot = 0;
	        $excerpt = $comment_text;
        } else {
        	$limit = get_option('sip-rswc-setting-limit-review-characters');
        	$use_dotdotdot = 1;
        	$excerpt = mb_strimwidth( $comment_text , 0, $limit, "" );

        }

    } else {
        $use_dotdotdot = 0;
        $excerpt = $comment_text;
    }
  
	
    $excerpt .= ($use_dotdotdot) ? ' <a style="cursor:pointer" class="comment-'.$p_id."-".$comment_ID.'">...'.__('Read More' , 'sip-reviews-shortcode').'</a>' : '';
 
    return apply_filters( 'get_comment_excerpt', $excerpt, $comment_ID, $comment );
}

function woocommerce_reviews_print_style3( $id = "" , $product_title = "" , $no_of_reviews = 5 ,  $product_names = false , $thumbs = false, $start_date = 0, $end_date = 0 ) {

	global $wpdb, $post;

	$product_title = filter_var ( $product_title, FILTER_SANITIZE_MAGIC_QUOTES );
	$comments_approved 	= sip_get_review_count( $id );
	?>

	<script>
	jQuery(document).ready(function($){

		var comments_approved = <?php echo $comments_approved ?>;
		var no_of_reviews	= <?php echo $no_of_reviews; ?>;

		if ( comments_approved <= no_of_reviews ) {
			$('#sip-rswc-more-btn-<?php echo $id ?>').hide();
		}

		$('#sip-rswc-more-btn-<?php echo $id ?>').click(function(e){

			var get_last_post_display = $("[id*='li-comments-<?php echo $id ?>-']").last().attr('id'); //get ip last <li>
			var limit			= <?php echo $no_of_reviews; ?>;
			var id 				= <?php echo $id ?>;
			var title 			= "<?php echo $product_title; ?>";
			var thumbs			= "<?php echo $thumbs; ?>";
			var product_names 	= "<?php echo $product_names; ?>";
			var start_date		= "<?php echo $start_date; ?>";
			var end_date 		= "<?php echo $end_date; ?>";

			$('#sip-rswc-more-btn-<?php echo $id ?>').html('<p align="center" class="load-mor-btn-load"><?php _e('Load More' , 'sip-reviews-shortcode');?> <img src="<?php echo SIP_RSWC_URL; ?>public/img/ajax-loader.gif" style="display: inline;"></p>');

			var data = {
				'action': 'more_post_style_three',
				'last_id_post': get_last_post_display, 
				'security' : '<?php $ajax_nonce = wp_create_nonce( "sip-rswc-style-three-load-more" ); echo $ajax_nonce; ?>',
				'limit': limit, 
				'id' : id, 
				'title' : title, 
				'thumbs' : thumbs, 
				'start_date' : start_date,
				'end_date' : end_date,
				'product_names' : product_names
			};

			$.post( sip_rswc_ajax.ajax_url, data ).done(function( html ) {
				$('ul.commentlist-<?php echo $id ?>').append(html);
				$("img.avatar").addClass("thumb");
				$('#sip-rswc-more-btn-<?php echo $id ?>').text("<?php _e('Load More' , 'sip-reviews-shortcode');?>"); //add text "Load More Post" to button again
				if( html == "" ) {
					$('#sip-rswc-more-btn-<?php echo $id ?>').text("<?php _e('No more comments' , 'sip-reviews-shortcode');?>"); // when last record add text "No more posts to load" to button.
				}
				if (!html.trim()) {
					// is empty or whitespace
					$('#sip-rswc-more-btn-<?php echo $id ?>').text("<?php _e('No more comments' , 'sip-reviews-shortcode');?>");
					// $('#sip-rswc-more').remove();
				}
			});

			e.preventDefault();
		});

		$('.sip-stars-rating').click(function(){

			var number 	= $(this).data("number");
			var id		= "<?php echo $id; ?>";
			var title 	= "<?php echo $product_title; ?>";
			var style	= 3;
			var thumbs	= "<?php echo $thumbs; ?>";
			var product_names = "<?php echo $product_names; ?>";
			var start_date	  = "<?php echo $start_date; ?>";
			var end_date 	  = "<?php echo $end_date; ?>";

			$('#sip-rswc-more-<?php echo $id ?>').html('<p align="center"><img src="<?php echo SIP_RSWC_URL; ?>public/img/ajax-loader.gif" ></p>');

			var data = {
				'action': 'more_post_rating', 
				'security' : '<?php $ajax_nonce = wp_create_nonce( "sip-rswc-more-post-rating" ); echo $ajax_nonce; ?>',
				'id' : id, 
				'title' : title,
				'style' : style ,
				'number': number,
				'thumbs' : thumbs,
				'start_date' : start_date,
				'end_date' : end_date,
				'product_names' : product_names
			};

			$.post( sip_rswc_ajax.ajax_url, data ).done(function( html ) {
				$('.woo-review-show-comments').hide();
				$('.woo-review-load-btn').hide();
				$('ul.commentlist-<?php echo $id ?>').append(html);
				$("img.avatar").addClass("thumb");
			});
		});
	});
   	</script>

	<?php

		$options 			= get_option( 'color_options' );
		$star_color 		= ( isset( $options['star_color'] ) ) ? sanitize_text_field( $options['star_color'] ) : '#d1c51d';
		$load_more_button 	= ( isset( $options['load_more_button'] ) ) ? sanitize_text_field( $options['load_more_button'] ) : '#dddddd';
		$load_more_text 	= ( isset( $options['load_more_text'] ) ) ? sanitize_text_field( $options['load_more_text'] ) : '#ffffff';

		$review_body_text_color 	= ( isset( $options['review_body_text_color'] ) ) ? sanitize_text_field( $options['review_body_text_color'] ) : '';
		$review_background_color 	= ( isset( $options['review_background_color'] ) ) ? sanitize_text_field( $options['review_background_color'] ) : '#f2f2f2';
		$review_title_color 		= ( isset( $options['review_title_color'] ) ) ? sanitize_text_field( $options['review_title_color'] ) : '#000000';

  		$button = 'style="';
  		if( $load_more_button != "" )
  			$button .= 'background-color:'. $load_more_button .';';
  		if( $load_more_text != "" )
  			$button .= 'color:'. $load_more_text .';';
			$button .= '"';

		  if( $review_title_color != "" )
  			$review_title_color = "style='color:". $review_title_color .";'";

			$review_background = 'style="';
  		if( $review_background_color != "" )
  			$review_background .= 'background-color:'. $review_background_color .';';
  		if( $review_body_text_color != "" )
  			$review_background .= 'color:'. $review_body_text_color .';';
  		$review_background .= '"';
		
			$query = $wpdb->prepare("SELECT c.* FROM {$wpdb->prefix}posts p, {$wpdb->prefix}comments c WHERE p.ID = %d AND p.ID = c.comment_post_ID AND c.comment_approved > 0 AND p.post_type = 'product' AND p.post_status = 'publish' AND p.comment_count > 0 ".($start_date ? " AND c.comment_date >= \"$start_date\" " : "" ) . ($end_date ? " AND c.comment_date <= \"$end_date\" " : "" ) ." AND c.comment_parent = 0 ORDER BY c.comment_ID DESC limit %d", $id, $no_of_reviews);
			$comments_products  = $wpdb->get_results($query, OBJECT);
			
			$out_reviews = "";
			if ( $comments_products ) {
				foreach ( $comments_products as $comment_product ) {
					$id_ 			= $comment_product->comment_post_ID;
					$name_author 	= $comment_product->comment_author;
					$comment_id  	= $comment_product->comment_ID;
					$comment_parent = $comment_product->comment_parent;
					$comment_date_ 	= get_comment_date( 'c', $comment_id );
					$comment_date  	= get_comment_date( wc_date_format(), $comment_id );
					$_product 		= wc_get_product( $id_ );
					$rating 		= intval( get_comment_meta( $comment_id, 'rating', true ) );
					$user_id	 	= $comment_product->user_id;
					$product_title 	= "";
					$votes 			= "";
					$avatar 		= "";
					$comment_chield	= "";
					$product_image	= "";
					$rating_value	= $rating;
					$comment_author_email = $comment_product->comment_author_email;
					$verified_customer = "";

					if( $product_names == true )
						$product_title = get_the_title( $id_ );

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
					$comments 			= get_comments($args);
					$comments_length 	= count($comments);
					$iteration    		= -1;
					$comment_parent_id 	= $comment_id;
					$sub_comment_ID 	= "";
					 
					do {

						if ( function_exists( 'get_avatar' ) ) {
							$avatar = get_avatar(get_comment_author_email($comment_id), 60,  null, null, array( 'class' => array( 'thumb' ) ) );
						} else {
							$grav_url = "http://www.gravatar.com/avatar.php?gravatar_id=
					   		" . md5($email) . "&default=" . urlencode($default) . "&size=" . $size;
							$avatar = "<img src='$grav_url' height='60px' width='60px' />";
						}
						
					if ( $comment_parent  > 0 ) {
						$comment_chield = " woo-review-show-comments-sub";
					}

					if( wc_customer_bought_product( $comment_author_email, $user_id, $id ) && get_option( 'sip-rswc-setting-verified-customer' ) ){
						$verified_customer = ' <em class="verified">'.__('(verified customer)' , 'sip-reviews-shortcode').'</em>';
					}

					if( user_can( $user_id, 'administrator' ) ) {
						$verified_customer = ' <em class="verified">'.__('(verified owner)' , 'sip-reviews-shortcode').'</em>';
					} else if ( user_can( $user_id, 'shop_manager' ) ) {
						$verified_customer = ' <em class="verified">'.__('(verified shop manager)' , 'sip-reviews-shortcode').'</em>';
					}

					$rating = $rating * 20 ;
					
					$out_reviews 	.= '<li itemprop="review" itemscope itemtype="http://schema.org/Review" id="li-comments-'.$id.'-'.$comment_parent_id.$sub_comment_ID.'" class="woo-review-show-comments '.$comment_chield.'" '.$review_background.'>'; 
											if ( get_option( 'sip-rswc-setting-link-to-comments' ) ) {
												$out_reviews	.= '<a href="'.get_permalink($id_).'#comment-'.$comment_parent_id.'" target="_blank">';
											}
							$out_reviews	.= $avatar.'
												<div class="woo-review-avatar-desc">';
								$out_reviews 	.=	'<div class="sip-reviews-up-box">
														<div class="sip-reviews-left-box">';
//										$out_reviews 	.=	'<span class="woo-review-name" '.$review_title_color.' itemprop="author">'.$name_author.' '.$verified_customer.'</span>
										$out_reviews 	.=	'<span class="woo-review-name" '.$review_title_color.' itemprop="author">'.$name_author.'</span>
															<time class="woo-review-date" '.$review_title_color.' itemprop="datePublished" datetime="'.$comment_date_.'">'.$comment_date.'</time>';
															if( $comment_parent == 0 ) {
																$out_reviews .=	'<span class="woo-review-rating"><span class="sip-score-star"><span style="width: '. $rating .'%"></span></span></span>';
																$out_reviews .=	'<div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" style="display:none;">
																					<span itemprop="ratingValue">'. $rating_value .'</span>
																				</div>';
															}
									$out_reviews 	.=	'</div>
														<div class="sip-reviews-right-box">';
															if( $thumbs == true ) {
																$out_reviews 	.= '<div class="sip-reviews-right-box-img"><img src="'.$product_image.'" alt="'.$product_title.'" title="'.$product_title.'"></div>';
															}
															if( $product_names == true ) {
																$out_reviews 	.= '<div class="sip-reviews-right-box-title"><h2 '.$review_title_color.'>'.$product_title.'</h2></div>';
															}
									$out_reviews 	.=	'</div>
													</div>
													<div style="clear:both;"></div>';

								if ( !get_option('sip-rswc-setting-limit-review-characters') ) {
									
									$out_reviews .='<div itemprop="description">
														<p class="woo-review-txt" style="color:'.$review_body_text_color.'">'.nl2br( get_comment_text( $comment_id ) ).'</p>
													</div>';
								} else {

									$out_reviews .=	'<div itemprop="description">
														<div class="hide-'.$id_."-".$comment_id.'">
															<p class="woo-review-txt" style="color:'.$review_body_text_color.'">'.nl2br( get_comment_excerpt_trim( $id_ , $comment_id ) ).'</p>
														</div>
														<p class="woo-review-txt comment-'.$id_."-".$comment_id.'-full" style="display:none;color:'.$review_body_text_color.'">'.nl2br( get_comment_text( $comment_id ) ).'</p>
													</div>';
								
								}

							$out_reviews 	.=	'</div>
												<div class="woo-clearfix"></div>';
										if ( get_option( 'sip-rswc-setting-link-to-comments' ) ) {
												$out_reviews 	.= '</a>';
											}
					$out_reviews	.='</li>';

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
							$name_author 	= $comments[$iteration]->comment_author;
						}
						if( !empty( $comments[$iteration]->comment_ID ) ) {
							$sub_comment_ID = "-".$comments[$iteration]->comment_ID;
							$comment_date_ 	= get_comment_date( 'c', $comments[$iteration]->comment_ID );
							$comment_date  	= get_comment_date( wc_date_format(), $comments[$iteration]->comment_ID );
						}
						if( !empty($comments[$iteration]->comment_ID )) {
							$comment_id 	= $comments[$iteration]->comment_ID;	
						}
					}
				} while ( $comments_length > $iteration );

			}//end of lop
		} //end of if condition
		if ( $out_reviews != '' ) {
			$out_reviews  = '<ul id="sip-commentlist-id-'. $id .'-'.$no_of_reviews.'" class="sip-woo-review-show-comments commentlist-'. $id .' commentlist_'. $id .'">' . $out_reviews . '</ul><div class="woo-clearfix"></div><!-- review bottom div end -->
							<div class="woo-review-load-btn">
								<a class="load-mor-btn" id="sip-rswc-more-btn-'. $id .'" href="#" '.$button.'>
									<strong>'.__('Load More' , 'sip-reviews-shortcode').'</strong>
								</a>
							</div>';
		} else {
			$out_reviews = '<ul class="sip-woo-review-show-comments commentlist "><li><p class="commentbox content-comment">'. __('No products reviews.' , 'sip-reviews-shortcode') . '</p></li></ul>';
		}

		echo $out_reviews;
 	}
add_shortcode ('woocommerce_reviews', 'sip_review_shortcode_wc' );


add_action( 'wp_head', function() {

	global $post;
	if( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'woocommerce_reviews') ) {

		$options 	= get_option( 'color_options' );
	  	$star_color = ( isset( $options['star_color'] ) ) ? sanitize_text_field( $options['star_color'] ) : '#d1c51d';
		$bar_color 		= ( isset( $options['bar_color'] ) ) ? sanitize_text_field( $options['bar_color'] ) : '#AD74A2';
		$accent_color 	= ( isset( $options['accent_color'] ) ) ? sanitize_text_field( $options['accent_color'] ) : '#32ddee';
		$review_body_text_color = ( isset( $options['review_body_text_color'] ) ) ? sanitize_text_field( $options['review_body_text_color'] ) : '';
		$review_background_color = ( isset( $options['review_background_color'] ) ) ? sanitize_text_field( $options['review_background_color'] ) : '#f2f2f2';
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

				.style4-review-description { 
					background-color: <?php echo $review_background_color; ?> ;
					color: <?php echo $review_body_text_color; ?>;
				}

				.style4-review-description:after {
					border-color: <?php echo $review_background_color; ?> transparent transparent transparent;
				}

				.sip-rswc-timer .sip-rswc-timer-half.right, .sip-rswc-timer .sip-rswc-timer-half.left { border: 10px solid <?php echo $bar_color; ?>; }
				.woo-review-container .woo-review-larg-hed { color : <?php echo $accent_color; ?>;}
				.woo-review-container .woo-review-h2 { border-bottom: 3px solid <?php echo $accent_color; ?>; }
				.woo-review-container li.woo-review-show-comments { border-left: 3px solid <?php echo $accent_color; ?>; }

	  		</style>
	  	<?php
	}
}, 10 );