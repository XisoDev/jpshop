<?php
add_action( 'wp_ajax_more_post_aggregate_rating', 'more_post_aggregate_rating_callback' );
add_action( 'wp_ajax_nopriv_more_post_aggregate_rating', 'more_post_aggregate_rating_callback' );

function more_post_aggregate_rating_callback() {

	global $wpdb, $post;
	check_ajax_referer( 'sip-rswc-more-post-aggregate-rating', 'security' );
	$number = intval( $_POST['number'] );
	$style	= sanitize_text_field( $_POST['style'] );
	$id 	= sanitize_text_field( $_POST['id'] );
	$title 	= sanitize_text_field( $_POST['title'] );
	$product_names 	= sanitize_text_field( $_POST['product_names'] );
	$thumbs = sanitize_text_field( $_POST['thumbs'] );
	$start_date 	= sanitize_text_field( $_POST['start_date'] );
	$end_date 		= sanitize_text_field( $_POST['end_date'] );
	$id = stripslashes(html_entity_decode($id));
	$ids = json_decode($id, true);

	$out_reviews 	  = "";
	$options 		  = get_option( 'color_options' );
	$star_color 	  = ( isset( $options['star_color'] ) ) ? sanitize_text_field( $options['star_color'] ) : '#d1c51d';
	$load_more_text   = ( isset( $options['load_more_text'] ) ) ? sanitize_text_field( $options['load_more_text'] ) : '#ffffff';
	$load_more_button = ( isset( $options['load_more_button'] ) ) ? sanitize_text_field( $options['load_more_button'] ) : '#dddddd';
	$review_body_text_color  = ( isset( $options['review_body_text_color'] ) ) ? sanitize_text_field( $options['review_body_text_color'] ) : '#000000';
	$review_background_color = ( isset( $options['review_background_color'] ) ) ? sanitize_text_field( $options['review_background_color'] ) : '#f2f2f2';
	$review_title_color 	 = ( isset( $options['review_title_color'] ) ) ? sanitize_text_field( $options['review_title_color'] ) : '#000000';

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

	$query = "SELECT c.* FROM {$wpdb->prefix}posts p LEFT JOIN {$wpdb->prefix}comments c ON p.ID = c.comment_post_ID LEFT JOIN {$wpdb->prefix}commentmeta cm ON c.comment_ID = cm.comment_id WHERE p.ID IN ({$aggregate_ids}) AND cm.meta_key = 'rating' AND cm.meta_value = {$number}  AND c.comment_approved > 0 AND p.post_type = 'product' AND p.post_status = 'publish' AND p.comment_count > 0 ".($start_date ? " AND c.comment_date >= \"$start_date\" " : "" ) . ($end_date ? " AND c.comment_date <= \"$end_date\" " : "" ) ." AND c.comment_parent = 0 GROUP BY cm.comment_id ORDER BY c.comment_ID DESC";

	$comments_products = $wpdb->get_results($query, OBJECT);
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
				'status'	=> 'approve',
				'number'	=> '100',
				'post_id'	=> $id_,
				'order'		=> 'ASC',
				'parent'	=> $comment_id
			);

			$comments 			= get_comments($args);
			$comments_length 	= count($comments);
			$iteration 			= -1;
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
				$verified_customer = ' <em class="verified">(verified customer)</em>';
			}

			if( user_can( $user_id, 'administrator' ) ) {
				$verified_customer = ' <em class="verified">(verified owner)</em>';
			} else if ( user_can( $user_id, 'shop_manager' ) ) {
				$verified_customer = ' <em class="verified">(verified shop manager)</em>';
			}

			$out_reviews 	.= '<li itemprop="review" itemscope="" itemtype="http://schema.org/Review" id="li-comment-'.$id.'-'.$comment_parent_id.'" class="show-everthing ShowEve '.$comment_chield.'">';

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
														$out_reviews .=	'<div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" style="display:none;">
																			<span itemprop="ratingValue">'.$rating.'</span>
																		</div>';
													}

									$out_reviews .=	'<p class="sip-author" '.$review_title_color.'>
														<strong itemprop="author">'.$name_author.'</strong>'.$verified_customer.' – <time itemprop="datePublished" datetime="'.$comment_date_.'">'.$comment_date.'</time>
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
	$out_reviews  = $out_reviews;

} else {
	$out_reviews = '<li><p class="commentbox content-comment">'. __('No products reviews.' , 'sip-reviews-shortcode') . '</p></li>';
}

echo $out_reviews;
wp_die(); // this is required to terminate immediately and return a proper response
}



add_action( 'wp_ajax_filter_post_aggregate_rating', 'filter_post_aggregate_rating_callback' );
add_action( 'wp_ajax_nopriv_filter_post_aggregate_rating', 'filter_post_aggregate_rating_callback' );

function filter_post_aggregate_rating_callback() {

	global $wpdb, $post;
	check_ajax_referer( 'sip-rswc-filter-post-aggregate-rating', 'security' );
	$style	= sanitize_text_field( $_POST['style'] );
	$ids 	= ( isset( $_POST['id'] ) ? $_POST['id'] : array( ) );
	$title 	= sanitize_text_field( $_POST['title'] );
	$thumbs = sanitize_text_field( $_POST['thumbs'] );
	$end_date = sanitize_text_field( $_POST['end_date'] );
	$start_date = sanitize_text_field( $_POST['start_date'] );
	$no_of_reviews = sanitize_text_field( $_POST['number_of_reviews'] );
	$product_names 	= sanitize_text_field( $_POST['product_names'] );
	$verified_owners = ( isset( $_POST['verified_owners'] ) ? sanitize_text_field( $_POST['verified_owners'] ) : array( ) );
	$rating_filter_stars = ( isset( $_POST['rating_filter_stars'] ) ? $_POST['rating_filter_stars'] : array( ) );

	$ids = array_map( 'sanitize_text_field', $ids );
	$rating_filter_stars = array_map( 'sanitize_text_field', $rating_filter_stars );

	if (empty($no_of_reviews)) {
		$no_of_reviews = 10;
	}

	$rating_filter_stars = implode (",", $rating_filter_stars);

	if ($start_date) {
		$date=date_create($start_date);
		$start_date = date_format($date,"Y-m-d H:i:s");
	}

	if ($end_date) {
		$date=date_create($end_date);
		$end_date = date_format($date,"Y-m-d H:i:s");
	}

	$out_reviews 	  = "";
	$options 		  = get_option( 'color_options' );
	$star_color 	  = ( isset( $options['star_color'] ) ) ? sanitize_text_field( $options['star_color'] ) : '#d1c51d';
	$load_more_text   = ( isset( $options['load_more_text'] ) ) ? sanitize_text_field( $options['load_more_text'] ) : '#ffffff';
	$load_more_button = ( isset( $options['load_more_button'] ) ) ? sanitize_text_field( $options['load_more_button'] ) : '#dddddd';
	$review_body_text_color  = ( isset( $options['review_body_text_color'] ) ) ? sanitize_text_field( $options['review_body_text_color'] ) : '#000000';
	$review_background_color = ( isset( $options['review_background_color'] ) ) ? sanitize_text_field( $options['review_background_color'] ) : '#f2f2f2';
	$review_title_color 	 = ( isset( $options['review_title_color'] ) ) ? sanitize_text_field( $options['review_title_color'] ) : '#000000';

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

	$query = "SELECT c.* FROM {$wpdb->prefix}posts p LEFT JOIN {$wpdb->prefix}comments c ON p.ID = c.comment_post_ID LEFT JOIN {$wpdb->prefix}commentmeta cm ON c.comment_ID = cm.comment_id WHERE p.ID IN ({$aggregate_ids}) AND cm.meta_key = 'rating' ".($rating_filter_stars ? " AND cm.meta_value IN ({$rating_filter_stars}) " : "" ) ." AND c.comment_approved > 0 AND p.post_type = 'product' AND p.post_status = 'publish' AND p.comment_count > 0 ".($start_date ? " AND c.comment_date >= \"$start_date\" " : "" ) . ($end_date ? " AND c.comment_date <= \"$end_date\" " : "" ) ." AND c.comment_parent = 0 GROUP BY cm.comment_id ORDER BY c.comment_ID DESC";

	$comments_products = $wpdb->get_results($query, OBJECT);
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
			$product_title 	= $title;
			$votes 			= "";
			$avatar 		= "";
			$comment_chield	= "";
			$product_image	= "";
			$comment_author_email = $comment_product->comment_author_email;
			$verified_customer = "";

			$verified = get_comment_meta( $comment_id, 'verified', true );
			if ( ( $verified < 1 || $verified == "" ) && ( $verified_owners == "on" ) ) {
				continue;
			}

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
				'status'	=> 'approve',
				'number'	=> '100',
				'post_id'	=> $id_,
				'order'		=> 'ASC',
				'parent'	=> $comment_id
			);

			$comments 			= get_comments($args);
			$comments_length 	= count($comments);
			$iteration 			= -1;
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
				$verified_customer = ' <em class="verified">(verified customer)</em>';
			}

			if( user_can( $user_id, 'administrator' ) ) {
				$verified_customer = ' <em class="verified">(verified owner)</em>';
			} else if ( user_can( $user_id, 'shop_manager' ) ) {
				$verified_customer = ' <em class="verified">(verified shop manager)</em>';
			}

			$out_reviews 	.= '<li itemprop="review" itemscope="" style="display:none;" itemtype="http://schema.org/Review" id="li-comment-'.$id.'-'.$comment_parent_id.'" class="show-everthing ShowEve '.$comment_chield.'">';

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
														$out_reviews .=	'<div itemprop="reviewRating" itemscope itemtype="http://schema.org/Rating" style="display:none;">
																			<span itemprop="ratingValue">'.$rating.'</span>
																		</div>';
													}

									$out_reviews .=	'<p class="sip-author" '.$review_title_color.'>
														<strong itemprop="author">'.$name_author.'</strong>'.$verified_customer.' – <time itemprop="datePublished" datetime="'.$comment_date_.'">'.$comment_date.'</time>
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

$colleted_id = 0;

if ( $out_reviews != '' ) {
	
	foreach ($ids as $id) {
		$colleted_id .= $id;
	}

	$colleted_id = substr($colleted_id, 0, 20);
	$out_reviews  = $out_reviews;

} else {
	$out_reviews = '<li><p class="commentbox content-comment">'. __('No products reviews.' , 'sip-reviews-shortcode') . '</p></li>';
}


	$out_reviews  = '<ul id="comments_list_'.$colleted_id.'" class="commentbox comments_list commentlist commentlist-'. $id .' commentlist_'. $id .'">' . $out_reviews . '</ul><button '. $button .' class="sip-rswc-more" id="sip-rswc-more-'.$colleted_id.'" type="button">'.__('Load More' , 'sip-reviews-shortcode').'</button>

<script>
					jQuery(document).ready(function () {
						size_li_'.$colleted_id.' = jQuery("#comments_list_'.$colleted_id.' li").size();
						x_'.$colleted_id.' = '.$no_of_reviews.';
						jQuery("#comments_list_'.$colleted_id.' li:lt("+x_'. $colleted_id.'+")").show();
						if( size_li_'.$colleted_id.' <= x_'.$colleted_id.' ){
							jQuery("#sip-rswc-more-'.$colleted_id.'").hide();
						}
						jQuery("#sip-rswc-more-'.$colleted_id.'").click(function () {
							x_'.$colleted_id.' = ( x_'. $colleted_id.' + '. $no_of_reviews.' <= size_li_'. $colleted_id.' ) ? x_'. $colleted_id.' + '. $no_of_reviews.' : size_li_'. $colleted_id.' ;
							jQuery("#comments_list_'. $colleted_id.' li:lt("+ x_'. $colleted_id.' +")").show();
							if( x_'. $colleted_id.' == size_li_'. $colleted_id.' ){
								jQuery("#sip-rswc-more-'.$colleted_id.'").hide();
							}
						});
					});
				</script>
	';

echo $out_reviews;
wp_die(); // this is required to terminate immediately and return a proper response
}

?>