<?php 
	global $woocommerce;
	if(isset($_GET['tab'])){
		$section = 'wc4jp_'.$_GET['tab'];
	}else{
		$section = 'wc4jp_setting';
		$_GET['tab'] = 'setting';
	}
	$title = array(
		'setting' => __( 'General Setting', 'woocommerce-for-japan' ),
		'shipment' => __( 'Shipment Setting', 'woocommerce-for-japan' ),
		'payment' => __( 'Payment Setting', 'woocommerce-for-japan' ),
	);
?>
<div class="wrap">
	<h2><?php echo  $title[$_GET['tab']];?></h2>
	<div class="wc4jp-settings metabox-holder">
		<div class="wc4jp-sidebar">
			<div class="wc4jp-credits">
				<h3 class="hndle"><?php echo __( 'WooCommerce for Japan', 'woocommerce-for-japan' ) . ' ' . WC4JP_VERSION;?></h3>
				<div class="inside">
					<?php $this->wc4jp_setting->wc4jp_support_notice('https://support.artws.info/forums/forum/wordpress-official/woocommerce-for-japan-plugins-forum/');?>
					<hr />
					<?php $this->wc4jp_setting->wc4jp_update_notice();?>
					<hr />
					<?php $this->wc4jp_setting->wc4jp_community_info();?>
					<?php if ( ! get_option( 'wc4jp_admin_footer_text_rated' ) ) :?>
					<hr />
					<h4 class="inner"><?php echo __( 'Do you like this plugin?', 'woocommerce-for-japan' );?></h4>
					<p class="inner"><a href="https://wordpress.org/support/plugin/woocommerce-for-japan/reviews/#postform" target="_blank" title="' . __( 'Rate it 5', 'woocommerce-for-japan' ) . '"><?php echo __( 'Rate it 5', 'woocommerce-for-japan' )?> </a><?php echo __( 'on WordPress.org', 'woocommerce-for-japan' ); ?><br />
					</p>
					<?php endif;?>
					<hr />
					<?php $this->wc4jp_setting->wc4jp_author_info();?>
				</div>
			</div>
		</div>
		<form id="wc4jp-setting-form" method="post" action="">
			<div id="main-sortables" class="meta-box-sortables ui-sortable">
<?php
	//Display Setting Screen
	settings_fields( $section );
	$this->wc4jp_setting->do_settings_sections( $section );
?>
			<p class="submit">
<?php
	submit_button( '', 'primary', 'save_'.$section, false );
?>
			</p>
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
		postboxes.add_postbox_toggles('<?php echo $section; ?>');
	});
	//]]>
	</script>
</div>