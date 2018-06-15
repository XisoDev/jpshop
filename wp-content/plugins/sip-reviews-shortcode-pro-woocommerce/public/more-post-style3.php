<?php 
add_action( 'wp_ajax_more_post_style_three', 'more_post_style_three_callback' );
add_action( 'wp_ajax_nopriv_more_post_style_three', 'more_post_style_three_callback' );

function more_post_style_three_callback() {

	global $wpdb, $post; // this is how you get access to the database
	check_ajax_referer( 'sip-rswc-style-three-load-more', 'security' );
	$product_names	= sanitize_text_field( $_POST['product_names'] );
	$last_id_post	= sanitize_text_field( $_POST['last_id_post'] );
	$thumbs			= sanitize_text_field( $_POST['thumbs'] );
	$limit			= intval( $_POST['limit'] );
	$title 			= sanitize_text_field( $_POST['title'] );
	$id 			= intval( $_POST['id'] );
	$start_date 	= sanitize_text_field( $_POST['start_date'] );
	$end_date 		= sanitize_text_field( $_POST['end_date'] );

	$last_id_post = explode("-", $last_id_post);

	$options 			= get_option( 'color_options' );
  	$star_color 		= ( isset( $options['star_color'] ) ) ? sanitize_text_field( $options['star_color'] ) : '';
  	$load_more_button   = ( isset( $options['load_more_button'] ) ) ? sanitize_text_field( $options['load_more_button'] ) : '';
	$load_more_text 	= ( isset( $options['load_more_text'] ) ) ? sanitize_text_field( $options['load_more_text'] ) : '';

	$review_body_text_color  = ( isset( $options['review_body_text_color'] ) ) ? sanitize_text_field( $options['review_body_text_color'] ) : '';
	$review_background_color = ( isset( $options['review_background_color'] ) ) ? sanitize_text_field( $options['review_background_color'] ) : '';
	$review_title_color 	 = ( isset( $options['review_title_color'] ) ) ? sanitize_text_field( $options['review_title_color'] ) : '';

	$button = 'style="';
	if( $load_more_button != "" )
		$button .= 'background-color:'. $load_more_button .';';
	if( $load_more_text != "" )
		$button .= 'color:'. $load_more_text .';';
	$button .= '"';

  if( $review_title_color != "")
		$review_title_color = "style='color:". $review_title_color .";'";

	$review_background = 'style="';
	if( $review_background_color != "")
		$review_background .= 'background-color:'. $review_background_color .';';
	if( $review_body_text_color != "")
		$review_background .= 'color:'. $review_body_text_color .';';
	$review_background .= '"';

	$query = $wpdb->prepare("SELECT c.* FROM {$wpdb->prefix}posts p, {$wpdb->prefix}comments c WHERE p.ID = %d AND p.ID = c.comment_post_ID AND c.comment_approved > 0 AND p.post_type = 'product' AND p.post_status = 'publish' ".($start_date ? " AND c.comment_date >= \"$start_date\" " : "" ) . ($end_date ? " AND c.comment_date <= \"$end_date\" " : "" ) ." AND p.comment_count > 0 AND c.comment_ID < %d AND c.comment_parent = 0 ORDER BY c.comment_ID DESC limit %d", $id, $last_id_post[3], $limit);
	
	$comments_products 	= $wpdb->get_results($query, OBJECT);
			
	$out_reviews = "";
	if ( $comments_products ) {
		foreach ( $comments_products as $comment_product ) {
			$id_ 			= $comment_product->comment_post_ID;
			$name_author 	= $comment_product->comment_author;
			$comment_id  	= $comment_product->comment_ID;
			$comment_parent = $comment_product->comment_parent;
			$comment_date  	= get_comment_date( 'M d, Y', $comment_id );
			$_product 		= wc_get_product( $id_ );
			$rating 		= intval( get_comment_meta( $comment_id, 'rating', true ) );
			$rating 		= $rating * 20 ;
			$user_id	 	= $comment_product->user_id;
			$product_title 	= "";
			$votes 			= "";
			$avatar 		= "";
			$comment_chield	= "";
			$product_image	= "";
			$comment_author_email = $comment_product->comment_author_email;
			$verified_customer = "";
					
			if( $product_names == true )
				$product_title = get_the_title( $id_ );

			if( $thumbs == true ) {

				if ( has_post_thumbnail( $id_ ) ) {
					$product_image = wp_get_attachment_image_src( get_post_thumbnail_id( $id_ ), 'single-post-thumbnail' );
				}
				$product_image = $product_image[0];
			}

			$args = array(
			    'status' 	=> 'approve', 
			    'number' 	=> '100',
			    'post_id' 	=> $id_,
			    'order' 	=> 'ASC',
			    'parent' 	=> $comment_id
			);
			$comments 			= get_comments($args);
			$comments_length 	= count($comments);
			$iteration    		= -1;
			$comment_parent_id 	= $comment_id;
			 
			do {

				if ( function_exists( 'get_avatar' ) ) {
					$avatar = get_avatar(get_comment_author_email($comment_id), 60,  null, null, array( 'class' => array( 'thumb' ) ) );
				} else {
					$grav_url = "http://www.gravatar.com/avatar.php?gravatar_id=
				" . md5($email) . "&default=" . urlencode($default) . "&size=" . $size;
					$avatar = "<img src='$grav_url' height='60px' width='60px' />";
				}

				if( $comment_parent  > 0 ){
					$comment_chield = " woo-review-show-comments-sub";
				}

				if( wc_customer_bought_product( $comment_author_email, $user_id, $id ) && get_option( 'sip-rswc-setting-verified-customer' ) ){
					$verified_customer = ' <em class="verified">(verified customer)</em>';
				}
				
				if( user_can( $user_id, 'administrator' ) ) {
					$verified_customer = ' <em class="verified">(verified owner)</em>';
				} else if ( user_can( $user_id, 'shop_manager' ) ) {
					$verified_customer = ' <em class="verified">(verified shop manager)</em>';
				}

				$out_reviews 	.= '<li itemprop="review" itemscope="" itemtype="http://schema.org/Review" id="li-comments-'.$id.'-'.$comment_parent_id.'" class="woo-review-show-comments '.$comment_chield.'" '.$review_background.'>'; 
										if ( get_option( 'sip-rswc-setting-link-to-comments' ) ) {
											$out_reviews	.= '<a href="'.get_permalink($id_).'#comment-'.$comment_parent_id.'" target="_blank">';
										}
						$out_reviews	.= $avatar.'
											<div class="woo-review-avatar-desc">
												<div itemprop="itemReviewed" itemscope="" itemtype="http://schema.org/Product" style="display:none;">
													<img itemprop="image" src="'.$product_image.'" alt="'.$product_title.'" />
													<span itemprop="name">'.$product_title.'</span>
												</div>';
							$out_reviews 	.=	'<div class="sip-reviews-up-box">
													<div class="sip-reviews-left-box">';
									$out_reviews 	.=	'<span class="woo-review-name" '.$review_title_color.' itemprop="author">'.$name_author.' '.$verified_customer.'<span>
														<time class="woo-review-date" '.$review_title_color.' itemprop="datePublished" datetime="'.$comment_date.'">'.$comment_date.'</time>';
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
						$comment_date = get_comment_date( 'M d, Y', $comments[$iteration]->comment_ID );	
					}
					if( !empty($comments[$iteration]->comment_ID )) {
						$comment_id 	= $comments[$iteration]->comment_ID;	
					}	
				}
			} while ( $comments_length > $iteration );
												
		}//end of lop
	} //end of if condition
		
	echo $out_reviews;
	wp_die(); // this is required to terminate immediately and return a proper response
}