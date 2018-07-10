<div class="wrap woocommerce">
    <h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
        <a href="<?php echo admin_url('admin.php?page=wc4amuz_japanshop_datacenter_output') ?>" class="nav-tab <?php echo ($tab == 'data') ? 'nav-tab-active' : ''; ?>"><?php echo __( '커스텀 주문데이터', 'amuz-japanshop' )?></a>
        <a href="<?php echo admin_url('admin.php?page=wc4amuz_japanshop_datacenter_output&tab=product') ?>" class="nav-tab <?php echo ($tab == 'product') ? 'nav-tab-active' : ''; ?>"><?php echo __( '상품데이터 추출', 'amuz-japanshop' )?></a>
    </h2>
	<?php
		switch ($tab) {
			case "product" :
                include( 'html-admin-product-screen.php' );
			break;
			default :
                include( 'html-admin-datacenter-screen.php' );
			break;

		}
	?>
</div>
