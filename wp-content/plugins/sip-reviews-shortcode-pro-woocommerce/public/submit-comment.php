<?php 

add_action( 'wp_ajax_submit_comment', 'submit_comment_callback' );
add_action( 'wp_ajax_nopriv_submit_comment', 'submit_comment_callback' );

function submit_comment_callback() {

	check_ajax_referer( 'sip-rswc-form-submit', 'security' );

	$id 		= intval($_POST['id']);
	$name 		= sanitize_text_field($_POST['name']);
	$email 		= sanitize_email($_POST['email']);
	$rating		= sanitize_text_field($_POST['rating']);
	$comment 	= sanitize_textarea_field($_POST['comment']);
	$author_id 	= 0;

	$current_user = wp_get_current_user();

	if ($name == 0 && $email == 0 && $current_user->ID != 0) {
		$name = $current_user->display_name;
		$email 		= $current_user->user_email;
		$author_id 	= $current_user->ID;
	}

	if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}

	$comment_approved = 0;
	$options = get_option('sip-rswc-settings-radio');
	if( '1' == $options['option_aproved'] ) { 
		$comment_approved = 1;
	}

	$time = current_time('mysql');
	$data = array(
		'comment_post_ID'		=> $id,
		'comment_author'		=> $name,
		'comment_author_email'	=> $email,
		'comment_author_url'	=> '',
		'comment_content'		=> $comment,
		'comment_type'			=> '',
		'comment_parent'		=> 0,
		'user_id'				=> $author_id,
		'comment_author_IP'		=> $ip,
		'comment_agent'			=> $_SERVER['HTTP_USER_AGENT'],
		'comment_date'			=> $time,
		'comment_approved'		=> $comment_approved
	);

	$comment_id = wp_insert_comment($data);
	add_comment_meta( $comment_id, 'rating', $rating );

	if ( get_option('sip-rswc-notify-admin-reviews-submitted') ) {
		$to = get_option( 'admin_email' );
		$admin_url = admin_url('comment.php?action=editcomment&c='.$comment_id);
		$subject = "New review submitted on ";
		$subject .= get_option( 'blogname' );
		$message = "Dear admin,\r\n";
		$message .= "A new review was submitted on your site.\r\n";
		$message .= "View now ( ".$admin_url." )";
		wp_mail( $to, $subject, $message );
	}
	wp_die(); // this is required to terminate immediately and return a proper response
}
?>