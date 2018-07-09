<div class="wrap woocommerce">
    <h2 class="nav-tab-wrapper woo-nav-tab-wrapper">
        <a href="<?php echo admin_url('admin.php?page=wc4amuz_japanshop_datacenter_output') ?>" class="nav-tab <?php echo ($tab == 'data') ? 'nav-tab-active' : ''; ?>"><?php echo __( '커스텀 주문데이터', 'amuz-japanshop' )?></a>
        <a href="<?php echo admin_url('admin.php?page=wc4amuz_japanshop_datacenter_output&tab=product') ?>" class="nav-tab <?php echo ($tab == 'product') ? 'nav-tab-active' : ''; ?>"><?php echo __( '상품데이터 추출', 'amuz-japanshop' )?></a>
        <a href="<?php echo admin_url('admin.php?page=wc4amuz_japanshop_datacenter_output&tab=calculate') ?>" class="nav-tab <?php echo ($tab == 'calculate') ? 'nav-tab-active' : ''; ?>"><?php echo __( '정산', 'amuz-japanshop' )?></a>
        <a href="<?php echo admin_url('admin.php?page=wc4amuz_japanshop_datacenter_output&tab=calculate-edit') ?>" class="nav-tab <?php echo ($tab == 'calculate-edit') ? 'nav-tab-active' : ''; ?>"><?php echo __( '정산 편집', 'amuz-japanshop' )?></a>
    </h2>
	<?php
		switch ($tab) {
			case "product" :
                include( 'html-admin-product-screen.php' );
			break;

            case "calculate" :
                include( 'html-admin-calculate-screen.php' );
                break;

            case "calculate-edit" :
                include( 'html-admin-calculate-edit-screen.php' );
                break;

			default :
                include( 'html-admin-datacenter-screen.php' );
			break;

		}
	?>
</div>
