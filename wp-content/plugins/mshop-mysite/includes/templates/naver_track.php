<?php

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( is_plugin_active( 'mshop-npay/mshop-npay.php' ) ) {
    return;
}

?>
<!-- 공통 적용 스크립트 , 모든 페이지에 노출되도록 설치. 단 전환페이지 설정값보다 항상 하단에 위치해야함 -->
<script type="text/javascript" src="//wcs.naver.net/wcslog.js"> </script>
<script type="text/javascript">
    if (!wcs_add) var wcs_add={};
    wcs_add["wa"] = "<?php echo $content_id['track_content'] ?>";
    if (!_nasa) var _nasa={};
    wcs.inflow();
    wcs_do(_nasa);
</script>
