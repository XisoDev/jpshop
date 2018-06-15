<div class="wrap">
	<h2><?php echo __( 'Information of Our Support', 'woocommerce-for-japan' );?></h2>
	<p><b><?php echo __('Sorry, Mainly Japanese and some English support Only', 'woocommerce-for-japan');?></b></p>
	<div>
		<div class="wc4jp-informations metabox-holder">
	<?php
	//Display Setting Screen
	settings_fields( 'wc4jp_informations' );
	$this->wc4jp_setting->do_settings_sections( 'wc4jp_informations' );
?>
		</div>
		</form>
		<div class="clear"></div>
	</div>
	<script type="text/javascript">
	//<![CDATA[
	jQuery(document).ready( function ($) {
		// close postboxes that should be closed
		$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
		// postboxes setup
		postboxes.add_postbox_toggles('wc4jp-options');
	});
	//]]>
	</script>
	<p>
	<?php echo __('Nice to meet you on Facebook page!', 'woocommerce-for-japan');?><br />
	<a href="https://www.facebook.com/wcjapan" target="_blank"><?php echo __('Woo Japan Wave Facebook page!', 'woocommerce-for-japan');?></a>
	</p>
</div>
