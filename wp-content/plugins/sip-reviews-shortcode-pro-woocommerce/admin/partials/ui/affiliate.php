<div class="sip-credit-affiliate-link-warp">
  <h2><?php _e('Be awesome' , 'sip-reviews-shortcode'); ?></h2>
  <p><?php _e('Do you like this plugin? Would you like to see even more great features? Please be awesome and help us maintain and develop free plugins by checking the option below' , 'sip-reviews-shortcode');?></p>
	
	<form method="post" action="options.php">
	  <?php settings_fields( 'sip-rswc-affiliate-settings-group' ); ?>
	  <?php $options = get_option('sip-rswc-affiliate-radio'); ?>
			<label><input  id="spc-rswc-affiliate-checkbox" type="checkbox" name="sip-rswc-affiliate-check-box" value="true" <?php echo esc_attr( get_option('sip-rswc-affiliate-check-box', false))?' checked="checked"':''; ?> /> <?php _e('Yes, I want to help development of this plugin' , 'sip-reviews-shortcode');?></label><br />
			<div id="spc-rswc-diplay-affiliate-toggle">

				<label><input id="spc-rswc-discreet-credit" type="radio" name="sip-rswc-affiliate-radio[option_three]" value="value1"<?php checked( 'value1' == $options['option_three'] ); ?> checked/> <?php _e('Add a discreet credit' , 'sip-reviews-shortcode') ;?></label><br />
				<label><input id="spc-rswc-affiliate-link" 	type="radio" name="sip-rswc-affiliate-radio[option_three]" value="value2"<?php checked( 'value2' == $options['option_three'] ); ?> /> <?php _e('Add my affiliate link' , 'sip-reviews-shortcode');?></label><br />
				<div id="spc-rswc-affiliate-link-box">
					<label><input type="text" name="sip-rswc-affiliate-affiliate-username" value="<?php echo esc_attr( get_option('sip-rswc-affiliate-affiliate-username')) ?>" /> <?php _e('Input affiliate username/ID' , 'sip-reviews-shortcode');?></label><br />
				</div>
            <p class="sip-text"><?php _e('Make money recommending our plugins. Register for an affiliate account at' , 'sip-reviews-shortcode');?> <a href="https://shopitpress.com/affiliate-area/?utm_source=wordpress.org&amp;utm_medium=affiliate&amp;utm_campaign=sip-reviews-shortcode-woocommerce" target="_blank">Shopitpress</a>.
            </p>
					</a>
			</div>
		<?php submit_button(); ?>
	</form>
</div>

<script type="text/javascript">
	jQuery(document).ready(function(){

		jQuery("#spc-rswc-diplay-affiliate-toggle").hide();
		jQuery("#spc-rswc-affiliate-link-box").hide();

		if (jQuery('#spc-rswc-affiliate-checkbox').is(":checked"))
		{
		  jQuery("#spc-rswc-diplay-affiliate-toggle").show('slow');
		}

		jQuery('#spc-rswc-affiliate-checkbox').click(function() {
		  jQuery('#spc-rswc-diplay-affiliate-toggle').toggle('slow');
		})

		if (jQuery('#spc-rswc-affiliate-link').is(":checked"))
		{
		  jQuery("#spc-rswc-affiliate-link-box").show('slow');
		}

		jQuery('#spc-rswc-affiliate-link').click(function() {
		  jQuery('#spc-rswc-affiliate-link-box').show('slow');
		})

		jQuery('#spc-rswc-discreet-credit').click(function() {
		  jQuery('#spc-rswc-affiliate-link-box').hide('slow');
		})

	});
</script>
