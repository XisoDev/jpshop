<?php

/**
 * To give complete list of comments in ul tag, it ie printing the all data of li
 *
 * @since    	1.2.6
 * @return 		string , mixed html string in $out_reviews
 */

function sip_review_carousel_style( $id = "", $title = "", $icon = "", $product_names = false, $thumbs = false , $start_date = 0, $end_date = 0, $limit = 0 ) {

	ob_start();
	$product_title = $title;
	?>

	<div itemscope itemtype="http://schema.org/Product" id="product-<?php echo $id; ?>">
		<?php $get_avg_rating = sip_get_avg_rating( $id ); ?>
		<?php $get_review_count = sip_get_review_count( $id ); ?>
		<?php $get_price = sip_get_price($id); ?>
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
		</div><!-- end display:none -->

		<div class="style4-wrap">
			<div class="sip-carousel <?php echo $icon; ?>">
				<?php sip_review_carousel_style_print( $id, $title, $icon, $product_names, $thumbs, $start_date, $end_date, $limit); ?>
			</div>
	 		<div class="woo-clearfix"></div>
	 	</div><!-- /.style4-wrap -->
	</div><!-- end of itemscop -->
	<?php
	return ob_get_clean();
}

function  sip_review_carousel_style_print( $id = "", $title = "", $icon = "", $product_names = false, $thumbs = false , $start_date = 0, $end_date = 0, $limit = 0 ) {

	global $wpdb, $post;
	$title = filter_var ( $title, FILTER_SANITIZE_MAGIC_QUOTES );

	$options = get_option( 'color_options' );
	$review_body_text_color	 = ( isset( $options['review_body_text_color'] ) ) ? sanitize_text_field( $options['review_body_text_color'] ) : '#000000';
	$review_title_color = ( isset( $options['review_title_color'] ) ) ? sanitize_text_field( $options['review_title_color'] ) : '#000000';
	$review_title_color = 'style="color:'.$review_title_color.'"';

	$query = $wpdb->prepare("SELECT c.* FROM {$wpdb->prefix}posts p, {$wpdb->prefix}comments c WHERE p.ID = %d AND p.ID = c.comment_post_ID AND c.comment_approved > 0 AND p.post_type = 'product' AND p.post_status = 'publish' AND p.comment_count > 0  ".($start_date ? " AND c.comment_date >= \"$start_date\" " : "" ) . ($end_date ? " AND c.comment_date <= \"$end_date\" " : "" ) ." AND c.comment_parent = 0 ORDER BY c.comment_ID DESC ". ($limit ? " limit $limit " : "" ), $id );

	$comments_products 	= 	$wpdb->get_results($query, OBJECT);

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
				// $rating_html 	= $_product->get_rating_html( $rating );
				$user_id	 	= $comment_product->user_id;
				$product_title 	= "";
				$votes 			= "";
				$avatar 		= "";
				$comment_chield	= "";
				$product_image	= "";
				$comment_author_email = $comment_product->comment_author_email;
				$verified_customer = "";
				$rating_value	= $rating;

				if( $product_names == true )
					$product_title 	= get_the_title( $id_ );

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
				    'status'  	=> 'approve', 
				    'number'  	=> '100',
				    'post_id' 	=> $id_,
				    'order' 	=> 'ASC',
				    'parent' 	=> $comment_id
				);
				$comments 			= get_comments($args);
				$comments_length 	= count($comments);
				$iteration    		= -1;
				$comment_parent_id 	= $comment_id;
				$sub_comment_ID = "";
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
					
					$out_reviews .= '<div class="row-style-4 '.$comment_chield.'" itemprop="review" itemscope itemtype="http://schema.org/Review" id="li-comments-'.$id_.'-'.$comment_parent_id.$sub_comment_ID.'">';
										if ( get_option( 'sip-rswc-setting-link-to-comments' ) ) {
											$out_reviews	.= '<a href="'.get_permalink($id_).'#comment-'.$comment_parent_id.'" target="_blank">';
										}
	        			$out_reviews .= '<div class="col-style-4">
	          								<div class="style4-review-description">';

												if ( !get_option('sip-rswc-setting-limit-review-characters') ) {
									
									$out_reviews .='<div itemprop="description">
														<p class="woo-review-txt">'.nl2br( get_comment_text( $comment_id ) ).'</p>
													</div>';
												} else {

									$out_reviews .=	'<div itemprop="description">
														<div class="hide-'.$id_."-".$comment_id.'">
															<p class="woo-review-txt">'.nl2br( get_comment_excerpt_trim( $id_ , $comment_id ) ).'</p>
														</div>
														<p class="woo-review-txt comment-'.$id_."-".$comment_id.'-full" style="display:none;color:'.$review_body_text_color.'">'.nl2br( get_comment_text( $comment_id ) ).'</p>
													</div>';
												}

					       $out_reviews .= '</div>
					          				<div class="style4-review-author">
												<div class="style4-avator">'.$avatar.'</div>
												<p '.$review_title_color.' itemprop="author">'.$name_author.' '.$verified_customer.'</p>
												<time '.$review_title_color.' itemprop="datePublished" datetime="'.$comment_date_.'">'.$comment_date.'</time>';

												if( $comment_parent == 0 ) {
													$out_reviews .=	'<span class="woo-review-rating"><span class="sip-score-star"><span style="width: '. $rating .'%"></span></span></span>';
													$out_reviews .=	'<div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" style="display:none;">
																		<span itemprop="ratingValue">'. $rating_value .'</span>
																	</div>';
												}

							$out_reviews .= '</div>';

							$out_reviews .=	'<div class="sip-reviews-right-box">';
							if( $thumbs == true ) {
								$out_reviews .= '<div class="sip-reviews-right-box-img"><img src="'.$product_image.'" alt="'.$product_title.'" title="'.$product_title.'"></div>';
							}
							
							if( $product_names == true ) {
								$out_reviews .= '<div class="sip-reviews-right-box-title"><h2 '.$review_title_color.'>'.$product_title.'</h2></div>';
							}

							$out_reviews .=	'</div>
										</div>';
							if ( get_option( 'sip-rswc-setting-link-to-comments' ) ) {
								$out_reviews 	.= '</a>';
							}

						$out_reviews .= '<script>
											jQuery(".comment-'.$id_."-".$comment_id.'").click(function(){
												jQuery(".comment-'.$id_."-".$comment_id.'-full").show();
												jQuery(".hide-'.$id_."-".$comment_id.'").hide();
											});
										</script>';
    				$out_reviews .= '</div>';

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
				// } while ( $comments_length > $iteration );
				} while ( 0 );

		}//end of lop
	} //end of if condition
	if ( $out_reviews == '' ) {
		$out_reviews = '<ul class="commentlist"><li><p class="commentbox content-comment">'. __('No products reviews.' , 'sip-reviews-shortcode') . '</p></li></ul>';
	}
	echo $out_reviews;
}