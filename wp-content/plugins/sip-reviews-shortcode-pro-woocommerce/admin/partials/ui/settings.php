<div class="settings-warp sip-tab-content" style="margin-top:10px;">
	<h2><?php _e('Settings' , 'sip-reviews-shortcode'); ?></h2>
	<form method="post" action="options.php">
		<?php settings_fields( 'sip-rswc-settings-group' ); ?>
		<table>
			<tr>
				<td><input id="sip-rswc-setting-customer-review" type="checkbox" name="sip-rswc-setting-customer-review" value="true" <?php echo esc_attr( get_option('sip-rswc-setting-customer-review', false))?' checked="checked"':''; ?> /></td>
				<td><?php _e('Accept reviews from customers only' , 'sip-reviews-shortcode');?></td>
			</tr>
			<tr>
				<td><input id="sip-rswc-setting-verified-customer" type="checkbox" name="sip-rswc-setting-verified-customer" value="true" <?php echo esc_attr( get_option('sip-rswc-setting-verified-customer', false))?' checked="checked"':''; ?> /></td>
				<td><?php _e('Show verified customer' , 'sip-reviews-shortcode');?></td>
			</tr>
			<tr>
				<td><input id="sip-rswc-setting-link-to-comments" type="checkbox" name="sip-rswc-setting-link-to-comments" value="true" <?php echo esc_attr( get_option('sip-rswc-setting-link-to-comments', false))?' checked="checked"':''; ?> /></td>
				<td><?php _e('Yes, I want to link to comments' , 'sip-reviews-shortcode');?></td>
			</tr>
			<tr>
				<td><input id="sip-rswc-setting-show-product-name" type="checkbox" name="sip-rswc-setting-show-product-name" value="true" <?php echo esc_attr( get_option('sip-rswc-setting-show-product-name', false))?' checked="checked"':''; ?> /></td>
				<td><?php _e('Yes, I want to show product name in aggregate reviews' , 'sip-reviews-shortcode');?></td>
			</tr>
			<tr>
				<td><input id="sip-rswc-setting-limit-review-characters" style="width:70px" type="number" name="sip-rswc-setting-limit-review-characters" value="<?php echo get_option('sip-rswc-setting-limit-review-characters'); ?>"  min="0"/></td>
				<td><?php _e('Limit review to number of characters. 0 mean complete review' , 'sip-reviews-shortcode');?></td>
			</tr>
			<tr>
				<td>
				<?php $rated_icons = ( (get_option('sip-rswc-setting-rated-icon') ) ? get_option('sip-rswc-setting-rated-icon') : "star" ) ?>
				<button type="button" class="sip-rswc-setting-rated-icons" name="sip-rswc-setting-rated-icons" id="sip-rswc-setting-rated-icons" value="<?php echo $rated_icons; ?>"><i class="fa fa-<?php echo $rated_icons; ?>"></i></button>

				<input type="hidden" class="sip-rswc-setting-rated-icon" name="sip-rswc-setting-rated-icon" value="<?php echo $rated_icons; ?>">
				</td>
				<td><?php _e('Select the icon to display in rateed area' , 'sip-reviews-shortcode');?></td>
			</tr>
		</table>
		<p><?php _e('When a new review is submitted via the form shortcode:' , 'sip-reviews-shortcode');?></p>
		<?php $options = get_option('sip-rswc-settings-radio'); ?>

		<label for="moderation_by_an_admin">
			<input id="moderation_by_an_admin" type="radio" name="sip-rswc-settings-radio[option_aproved]" value="0"<?php checked( '0' == $options['option_aproved'] ); ?> checked/> <?php _e('Hold for moderation by an admin' , 'sip-reviews-shortcode') ;?>
		</label><br />

		<label for="automatically_publish">
			<input id="automatically_publish" 	type="radio" name="sip-rswc-settings-radio[option_aproved]" value="1"<?php checked( '1' == $options['option_aproved'] ); ?> /> <?php _e('Automatically publish' , 'sip-reviews-shortcode');?>
		</label><br />
		<table>
			<tr>
				<td><input id="sip-rswc-notify-admin-reviews-submitted" type="checkbox" name="sip-rswc-notify-admin-reviews-submitted" value="true" <?php echo esc_attr( get_option('sip-rswc-notify-admin-reviews-submitted', false))?' checked="checked"':''; ?> /></td>
				<td><?php _e('Notify admin when new reviews are submitted' , 'sip-reviews-shortcode');?></td>
			</tr>
		</table>

		<?php submit_button(); ?>
	</form>
</div>