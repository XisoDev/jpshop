<!-- 전환페이지 설정 -->
<script type="text/javascript" src="//wcs.naver.net/wcslog.js"></script>
<script type="text/javascript">
    var _nasa={};
    _nasa["cnv"] = wcs.cnv("1","<?php echo $order->get_total(); ?>"); // 전환유형, 전환가치 설정해야함. 설치매뉴얼 참고
    <?php
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    if ( is_plugin_active( 'mshop-npay/mshop-npay.php' ) ) {
	    ?>
        wcs_do(_nasa);
        <?php
    }
    ?>
</script>

