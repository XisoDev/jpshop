<?php

global $woocommerce;
$site_code = getSiteOrderCode();
print_r($site_code);
?>

<h3><?php echo __( '상품데이터 커스터마이징', 'amuz-japanshop' );?></h3>

<div class="metabox-holder">
    <div class="postbox-container">
    <div class="postbox">
        <h2 class="hndle"><span>야후재팬</span></h2>
        <div class="inside">
        <form id="wc-amuz-japanshop-yahoojapan" method="post" action="../wp-content/plugins/amuz-japanshop/actions/yahoo.php" enctype="multipart/form-data">
            <h5>상품데이터 추출</h5>
            <input type="hidden" id="data_action_type" name="action" value="" />
            <?php wp_nonce_field( 'my-nonce-key','wc-am-jp-datacenter');?>
            페이지당 <input type="text" name="wc-amuz-japanshop-list_count" value="1000" size="5" /> 건 /
            <input type="text" name="wc-amuz-japanshop-page" value="1" size="3" /> 페이지
            <input type="submit" onclick="return jQuery('#data_action_type').val('save');" value="다운로드" class="button action" />
        </form>

        <form id="wc-amuz-japanshop-yahoojapan_thumbnail" target="hidden_frame_thumbnail" method="post" action="/wp-content/plugins/amuz-japanshop/actions/yahoo_thumbnail_creator.php" enctype="multipart/form-data">
            <h5>썸네일 생성</h5>
            <input type="hidden" id="data_action_type" name="action" value="" />
            <?php wp_nonce_field( 'my-nonce-key','wc-am-jp-datacenter');?>
            Prefix : <input type="text" name="wc-amuz-japanshop-prefix" value="<?=$site_code["fullname"]?>" size="15" />
            제한할용량 : <input type="text" name="wc-amuz-japanshop-limit_storage" value="25" size="5" /> MB
            <input type="submit" onclick="return jQuery('#hidden_frame_thumbnail').show();" value="생성" class="button action" />
        </form>
        <iframe id="hidden_frame_thumbnail" name="hidden_frame_thumbnail" src="about:blank" style="display:none;" frameborder="none" width="100%" height="300" scrolling="no"></iframe>
        </div>
    </div>
    </div>


    <div class="postbox-container" style="margin-left : 10%;">
        <div class="postbox">
            <h2 class="hndle"><span> WEAR 엑셀 변환</span></h2>
            <div class="inside">
                <form id="wc-amuz-japanshop-wear" method="post" action="../wp-content/plugins/amuz-japanshop/actions/wear_csv_read.php" enctype="multipart/form-data">
                    <h5>상품데이터 wear 업로드 파일로 변환 ( 페이지당 최대 50건 )</h5>
                    <input type="hidden" id="data_action_type" name="action" value="" />
                    <?php wp_nonce_field( 'my-nonce-key','wc-am-jp-datacenter');?>
                    페이지당 <input type="text" name="wear-list-count" value="50" size="5" /> 건 /
                    <input type="text" name="wear-list-page" value="1" size="3" /> 페이지
                    <h5>상품파일 업로드</h5>
                    <input type="file" name="upfile" id="upfile" >
                    <input type="hidden" name="site_code" value="<?=$site_code["fullname"]?>">
                    <input type="submit" id="upload" value="다운로드" class="button action" />
                    <br>
                </form>
                </div>
            </div>
        </div>
</div>