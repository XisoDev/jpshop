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
function sip_review_id_style_all( $review_id, $style = "", $product_title, $product_names = false, $thumbs = false , $description = "", $total_review_score = "", $offers = "", $review = "", $sku = "", $icon = "" ) {

	global $wpdb;
	$options 	= get_option( 'color_options' );
	$star_color = ( isset( $options['star_color'] ) ) ? sanitize_text_field( $options['star_color'] ) : '#d1c51d';
	$bar_color 	= ( isset( $options['bar_color'] ) ) ? sanitize_text_field( $options['bar_color'] ) : '#AD74A2';
	
	if( $star_color != "")
		$star_color = "style='color:". $star_color .";'";

	if( $bar_color != "")
		$bar_color = "background-color:".$bar_color .";";
	ob_start();
	// to get the detail of the comments etc aproved and panding status
	?>

	<!--Wrapper: Start -->
		<div class="sip-rswc-wrapper <?php echo $icon; ?>"> 
		  	<!--Main Container: Start -->
		  	<div class="main-container">
		    	<aside class="page-wrap" id="product-<?php echo $review_id[0]; ?>">
		      		<!--Tabs: Start -->
					<aside class="tabs-wrap">
						<div class="page-wrap">
							<div class="tabs-content">
							<?php $review_count = count( $review_id ); ?>
							<?php if ( $review_count == 1 ) { ?>
							<?php
								$comment_id = get_comment( $review_id[0] ); 
				    			$comment_post_id = $comment_id->comment_post_ID;
				    			$image = "";

								$comments_approved 	= sip_get_review_count( $comment_post_id );
				    		?>
							<?php $get_the_title = get_the_title( $comment_post_id ); ?>
							<?php if (has_post_thumbnail( $comment_post_id ) ) { ?>

							<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $comment_post_id ), 'single-post-thumbnail' ); ?>
							<?php } ?>
							<div itemscope itemtype="http://schema.org/Product" id="product-<?php echo $comment_post_id; ?>">
								<div style="display:none;">
									<span itemprop="name"><?php echo $get_the_title; ?></span>
									<a href="<?php echo $image[0]; ?>" itemprop="image"><?php echo $get_the_title; ?></a>
									<meta itemprop="url" content="<?php echo get_permalink( $comment_post_id ); ?>">
									<?php if ( $total_review_score != "no" ) { ?>
										<div class="star_container" itemprop="aggregateRating" itemscope="" itemtype="http://schema.org/AggregateRating">
											<span itemprop="ratingValue"><?php echo sip_get_avg_rating( $comment_post_id ); ?></span>
											<span itemprop="bestRating">5</span>
											<span itemprop="reviewcount" style="display:none;"><?php echo $comments_approved ?></span>
											<span itemprop="ratingCount"><?php echo $comments_approved ?></span>
										</div>
									<?php } ?>
									<?php if ( $offers != "no" ) { ?>
										<div itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
											<span itemprop="priceCurrency" content="<?php $currency = get_woocommerce_currency(); echo $currency; ?>"><?php echo get_woocommerce_currency_symbol($currency) ?></span>
											<?php $get_price = sip_get_price( $comment_post_id ); ?>
											<span itemprop="price" content="<?php echo $get_price; ?>"><?php echo get_woocommerce_currency_symbol(); echo $get_price; ?></span>
											<link itemprop="availability" href="http://schema.org/InStock">
										</div>
									<?php } ?>
									<?php
										$content_post = get_post( $comment_post_id );
										$content = $content_post->post_content;
										$product_ = wc_get_product( $comment_post_id );
					  					$sku_ = $product_->get_sku();
									?>
									<?php if ( $sku != "no" ) { ?>
										<span style="display:none;" itemprop="sku"><?php echo $sku_; ?></span>
									<?php } ?>
									<?php if ( $description != "no" ) { ?>
										<span itemprop="description"><?php echo $content ?></span>
									<?php } ?>
								</div><!-- end itemscope -->
							<?php } ?>
							<?php woocommerce_id_reviews( $review_id, $product_title, $style, $product_names, $thumbs, $review ); ?> 
							<?php if ( $review_count == 1 ) { ?>
								</div>
							<?php } ?>	
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
	}


	/**
	 * To give complete list of comments in ul tag, it ie printing the all data of li
	 *
	 * @since    	1.0.0
	 * @return 		string , mixed html string in $out_reviews
	 */
	function woocommerce_id_reviews( $id = "" , $title = "" , $style = "" , $product_names = false , $thumbs = false, $review = "" ) {

		global $wpdb, $post;
		$title = filter_var ( $title, FILTER_SANITIZE_MAGIC_QUOTES );
		$review_count = count( $id );

		$options = get_option( 'color_options' );
		$star_color = ( isset( $options['star_color'] ) ) ? sanitize_text_field( $options['star_color'] ) : '#d1c51d';
		$load_more_button = ( isset( $options['load_more_button'] ) ) ? sanitize_text_field( $options['load_more_button'] ) : '#dddddd';
		$load_more_text = ( isset( $options['load_more_text'] ) ) ? sanitize_text_field( $options['load_more_text'] ) : '#ffffff';

		$review_body_text_color	= ( isset( $options['review_body_text_color'] ) ) ? sanitize_text_field( $options['review_body_text_color'] ) : '#000000';
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
		
		$loop = 0;
		$id_ = "";
		foreach ( $id as $val ) {

			if( $loop!= 0 ) {
				$id_ .= ",";
			}

			$id_ .= $val ;
			$loop++;
		}

		$query = "SELECT c.* FROM {$wpdb->prefix}posts p, {$wpdb->prefix}comments c WHERE c.comment_ID IN ($id_) AND p.ID = c.comment_post_ID AND c.comment_approved > 0 AND p.post_type = 'product' AND p.post_status = 'publish' AND p.comment_count > 0 AND c.comment_parent = 0";
		$comments_products = $wpdb->get_results($query, OBJECT);

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
					$comment_author_email = $comment_product->comment_author_email;
					$verified_customer = "";

					if( $product_names == true )
						$product_title 	=		get_the_title( $id_ );

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

					$itemprop_review = "";
					$itemprop_author = "";
					$itemprop_description = "";
					$itemprop_datePublished = "";
					if ( $review != "no" ) {
						$itemprop_review = 'itemprop="review" itemscope itemtype="http://schema.org/Review"';
						$itemprop_description = 'itemprop="description"';
						$itemprop_author = 'itemprop="author"';
						$itemprop_datePublished = 'itemprop="datePublished"';
					}

					$out_reviews 	.= '<li '. ( ($review_count == 1 ) ? "{$itemprop_review}" : "" ).' id="li-comment-'.$id_.'-'.$comment_parent_id.'" class="show-everthing ShowEve '.$comment_chield.'">';

											if ( get_option( 'sip-rswc-setting-link-to-comments' ) ) {
												$out_reviews .= '<a href="'.get_permalink($id_).'#comment-'.$comment_parent_id.'" target="_blank">';
											}
							
							$out_reviews 	.=	'<div class="comment-borderbox" '.$review_background.'> 
													'.$avatar ;

								$out_reviews 	.=	'<div class="sip-reviews-up-box">
														<div class="sip-reviews-left-box">';

															if( $comment_parent == 0 ) {
																$out_reviews .=	'<div class="br-wrapper br-theme-fontawesome-stars"><p class="sip-star-rating" style="display:none;">'.$rating.'</p><select class="rating-readonly-'.$rating.'"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option></select></div>';
															}
									$out_reviews 	.=	'<p class="sip-author" '.$review_title_color.'>
															<strong '. ( ($review_count == 1 ) ? "{$itemprop_author}" : "" ).' >'.$name_author.'</strong>'.$verified_customer.' – <time '. ( ($review_count == 1 ) ? "{$itemprop_datePublished}" : "" ).' datetime="'.$comment_date_.'">'.$comment_date.'</time>
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
									
									$out_reviews .='<div '. ( ($review_count == 1 ) ? "{$itemprop_description}" : "" ).'>
														<p style="color:'.$review_body_text_color.'">'.nl2br( get_comment_text( $comment_id ) ).'</p>
													</div>';
								} else {

									$out_reviews .=	'<div '. ( ($review_count == 1 ) ? "{$itemprop_description}" : "" ).'>
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
			$out_reviews  = '<ul class="commentbox commentlist commentlist-'. $id_ .' commentlist_'. $id_ .'">' . $out_reviews . '</ul>';
		} else {
			$out_reviews = '<ul class="commentlist"><li><p class="commentbox content-comment">'. __('No products reviews.' , 'sip-reviews-shortcode') . '</p></li></ul>';
		}
		echo $out_reviews;
	}

function sip_review_id_style_3( $review_id, $product_title, $product_names = false , $thumbs = false , $description = "", $total_review_score = "", $offers = "", $review = "" , $sku = "", $icon = "" ) { 
	ob_start();

	global $wpdb;
	$options 		= get_option( 'color_options' );
	$star_color 	= ( isset( $options['star_color'] ) ) ? sanitize_text_field( $options['star_color'] ) : '#d1c51d';
	$bar_color 		= ( isset( $options['bar_color'] ) ) ? sanitize_text_field( $options['bar_color'] ) : '#AD74A2';
	$accent_color 	= ( isset( $options['accent_color'] ) ) ? sanitize_text_field( $options['accent_color'] ) : '#32ddee';

	if( $star_color != "")
		$star_color = "style='color:". $star_color .";'";

	if( $bar_color != "")
		$bar_color = "background-color:".$bar_color .";"; ?>

	<div class="woo-review-container <?php echo $icon; ?>">
		<?php
		$review_count = count( $review_id );
		if ( $review_count == 1 ) {

			$comment_id = get_comment( $review_id[0] ); 
			$comment_post_id = $comment_id->comment_post_ID;
			$image = "";

			$comments_approved 	= sip_get_review_count( $comment_post_id );
				?>

			<?php if (has_post_thumbnail( $comment_post_id ) ) { ?>
				<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $comment_post_id ), 'single-post-thumbnail' ); ?>
			<?php } ?>
			<?php $get_the_title = get_the_title( $comment_post_id ); ?>

			<div itemscope itemtype="http://schema.org/Product" id="product-<?php echo $comment_post_id; ?>">
				<div style="display:none;">
					<span itemprop="name"><?php echo $get_the_title; ?></span>
					<a href="<?php echo $image[0]; ?>" itemprop="image"><?php echo $get_the_title; ?></a>
					<meta itemprop="url" content="<?php echo get_permalink( $comment_post_id ); ?>" />
					<?php if ( $total_review_score != "no" ) { ?>
						<div class="star_container" itemprop="aggregateRating" itemscope="" itemtype="http://schema.org/AggregateRating">
							<span itemprop="ratingValue"><?php echo sip_get_avg_rating( $comment_post_id ); ?></span>
							<span itemprop="bestRating">5</span>
							<span itemprop="reviewcount" style="display:none;"><?php echo $comments_approved ?></span>
							<span itemprop="ratingCount"><?php echo $comments_approved ?></span>
						</div>
					<?php } ?>
					<?php if ( $offers != "no" ) { ?>
						<div itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
							<span itemprop="priceCurrency" content="<?php $currency = get_woocommerce_currency(); echo $currency; ?>"><?php echo get_woocommerce_currency_symbol($currency) ?></span>
							<?php $get_price = sip_get_price( $comment_post_id ); ?>
							<span itemprop="price" content="<?php echo $get_price; ?>"><?php echo get_woocommerce_currency_symbol(); echo $get_price; ?></span>
							<link itemprop="availability" href="http://schema.org/InStock" />
						</div>
					<?php } ?>
					<?php
					$content_post = get_post( $comment_post_id );
					$content = $content_post->post_content;
					$product_ = wc_get_product( $comment_post_id );
					$sku_ = $product_->get_sku();
					?>
					<?php if ( $sku != "no" ) { ?>
						<span style="display:none;" itemprop="sku"><?php echo $sku_; ?></span>
					<?php } ?>
					<?php if ( $description != "no" ) { ?>
						<span itemprop="description"><?php echo $content ?></span>
					<?php } ?>
				</div><!-- display none -->
				<?php } ?>
				<?php woocommerce_reviews_id_style3( $review_id, $product_title, $product_names, $thumbs, $review ); ?>
		<?php if ( $review_count == 1 ) { ?>
			</div><!-- end itemscope -->
		<?php } ?>
		
		<div class="woo-clearfix"></div>
	</div><!-- /.woo-review-container -->
	<?php 
	return ob_get_clean();
}//end function


function woocommerce_reviews_id_style3( $id = "" , $product_title = "" ,  $product_names = false , $thumbs = false, $review = "" ) {
	
	global $wpdb, $post;
	$product_title = filter_var ( $product_title, FILTER_SANITIZE_MAGIC_QUOTES );

		$review_count = count( $id );
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
		
		$loop = 0;
		$id_ = "";
		foreach ( $id as $val ) {

			if( $loop!= 0 ) {
				$id_ .= ",";
			}

			$id_ .= $val ;
			$loop++;
		}
			
			$query = "SELECT c.* FROM {$wpdb->prefix}posts p, {$wpdb->prefix}comments c WHERE c.comment_ID IN ({$id_}) AND p.ID = c.comment_post_ID AND c.comment_approved > 0 AND p.post_type = 'product' AND p.post_status = 'publish' AND p.comment_count > 0 AND c.comment_parent = 0";
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
					// $rating_html 	= $_product->get_rating_html( $rating );
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
					 
					do {
						
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

					$itemprop_review = "";
					$itemprop_author = "";
					$itemprop_description = "";
					$itemprop_datePublished = "";
					$itemprop_image = "";
					if ( $review != "no" ) {
						$itemprop_review = 'itemprop="review" itemscope itemtype="http://schema.org/Review"';
						$itemprop_description = 'itemprop="description"';
						$itemprop_author = 'itemprop="author"';
						$itemprop_datePublished = 'itemprop="datePublished"';
						$itemprop_image = 'itemprop="image"';
					}
					

					if ( function_exists( 'get_avatar' ) ) {
						$avatar = get_avatar(get_comment_author_email($comment_id), 60,  null, null, array( 'class' => array( 'thumb' ) ) );
					} else {
						$grav_url = "http://www.gravatar.com/avatar.php?gravatar_id=
				   		" . md5($email) . "&default=" . urlencode($default) . "&size=" . $size;
						$avatar = "<img {$itemprop_image} src='$grav_url' height='60px' width='60px' />";
					}

					$out_reviews 	.= '<li '. ( ($review_count == 1 ) ? "{$itemprop_review}" : "" ).' id="li-comments-'.$id_.'-'.$comment_parent_id.'" class="woo-review-show-comments '.$comment_chield.'" '.$review_background.'>'; 
											if ( get_option( 'sip-rswc-setting-link-to-comments' ) ) {
												$out_reviews	.= '<a href="'.get_permalink($id_).'#comment-'.$comment_parent_id.'" target="_blank">';
											}
							$out_reviews	.= $avatar.'
												<div class="woo-review-avatar-desc">';
								$out_reviews 	.=	'<div class="sip-reviews-up-box">
														<div class="sip-reviews-left-box">';
										$out_reviews 	.=	'<span class="woo-review-name" '. ( ($review_count == 1 ) ? "{$itemprop_author}" : "" ).' '.$review_title_color.'>'.$name_author.' '.$verified_customer.'</span>
															<time class="woo-review-date" '. ( ($review_count == 1 ) ? "{$itemprop_datePublished}" : "" ).' '.$review_title_color.' datetime="'.$comment_date_.'">'.$comment_date.'</time>';
															if( $comment_parent == 0 ) {
																$out_reviews .=	'<span class="woo-review-rating"><span class="sip-score-star"><span style="width: '. $rating .'%"></span></span></span>';

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
									
									$out_reviews .='<div '. ( ($review_count == 1 ) ? "{$itemprop_description}" : "" ).'>
														<p class="woo-review-txt" style="color:'.$review_body_text_color.'">'.nl2br( get_comment_text( $comment_id ) ).'</p>
													</div>';
								} else {

									$out_reviews .=	'<div '. ( ($review_count == 1 ) ? "{$itemprop_description}" : "" ).'>
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
			$out_reviews  = '<ul class="sip-woo-review-show-comments commentlist-'. $id_ .' commentlist_'. $id_ .'">' . $out_reviews . '</ul><div class="woo-clearfix"></div><!-- review bottom div end -->';
		} else {
			$out_reviews = '<ul class="sip-woo-review-show-comments commentlist "><li><p class="commentbox content-comment">'. __('No products reviews.' , 'sip-reviews-shortcode') . '</p></li></ul>';
		}

		echo $out_reviews;
	}

function sip_review_id_carousel_style( $review_id, $product_title, $product_names = false , $thumbs = false, $description = "", $total_review_score = "", $offers = "", $review = "" , $sku = "", $icon = "" ) { 
	ob_start();

	$options 		= get_option( 'color_options' );
	$star_color 	= ( isset( $options['star_color'] ) ) ? sanitize_text_field( $options['star_color'] ) : '#d1c51d';
	$bar_color 		= ( isset( $options['bar_color'] ) ) ? sanitize_text_field( $options['bar_color'] ) : '#AD74A2';
	$accent_color 	= ( isset( $options['accent_color'] ) ) ? sanitize_text_field( $options['accent_color'] ) : '#32ddee';
	$review_body_text_color 	= ( isset( $options['review_body_text_color'] ) ) ? sanitize_text_field( $options['review_body_text_color'] ) : '';
	$review_background_color 	= ( isset( $options['review_background_color'] ) ) ? sanitize_text_field( $options['review_background_color'] ) : '#f2f2f2';

	if( $star_color != "")
		$star_color = "style='color:". $star_color .";'";

	if( $bar_color != "")
		$bar_color = "background-color:".$bar_color .";";

			?>
			<div class="style4-wrap">
				<div class="sip-carousel <?php echo $icon; ?>">

				<?php $review_count = count( $review_id ); ?>
				<?php if ( $review_count == 1 ) { ?>
				<?php
					$comment_id = get_comment( $review_id[0] ); 
					$comment_post_id = $comment_id->comment_post_ID;
					$image = "";

					$comments_approved 	= sip_get_review_count( $comment_post_id );
				?>

				<?php if (has_post_thumbnail( $comment_post_id ) ) { ?>

				<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( $comment_post_id ), 'single-post-thumbnail' ); ?>
				<?php } ?>
				<?php $get_the_title = get_the_title( $comment_post_id ); ?>
				<div itemscope itemtype="http://schema.org/Product" id="product-<?php echo $comment_post_id; ?>">
					<div style="display:none;">
						<span itemprop="name"><?php echo $get_the_title; ?></span>
						<a href="<?php echo $image[0]; ?>" itemprop="image"><?php echo $get_the_title; ?></a>
						<meta itemprop="url" content="<?php echo get_permalink( $comment_post_id ); ?>">
						<?php if ( $total_review_score != "no" ) { ?>
							<div class="star_container" itemprop="aggregateRating" itemscope="" itemtype="http://schema.org/AggregateRating">
								<span itemprop="ratingValue"><?php echo sip_get_avg_rating( $comment_post_id ); ?></span>
								<span itemprop="bestRating">5</span>
								<span itemprop="reviewcount" style="display:none;"><?php echo $comments_approved ?></span>
								<span itemprop="ratingCount"><?php echo $comments_approved ?></span>
							</div>
						<?php } ?>
						<?php if ( $offers != "no" ) { ?>
							<div itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
								<span itemprop="priceCurrency" content="<?php $currency = get_woocommerce_currency(); echo $currency; ?>"><?php echo get_woocommerce_currency_symbol($currency) ?></span>
								<?php $get_price = sip_get_price( $comment_post_id ); ?>
								<span itemprop="price" content="<?php echo $get_price; ?>"><?php echo get_woocommerce_currency_symbol(); echo $get_price; ?></span>
								<link itemprop="availability" href="http://schema.org/InStock">
							</div>
						<?php } ?>
						<?php
							$content_post = get_post( $comment_post_id );
							$content = $content_post->post_content;
							$product_ = wc_get_product( $comment_post_id );
							$sku_ = $product_->get_sku();
						?>
						<?php if ( $sku != "no" ) { ?>
							<span style="display:none;" itemprop="sku"><?php echo $sku_; ?></span>
						<?php } ?>
						<?php if ( $description != "no" ) { ?>
							<span itemprop="description"><?php echo $content ?></span>
						<?php } ?>
					</div><!-- end itemscope -->
				<?php } ?>

				<?php sip_review_id_carousel_style_print( $review_id, $product_title, $product_names, $thumbs, $review ); ?>
				<?php if ( $review_count == 1 ) { ?>
					</div>
				<?php } ?>
				</div>
				<div class="woo-clearfix"></div>
			</div><!-- /.style4-wrap -->
		<?php 
	return ob_get_clean();
}//end function


function sip_review_id_carousel_style_print( $id = "" , $product_title = "" ,  $product_names = false , $thumbs = false, $review = "" ) {
	
	global $wpdb, $post;
	$product_title = filter_var ( $product_title, FILTER_SANITIZE_MAGIC_QUOTES );
	$review_count = count( $id );

		$options = get_option( 'color_options' );
		$star_color = ( isset( $options['star_color'] ) ) ? sanitize_text_field( $options['star_color'] ) : '#d1c51d';
		$load_more_button = ( isset( $options['load_more_button'] ) ) ? sanitize_text_field( $options['load_more_button'] ) : '#dddddd';
		$load_more_text = ( isset( $options['load_more_text'] ) ) ? sanitize_text_field( $options['load_more_text'] ) : '#ffffff';
		$review_title_color = ( isset( $options['review_title_color'] ) ) ? sanitize_text_field( $options['review_title_color'] ) : '#000000';
		$review_body_text_color	= ( isset( $options['review_body_text_color'] ) ) ? sanitize_text_field( $options['review_body_text_color'] ) : '#000000';

		$button = 'style="';
		if( $load_more_button != "" )
			$button .= 'background-color:'. $load_more_button .';';
		if( $load_more_text != "" )
			$button .= 'color:'. $load_more_text .';';
			$button .= '"';

		if( $review_title_color != "" )
			$review_title_color = "style='color:". $review_title_color .";'";

		$loop = 0;
		$id_ = "";
		foreach ( $id as $val ) {

			if( $loop!= 0 ) {
				$id_ .= ",";
			}

			$id_ .= $val ;
			$loop++;
		}
	
		$query = "SELECT c.* FROM {$wpdb->prefix}posts p, {$wpdb->prefix}comments c WHERE c.comment_ID IN ($id_) AND p.ID = c.comment_post_ID AND c.comment_approved > 0 AND p.post_type = 'product' AND p.post_status = 'publish' AND p.comment_count > 0 AND c.comment_parent = 0";
		$comments_products = $wpdb->get_results($query, OBJECT);

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
					'status' => 'approve', 
					'number' => '100',
					'post_id' => $id_,
					'order' => 'ASC',
					'parent' => $comment_id
				);
				$comments 			= get_comments($args);
				$comments_length 	= count($comments);
				$iteration			= -1;
				$comment_parent_id 	= $comment_id;

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

				$itemprop_review = "";
				$itemprop_author = "";
				$itemprop_description = "";
				$itemprop_datePublished = "";
				if ( $review != "no" ) {
					$itemprop_review = 'itemprop="review" itemscope itemtype="http://schema.org/Review"';
					$itemprop_description = 'itemprop="description"';
					$itemprop_author = 'itemprop="author"';
					$itemprop_datePublished = 'itemprop="datePublished"';
				}

				$out_reviews .= '<div '. ( ($review_count == 1 ) ? "{$itemprop_review}" : "" ).'  class="row-style-4 '.$comment_chield.'" id="li-comments-'.$id_.'-'.$comment_parent_id.'">';
									if ( get_option( 'sip-rswc-setting-link-to-comments' ) ) {
										$out_reviews	.= '<a href="'.get_permalink($id_).'#comment-'.$comment_parent_id.'" target="_blank">';
									}
					$out_reviews .= '<div class="col-style-4">
										<div class="style4-review-description">';

											if ( !get_option('sip-rswc-setting-limit-review-characters') ) {

								$out_reviews .='<div '. ( ($review_count == 1 ) ? "{$itemprop_description}" : "" ).'>
													<p class="woo-review-txt">'.nl2br( get_comment_text( $comment_id ) ).'</p>
												</div>';
											} else {

								$out_reviews .=	'<div '. ( ($review_count == 1 ) ? "{$itemprop_description}" : "" ).'>
													<div class="hide-'.$id_."-".$comment_id.'">
														<p class="woo-review-txt">'.nl2br( get_comment_excerpt_trim( $id_ , $comment_id ) ).'</p>
													</div>
													<p class="woo-review-txt comment-'.$id_."-".$comment_id.'-full" style="display:none;color:'.$review_body_text_color.'">'.nl2br( get_comment_text( $comment_id ) ).'</p>
												</div>';

											}

						$out_reviews .= '</div>
										<div class="style4-review-author">
											<div class="style4-avator">'.$avatar.'</div>
											<p '. ( ($review_count == 1 ) ? "{$itemprop_author}" : "" ).' '.$review_title_color.'>'.$name_author.' '.$verified_customer.'</p>

											<time '. ( ($review_count == 1 ) ? "{$itemprop_datePublished}" : "" ).' '.$review_title_color.' datetime="'.$comment_date_.'">'.$comment_date.'</time>';

											if( $comment_parent == 0 ) {
												$out_reviews .=	'<span class="woo-review-rating"><span class="sip-score-star"><span style="width: '. $rating .'%"></span></span></span>';
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
	if ( $out_reviews == '' ) {
		$out_reviews = '<ul class="sip-woo-review-show-comments commentlist "><li><p class="commentbox content-comment">'. __('No products reviews.' , 'sip-reviews-shortcode') . '</p></li></ul>';
	}

	echo $out_reviews;
}