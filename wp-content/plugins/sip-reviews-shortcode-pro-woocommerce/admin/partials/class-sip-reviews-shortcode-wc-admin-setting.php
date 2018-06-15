<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://shopitpress.com
 * @since      1.0.4
 *
 * @package    SIP_Reviews_Shortcode
 * @subpackage SIP_Reviews_Shortcode/admin/partials
 */



	/**
	 * After loding this function global page show the admin panel
	 *
	 * @since    	1.0.0
	 */
	function sip_rswc_settings_page_ui() { ?>

		<div class="sip-tab-content">
		  <?php //get_screen_icon(); ?>
		  <h2><?php _e('Custom Color Settings' , 'sip-reviews-shortcode');?></h2>
		  <form id="wp-color-picker-options" action="options.php" method="post">
		    <?php color_input(); ?>
		    <?php settings_fields( 'wp_color_picker_options' ); ?>
		    <?php do_settings_sections( 'wp-color-picker-settings' ); ?>

		    <p class="submit">
		      <input id="wp-color-picker-submit" name="Submit" type="submit" class="button-primary" value="<?php _e( 'Save Color'  , 'sip-reviews-shortcode' ); ?>" />
		    </p>

		  </form>
		</div>
		
		<!-- settings -->
		<?php include( SIP_RSWC_DIR . 'admin/partials/ui/settings.php'); ?>
		<!-- affiliate/credit link -->
		<?php include( SIP_RSWC_DIR . 'admin/partials/ui/affiliate.php'); ?>
		<?php
	}

	/**
	 * Register settings, add a settings section, and add our color fields.
	 *
	 * @since    	1.0.0
	 */
	function sip_rswc_settings_init(){

	  register_setting(
	    'wp_color_picker_options',
	    'color_options',
	    'validate_options'
	  );
	}

	function validate_options( $input ){
	  $valid 							= array();
	  $valid['star_color'] 				= sanitize_text_field( $input['star_color'] );
	  $valid['bar_color'] 				= sanitize_text_field( $input['bar_color'] );
	  $valid['review_body_text_color'] 	= sanitize_text_field( $input['review_body_text_color'] );
	  $valid['review_background_color'] = sanitize_text_field( $input['review_background_color'] );
	  $valid['review_title_color'] 		= sanitize_text_field( $input['review_title_color'] );
	  $valid['load_more_button'] 		= sanitize_text_field( $input['load_more_button'] );
	  $valid['load_more_text'] 			= sanitize_text_field( $input['load_more_text'] );
		$valid['accent_color'] 			= sanitize_text_field( $input['accent_color'] );

	  return $valid;
	}


	function color_input(){
	  $options 					= get_option( 'color_options' );
	  $star_color 				= ( isset( $options['star_color'] ) ) ? sanitize_text_field( $options['star_color'] ) : '#d1c51d';
	  $bar_color 				= ( isset( $options['bar_color'] ) ) ? sanitize_text_field( $options['bar_color'] ) : '#AD74A2';
	  $review_body_text_color 	= ( isset( $options['review_body_text_color'] ) ) ? sanitize_text_field( $options['review_body_text_color'] ) : '#000000';
	  $review_background_color 	= ( isset( $options['review_background_color'] ) ) ? sanitize_text_field( $options['review_background_color'] ) : '#f2f2f2';
	  $review_title_color 		= ( isset( $options['review_title_color'] ) ) ? sanitize_text_field( $options['review_title_color'] ) : '#000000';
	  $load_more_button 		= ( isset( $options['load_more_button'] ) ) ? sanitize_text_field( $options['load_more_button'] ) : '#dddddd';
	  $load_more_text 			= ( isset( $options['load_more_text'] ) ) ? sanitize_text_field( $options['load_more_text'] ) : '#ffffff';
		$accent_color 			= ( isset( $options['accent_color'] ) ) ? sanitize_text_field( $options['accent_color'] ) : '#32ddee';

	 ?>
	<table>
		<tr>
			<td width="250"><strong><?php _e('Review stars' , 'sip-reviews-shortcode');?></strong></td>
			<td>
				<input id="star-color" name="color_options[star_color]" type="text" value="<?php echo $star_color ?>" />
	  		<div id="star-colorpicker"></div>
	  	</td>
		</tr>
		<tr>
			<td><strong><?php _e('Reviews bar summary' , 'sip-reviews-shortcode');?></strong></td>
			<td>
				<input id="bar-color" name="color_options[bar_color]" type="text" value="<?php echo $bar_color ?>" />
	  		<div id="bar-colorpicker"></div>
			</td>
		</tr>
		<tr>
			<td><strong><?php _e('Review background' , 'sip-reviews-shortcode');?></strong></td>
			<td>
				<input id="review-background-color" name="color_options[review_background_color]" type="text" value="<?php echo $review_background_color ?>" />
	  		<div id="review-background-colorpicker"></div>
			</td>
		</tr>
		<tr>
			<td><strong><?php _e('Review body text' , 'sip-reviews-shortcode');?></strong></td>
			<td>
				<input id="review-body-text-color" name="color_options[review_body_text_color]" type="text" value="<?php echo $review_body_text_color ?>" />
	  		<div id="review-body-text-colorpicker"></div>
			</td>
		</tr>
		<tr>
			<td><strong><?php _e('Review title' , 'sip-reviews-shortcode');?></strong></td>
			<td>
				<input id="review-title-color" name="color_options[review_title_color]" type="text" value="<?php echo $review_title_color ?>" />
	  		<div id="review-title-colorpicker"></div>
			</td>
		</tr>
		<tr>
			<td><strong><?php _e('Load more button background' , 'sip-reviews-shortcode');?></strong></td>
			<td>
				<input id="load-more-button-color" name="color_options[load_more_button]" type="text" value="<?php echo $load_more_button ?>" />
	  		<div id="load-more-button-colorpicker"></div>
			</td>
		</tr>

		<tr>
			<td><strong><?php _e('Load more button text' , 'sip-reviews-shortcode');?></strong></td>
			<td>
				<input id="load-more-button-text-color" name="color_options[load_more_text]" type="text" value="<?php echo $load_more_text ?>" />
	  		<div id="load-more-button-text-colorpicker"></div>
			</td>
		</tr>

		<tr>
			<td><strong><?php _e('Accent color' , 'sip-reviews-shortcode');?></strong></td>
			<td>
				<input id="accent-color-color" name="color_options[accent_color]" type="text" value="<?php echo $accent_color ?>" />
	  		<div id="accent-color-colorpicker"></div>
			</td>
		</tr>

	</table>
	 <?php
	}
	