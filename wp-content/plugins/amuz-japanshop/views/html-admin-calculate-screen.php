<style>
    th.minus{
        background-color : #ff7373;
    }
    th.plus{
            background-color : #99ffff;
    }
    td.minus{
        background-color : #ffe0eb;
    }
    td.plus{
        background-color: #ccffff;
    }

</style>
<?php

global $woocommerce ;
include __DIR__.'./../define_arrays.php';
$site_code = getSiteOrderCode();

//데이터가 들어오는것과 상관없이 일단 쿠키를 세션에 집어넣음.
$date_list = array(
    "start_date" => "검색 시작일",
);
$status_list = array(
    "wc-completed" => "완료",
    "wc-refunded" => "환불",
);

foreach ($date_list as $key => $value) {
    $date_method_str = 'wc-amuz-japanshop-' . $key;
    $_SESSION[$date_method_str] = $_COOKIE[$date_method_str];
}

if( isset( $_POST['wc-am-jp-datacenter'] ) && $_POST['wc-am-jp-datacenter'] ) {
    if (check_admin_referer('my-nonce-key', 'wc-am-jp-datacenter')) {
        //All payment method setting
        foreach ($status_list as $key => $value) {
            $status_method_str = 'wc-amuz-japanshop-' . $key;
            if (isset($_POST[$status_method_str]) && $_POST[$status_method_str]) {
                update_option($status_method_str, $_POST[$status_method_str]);
            } else {
                update_option($status_method_str, 'Y');
            }
        }
        foreach ($date_list as $key => $value) {
            $date_method_str = 'wc-amuz-japanshop-' . $key;
            if (isset($_POST[$date_method_str]) && $_POST[$date_method_str]) {
                setcookie($date_method_str, $_POST[$date_method_str], time() + 86400 * 7);
                $_SESSION[$date_method_str] = $_POST[$date_method_str];
            } else {
                setcookie($date_method_str, null, -1);
                $_SESSION[$date_method_str] = false;
            }

        }
    }
}

//기타변수처리
if(isset($_POST['wc-amuz-japanshop-list_count'])) $_SESSION['wc-amuz-japanshop-list_count'] = $_POST['wc-amuz-japanshop-list_count'];

//엑셀에서 받아온 row수, 배열
$num = $_POST['number'];
$x2 = unserialize(urldecode($_POST['view']));

?>

<form method="post" id="excelupload" name="excelupload" action="../wp-content/plugins/amuz-japanshop/actions/readfile.php" enctype="multipart/form-data" >
    <div align="right" id="test_div" style="display: none">
        <input type="file" name="upfile" id="upfile" >
        <input type="submit" id="upload" value="변경" >
        </div>
    <div align="right">
    <input type="button" id="excelup" value="청구 배송료 추가" onclick="div_show();"/>
    </div>
</form>
<form id="wc-amuz-japanshop-datacenter-form" method="post" action="" enctype="multipart/form-data">
    <?php wp_nonce_field( 'my-nonce-key','wc-am-jp-datacenter');?>
    <h3><?php echo __( '정산표', 'amuz-japanshop' );?></h3>

    <div class="tablenav top">
        <div class="alignleft actions bulkactions">
            <label for="bulk-action-selector-top" class="screen-reader-text">일괄 작업 선택</label>
            <select name="action" id="bulk-action-selector-top" onchange="jQuery('#data_action_type').val(jQuery(this).val());">
                <option value="calculate">정산표</option>
            </select>

        </div>
        <div class="alignleft actions">
            <?php

            //조회일 지정
            foreach ($date_list as $key => $value) {
                $date_method_str = "wc-amuz-japanshop-".$key;
                echo '<input type="month" name="' . $date_method_str . '" value="'.$_SESSION[$date_method_str].'" /> &nbsp; ';
            }
            //조회할 상태지정
            $post_status = array();
            $date_arr = array();

            foreach ($status_list as $key => $value) {
                $status_method_str = "wc-amuz-japanshop-".$key;
                $options = get_option($status_method_str);
                if($options == "Y") $post_status[] = $key;
                echo '<label for="woocommerce_input_'.$key.'">';
                echo '</label> &nbsp; ';
            }
            ?>
            <input name="save" class="button-primary" type="submit" value="조회">
        </div>
        <?php
        //페이지네이션을 위해 쿼리를 미리 실행
/*
        $currentYear = date('Y');
        $currentMonth = date('m');
        $startMonth = 01;
        $selectMonth = $currentMonth;
        echo '<select name="year">';
        foreach (range($currentMonth, $startMonth) as $Month) {
            $selected = "";
            if($Month == $selectMonth) { $selected = " selected"; }
            $date = $currentYear ."-". $Month;
            if($Month < 10)
                echo '<option  ' . $selected . '>' .$currentYear ."년 0". $Month ."월 ". '</option>';
            else echo '<option  ' . $selected . '>' .$currentYear ."년 ". $Month ."월 ". '</option>';
        }
        echo '</select>';
*/
        if($_SESSION['wc-amuz-japanshop-start_date']){
            $ymd = explode("-",$_SESSION['wc-amuz-japanshop-start_date']);
            $date_arr["after"] = array(
                'year'  => $ymd[0],
                'month' => $ymd[1],
                'day'   => '1',
            );
            $end_date = date('t',mktime(0,0,0,$ymd[1],1,$ymd[0]));
            $date_arr["before"] = array(
                'year'  => $ymd[0],
                'month' => $ymd[1],
                'day'   => $end_date,
            );
        }

        if($date_arr["after"] && $date_arr["before"]) $date_arr['inclusive'] = true;

        $curPage = ( $_GET['paged'] ) ? $_GET['paged'] : 1;
        $list_count = $_SESSION['wc-amuz-japanshop-list_count'] ? $_SESSION['wc-amuz-japanshop-list_count'] : 30;
        $args = array(
            'post_type' => "shop_order",
            'post_status' => $post_status,
            'posts_per_page' => $list_count,
            'paged' => $curPage,
            'date_query' => array(
                $date_arr
            ),
        );
        # 상품 조회 관련 옵션
        $order_query = new WP_Query( $args );
        $order_list = $order_query->posts;
        $maxItem = $order_query->found_posts;
        $maxPage = $order_query->max_num_pages;
        ?>
        <div class="tablenav-pages">
            <span class="displaying-num"><?php echo $maxItem ?> 주문 / 페이지당 <input type="text" name="wc-amuz-japanshop-list_count" value="<?=$list_count?>" size="3" /> 건</span>
            <span class="pagination-links">
                <a class="first-page <?php echo ($curPage <= 1) ? ' disabled' : ''; ?>" title="Go to the first page" href="admin.php?page=wc4amuz_japanshop_datacenter_output&tab=calculate">«</a>
                <a class="prev-page <?php echo ($curPage <= 1) ? ' disabled' : ''; ?>" title="Go to the previous page" href="admin.php?page=wc4amuz_japanshop_datacenter_output&tab=calculate&amp;paged=<?php echo ($curPage-1) ?>">‹</a>

                <span class="total-pages"><?php echo $curPage ?> / <?php echo $maxPage ?></span>

                <a class="next-page <?php echo ($curPage >= $maxPage) ? ' disabled' : ''; ?>" title="Go to the next page" href="admin.php?page=wc4amuz_japanshop_datacenter_output&tab=calculate&amp;paged=<?php echo ($curPage+1) ?>">›</a>
                <a class="last-page <?php echo ($curPage >= $maxPage) ? ' disabled' : ''; ?>" title="Go to the last page" href="admin.php?page=wc4amuz_japanshop_datacenter_output&tab=calculate&amp;paged=<?php echo $maxPage ?>">»</a>
            </span>
        </div>
    </div>
    </form>
    <form id="wc-amuz-japanshop-orderlist-cart" method="post" onsubmit="if(this.action.value ==''){ alert('내려받을 데이터 유형을 선택하세요.'); return false;}" action="/wp-content/plugins/amuz-japanshop/actions/savefile.php" enctype="multipart/form-data">
        <input type="hidden" id="data_action_type" name="action" value="" />
<?php
echo "<table class='wp-list-table widefat fixed striped posts' id='film'>";
echo "<cols>    
            <col width='30' />
        </cols>";
echo "<thead class='calculate'><tr>
    <th><input id='cart_all' type='checkbox'></th>
    <th>날짜</th>
    <th>주문<br>번호</th> <th>결제<br>방법</th> <th>정산<br>금액</th> <th class='plus'>+<br>합계<br>금액</th>
    <th class='minus'>-<br>합계<br>금액</th> <th class='plus'>+<br>소비세</th> <th class='plus'>+<br>티쿤<br>수수료</th>
    <th class='plus'>+<br>배송<br>비용</th> <th class='plus'>+<br>상품<br>정산</th> 
    <th class='plus'>+<br>결제<br>수수료</th><th class='plus'>+<br>관세</th>
    <th class='minus'>-<br>소비세</th> <th class='minus'>-<br>티쿤<br>수수료</th> <th class='minus'>-<br> 결제<br>수수료</th> 
    <th class='minus'>-<br>환불</th> <th class='minus'>-<br>송금<br>수수료</th>  
    <th class='minus'>-<br>관세</th> <th class='minus'>-<br>[청구서]<br>배송료</th>
</tr></thead>";
// ^ 테이블 헤드
// 테이블 주 내용
echo "<tbody>";
$total = array();
$total['plus'] = 0;         # + 총 합계금액
$total['minus'] = 0;       # - 총 합계금액
$total['tax'] = 0;              # + 소비세
$total['excise'] = 0;           # + 수수료
$total['delivery'] = 0;            # + 배송비
                                #PG 결제 수수료
                                #관세
$total['m_tax'] = 0;            # - 소비세
$total['m_excise'] = 0;         # - 수수료
                                #-PG 수수료
                                # 송금 수수료
$total['m_subtotal'] = 0;       #환불
$total['customs'] = 0;          #돌려받은 관세
$total['m_customs'] = 0;        #부과된 관세

$hs_codes = array();
include"hscodes.php";

$hs_codes_refund = array();
include"hscodes_refund.php";

foreach($order_list as $no => $order) {
echo "<div class='V1'>";
    echo "<tr ID='1'>";
    echo "<th scope='row' class='check-column'>
    <input type='checkbox' name='cart[]' class='cart' value='{$order->ID}'></th>";
    # 주문번호 앞에 있는 체크박스
    $order = new WC_Order($order->ID);
    $order_refund = $order->get_refunds();
    $payment = get_payment_method($order->payment_method);

/*
    $array=array();
    $del_date=$order->get_meta('ywot_pick_up_date');
    if ($del_date !="") {
        if(strpos($del_date,'-'))      $array=explode("-", $del_date);
}
*/
    echo "<td>{$order->get_date_created()->format("m / d")}</td>";
    #주문번호
    echo "<td>" . "<a href='" . get_edit_post_link($order->get_order_number()) . "' target='_blank'>" . $site_code["order_code"]
        . trim(str_replace('#', '', $order->get_order_number())) . "</a></td>";

    if($order->get_meta('비교')=='협찬')
        echo "<td>협찬</td>";
        else echo "<td>{$payment}</td>";


    #배송비용
    $delivery = $order->get_shipping_total();

    # 할인되는 쿠폰 총합
    $discount = $order->get_discount_total();
    # 상품가
    $item_subtotal = $order->get_subtotal() - $discount;
    $itemtotal = round($item_subtotal*1.08);

    $custom_fee = $order->get_fees();
    $fees=0;
if ($payment == '편의점') {
    if ($order->get_subtotal() < 1000) $fee = 130;
    elseif ($order->get_subtotal() < 2000) $fee = 150;
    elseif ($order->get_subtotal() < 3000) $fee = 180;
    elseif ($order->get_subtotal() < 10000) $fee = 210;
    elseif ($order->get_subtotal() < 30000) $fee = 270;
    elseif ($order->get_subtotal() < 100000) $fee = 410;
    elseif ($order->get_subtotal() < 150000) $fee = 560;
    elseif ($order->get_subtotal() < 300000) $fee = 770;
    $Convenience = $fee/1.08;
    $Convenience_fee = $Convenience * 0.08;
    }
else {
    $Convenience = 0;
    $Convenience_fee = 0;
}
    if($order->get_meta('결제 수수료')=='없음') $total_Convenience=0;
    else$total_Convenience = $Convenience+$Convenience_fee;
    #환불 받은 가격
    #오차 수정이 발생했을 경우 meta data
    $errorcorrection=0;
    $get_refunds = $order->get_total_refunded();

    if($order->get_total_refunded()!="0"){
        if ($order->get_meta('수수료 오차 수정') !="") {
            if ($order->get_total_refunded() == $order->get_subtotal()) {
                $errorcorrection = 0;
            }
            else$errorcorrection = $order->get_meta('수수료 오차 수정');
        }
    }


    #환불 시 돌려줄 배송료
    $shipping_refunded = $order->get_total_shipping_refunded();
    #환불 시 돌려줄 총 수수료
    $tax_refunded = $order->get_total_tax_refunded();
    #환불 시 돌려줄 주문(편의점 카드 수수료 등) 수수료
    $custom_tax = $order->get_total()-$order->get_total_tax()-$order->get_shipping_total();

    $refunds= ($get_refunds)-($errorcorrection);

    #환불 받은 상품 가격
    if($refunds!="0") {
        $refund_subtotal = $refunds - $tax_refunded - $shipping_refunded - $Convenience;
        $refunds = round($refund_subtotal * 1.08);
        $refund = round($refund_subtotal * 1.08)+$shipping_refunded;
    }
    else{
        $refund_subtotal=0;
        $refund=0;
    }

    # 상품 수수료 계산
    //소비세
    $totalm_excise =round(($itemtotal*0.08)/1.08);
    //수수료
    $totalm_tax=round($itemtotal*0.08);

    #환불 수수료 계산
    foreach ($order->get_refunds() as $item_key => $item_values) {
        $line_amount = $refunds;
        $total_tax += $line_amount * 0.08;                  # 총 결제금액의 티쿤 수수료 계산
        $total_excise += round($total_tax / 1.08); # 소비세 계산
    }
    if($order->get_meta("환불 소비세 조정") !=""){
        $total_excise = $total_excise - $order->get_meta("환불 소비세 조정");
    }
    if($order->get_meta("환불 티쿤 수수료 조정") !="")
        $total_tax = $total_tax - $order->get_meta("환불 티쿤 수수료 조정");
    $pay_refund = $refund;
    /*if ($payment == '편의점') {
            if($pay_refund < 1)$pg_tax = 0;
            elseif($pay_refund < 2000) $pg_tax = 125;
            elseif ($pay_refund < 3000) $pg_tax = 140;
            elseif ($pay_refund < 10000) $pg_tax = 185;
            elseif ($pay_refund < 30000) $pg_tax = 230;
            elseif ($pay_refund < 100000) $pg_tax = 300;
            elseif ($pay_refund < 150000) $pg_tax = 400;
            elseif ($pay_refund < 300000) $pg_tax = 600;
        } else*/if ($payment == '신용카드'){
        if($card_type=='visa'||$card_type=='mastercard')
            $pg_tax = $pay_refund * 2.85 / 100;
        elseif($order->get_meta('credit')=='JCB'||$order->get_meta('credit')=='AMEX'
            ||$order->get_meta('credit')=='Diners')
            $pg_tax = $pay_refund * 3.35 / 100;
        else $pg_tax = $pay_refund * 2.85 / 100;
        }
    /*elseif ($payment == '은행결제') $pg_tax = /*($pay_refund * 1.50) / 100;*/##0;
    /*if ($payment == '대인결제'){
        if($pay_refund < 1)$pg_tax = 0;
        elseif($pay_refund < 10000) $pg_tax = 300;
        elseif ($pay_refund < 30000) $pg_tax = 400;
        elseif ($pay_refund < 100000) $pg_tax = 600;
        elseif ($pay_refund < 300000) $pg_tax = 1000;
        elseif ($pay_refund < 500000) $pg_tax = 2000;
        elseif ($pay_refund < 600000) $pg_tax = 6000;
    }*/
        elseif($payment == '기타')$pg_tax = 0;

        $pg_tx = $pg_tax*1.08;

    if($pay_refund!=0 and $payment == '신용카드') {
        $pg_tax = $pg_tx + 5;
    }
    else
        $pg_tax = $pg_tx;

    $zeusm = $itemtotal + $delivery;


        if ($payment == '편의점') {
            if($zeusm < 1)$pgm_tax = 0;
            elseif($zeusm < 2000) $pgm_tax = 125;
            elseif ($zeusm < 3000) $pgm_tax = 140;
            elseif ($zeusm < 10000) $pgm_tax = 185;
            elseif ($zeusm < 30000) $pgm_tax = 230;
            elseif ($zeusm < 100000) $pgm_tax = 300;
            elseif ($zeusm < 150000) $pgm_tax = 400;
            elseif ($zeusm < 300000) $pgm_tax = 600;
        } elseif ($payment == '신용카드'){
            if($card_type=='visa'||$card_type=='mastercard')
                $pgm_tax = $zeusm * 2.85 / 100;
            elseif($order->get_meta('credit')=='JCB'||$order->get_meta('credit')=='AMEX'
                ||$order->get_meta('credit')=='Diners')
                $pgm_tax = $zeusm * 3.35 / 100;
            else $pgm_tax = $zeusm * 2.85 / 100;
        }
        elseif ($payment == '은행결제') $pgm_tax = ($zeusm * 1.50) / 100;
        elseif ($payment == '대인결제'){
            if($zeusm < 1)$pgm_tax = 0;
            elseif($zeusm < 10000){
                $pgm_tax = 300;
            }
            elseif ($zeusm < 30000){
                $pgm_tax = 400;
            }
            elseif ($zeusm < 100000){
                $pgm_tax = 600;
            }
            elseif ($zeusm < 300000){
                $pgm_tax = 1000;
            }
            elseif ($zeusm < 500000){
                $pgm_tax = 2000;
            }
            elseif ($zeusm < 600000){
                $pgm_tax = 6000;
            }
        }
        elseif ($payment == '기타') $pgm_tax=0;

        $pgm_tx=round(round($pgm_tax)*1.08);
    if($zeusm!=0 and $payment == '신용카드') {
        $pgm_tax = $pgm_tx + 5;
    }
    else $pgm_tax= $pgm_tx;

    $interpers = $order->get_subtotal();
    $inter=0;
        if ($payment == '대인결제') {
                if ($interpers < 1) $inter = 0;
                elseif ($interpers < 10000) {
                    $inter = 300;
                } elseif ($interpers < 30000) {
                    $inter = 400;
                } elseif ($interpers < 100000) {
                    $inter = 600;
                } elseif ($interpers < 300000) {
                    $inter = 1000;
                } elseif ($interpers < 500000) {
                    $inter = 2000;
                } elseif ($interpers < 600000) {
                    $inter = 6000;
                }

            $interper = round($inter * 1.08);

        }
    else $interper=0;

    $oHSInfo = getHsValues($hs_codes, $order->get_items());

    $oHsRefundInfo = getHsRefundValues($hs_codes_refund, $order->get_refunds());

    //환불(발송후 환불) 수수료만 청구 없음
    if($order->get_meta('환불시 배송여부') !=""){
        $total_excise=0;
        $oHsRefundInfo['tqoon_tax']=0;
    }

    #송금 수수료
    if($order->get_meta('remittance_fee')!="")
    $remittance = $order->get_meta('remittance_fee');
    else $remittance = 0;


    #청구 배송료 추가
    $custom_delivery = get_post_meta($order->get_order_number(),'custom_delivery')[0];

    for($i = 0; $i<$num; $i++) {
        $cus_deli = $x2[$i];
        if($cus_deli['order_id']==$order->get_order_number() and $custom_delivery == "" ){
            add_post_meta($order->get_order_number(),'custom_delivery',$cus_deli['delivery']);
        }
        elseif($cus_deli['order_id']==$order->get_order_number() and $custom_delivery != ""and $cus_deli['delivery'] != $custom_delivery ){
            update_post_meta($order->get_order_number(),'custom_delivery',$cus_deli['delivery'],$custom_delivery);
        }
    }

    if($custom_delivery== "") $custom_delivery = 0;


    if($itemtotal==0){
        $totalm_excise = 0;
        $totalm_tax = 0;
        $remittance=0;
        $pgm_tax=0;
        $oHSInfo['tqoon_tax']=0;
        $oHSInfo_tax = 0;
        $total_m_calculate = 0;
        $total_calculate = 0;
        $jungsan=0;
    }

    //환불시 돌려받는 관세
    $oHSInfo_refund_tax = number_format($oHsRefundInfo['tqoon_tax']);
    //관세
    $oHSInfo_tax = number_format($oHSInfo['tqoon_tax']);

    if($payment == '편의점')
        $plustax=$total_Convenience;
    elseif($payment == '대인결제')
        $plustax=$interper;
    else
        $plustax=$pg_tax;

    #  + 합계금액
    $total_calculate = $itemtotal + $delivery + $total_tax + $total_excise + $plustax + $oHSInfo_refund_tax;


    # - 합계금액
    $total_m_calculate = $refund + $totalm_tax + $totalm_excise +   $oHSInfo_tax + $pgm_tax + $remittance + $custom_delivery;

    $jungsan = round($total_calculate - $total_m_calculate);


    # 총 정산금액
    echo "<td>￥" . number_format($jungsan) . "</td>";
    # + 합계  = 상품가격  + 배송료
    echo "<td class='plus'>￥" . number_format($total_calculate) . "</td>";
    # -합계 = 환불 +
    echo "<td class='minus'>￥" . number_format($total_m_calculate) . "</td>";
    #소비세
    echo "<td class='plus'>￥" . number_format($total_excise) . "</td>";
    #티쿤수수료
    echo "<td class='plus'>￥" . number_format($total_tax) . "</td>";
    #배송비
    echo "<td class='plus'>￥" . number_format($delivery) . "</td>";
    #상품정산
    echo "<td class='plus'>￥" . number_format($itemtotal) . "</td>";

    # + 결제 수수료
    echo "<td class='plus'>￥" . number_format($plustax) . "</td>";
    //관세 받아올것
    #+관세
    echo "<td class='plus'>￥".$oHSInfo_refund_tax."</td>";
    #-소비세
    echo "<td class='minus'>￥" . number_format($totalm_excise) . "</td>";
    #-수수료
    echo "<td class='minus'>￥" . number_format($totalm_tax) . "</td>";
    #-PG 결제 수수료
    echo "<td class='minus'>￥" . number_format($pgm_tax) . "</td>";
    #환불
    echo "<td class='minus'>￥" . number_format($refund) . "</td>";
    #송금 수수료
    echo "<td class='minus'>￥".number_format($remittance)."</td>";

    #-관세
    /*echo $implode = "상품의 관세".implode("<br>"."상품의 관세",$oHSInfo["items"]);
    echo $implode = "관세".implode("%"."<br>"."관세",$oHSInfo["ttax"]);*/
    echo "<td class='minus'>￥".$oHSInfo_tax."</td>";

    echo "<td class='minus'>￥".number_format($custom_delivery)."</td>";


    # 총 합계 배송비
    $total['delivery'] += $delivery;

    # 상품가 합계(정산)
    $total['amount'] += $itemtotal;

    # 환불 합계
    $total['refund'] += $refund;

    # 소비세 합계
    $total['excise'] += $total_excise;

    # 수수료 합계
    $total['tax'] += $total_tax;

    # - 소비세 합계
    $total['m_excise'] += $totalm_excise;

    # - 수수료 합계
    $total['m_tax'] += $totalm_tax;

    $total['customs'] += $oHsRefundInfo['tqoon_tax'];

    #-관세 총합
    $total['m_customs'] += $oHSInfo['tqoon_tax'];

    # 총 합계 +합계금액
    $total['plus'] += $total_calculate;

    # 총 합계 -합계금액
    $total['minus'] += $total_m_calculate;

    #총 합계 정산금액
    $total['jungsan'] += $jungsan;


    $total['pg_tax'] += $plustax;
    $total['pgm_tax'] += $pgm_tax;

    $total['Remittance'] += $remittance;
    $total['custom_delivery'] += $custom_delivery;

    $m_tax=0;
    $m_excise = 0;
    $total_tax = 0;
    $total_excise = 0;
    echo "</tr>";
/*
    if($itemtotal==0)
        echo "<div style='display:none'>";
    else
        echo "<div>";
    foreach ($order->get_items() as $item_key => $item_values) {
        echo "<tr style='background:#fff8e1;'>";
        $item_data = $item_values->get_data();
        $product_name = $item_data['name'];
        $quantity = $item_data['quantity'];
        $line_total = $item_data['total'];
        $line_total_tax = $item_data['total_tax'];
        $product_id = $item_data['product_id'];
        $product_code = get_post_meta( $product_id, '원청_상품코드', true);
        $hs_code = get_post_meta( $product_id, '수출용_관세코드', true);
        $hs_title = get_post_meta( $product_id, '수출용_상품명', true);
        $hs_fabric = get_post_meta( $product_id, '수출용_재질', true);
        $order_id = $item_data['order_id'];
        $aa = array_keys($oHSInfo['order_id']);
        if($order_id = $aa[0]){
            $bb=$oHSInfo['order_id'][$aa[0]];
            //echo "상품번호".$product_id. "=" ."관세율".$bb[$product_id];
            echo "<td colspan='3'></td>";
            echo "<td><a href='".get_permalink( $product_id )."' target='_blank'>상품보기</a></td>";
            echo "<td colspan='3'>[$product_code] $product_name x $quantity</td>";
            echo "<td colspan='3'>[$hs_code] $hs_title // $hs_fabric</td>";
            echo "<td>￥". number_format($line_total) . "</td>";
            echo "<td colspan='2'>" ."관세율  ".$bb[$product_id]."</td>";
        }
        echo "</tr></div>";
    }
*/
}


echo "</tbody>";
echo "<tfoot>";
echo "<tr><th></th>
<td colspan='3'>총합계</td>";
echo "<td >￥".number_format($total['jungsan'])           ."</td>";#정산금액
echo "<td class='plus'>￥".number_format($total['plus'])              ."</td>"; #+ 총합
echo "<td class='minus'>￥".number_format($total['minus'])             ."</td>";   #-합계
echo "<td class='plus'>￥".number_format($total['excise'])            ."</td>";   #소비세
echo "<td class='plus'>￥".number_format($total['tax'])               ."</td>";  #수수료
echo "<td class='plus'>￥".number_format($total['delivery'])          ."</td>";#배송비
echo "<td class='plus'>￥".number_format($total['amount'])            ."</td>";   #상품정산
echo "<td class='plus'>￥".number_format($total['pg_tax'])            ."</td>";   # 결제 수수료
echo "<td class='plus'>￥".number_format($total['customs'])           ."</td>";#+관세
echo "<td class='minus'>￥".number_format($total['m_excise'])          ."</td>";    #청구된 소비세
echo "<td class='minus'>￥".number_format($total['m_tax'])             ."</td>"; #청구된 수수료
echo "<td class='minus'>￥".number_format($total['pgm_tax'])           ."</td>";
echo "<td class='minus'>￥".number_format($total['refund'])            ."</td>";  #환불
echo "<td class='minus'>￥".number_format($total['Remittance'])        ."</td>";
echo "<td class='minus'>￥".number_format($total['m_customs'])         ."</td>";
echo "<td class='minus'>￥".number_format($total['custom_delivery'])   ."</td>";
echo "</tr></tfoot>";
echo "</table>";

?>
    </form>
<style>
    .product_list td{
    font-size:12px;
    }
</style>
<script type="text/javascript">
    jQuery(document).ready(function($){
        $('#cart_all').on('click',function(){
            if(this.checked){
                $('.cart').each(function(){
                    this.checked = true;
                });
            }else{
                $('.cart').each(function(){
                    this.checked = false;
                });
            }
        });

        $('.cart').on('click',function(){
            if($('.cart:checked').length == $('.cart').length){
                $('#cart_all').prop('checked',true);
            }else{
                $('#cart_all').prop('checked',false);
            }
        });
    });

    var button = document.getElementById('excelup');
    button.onclick = function() {
        var div = document.getElementById('test_div');
        if (div.style.display !== 'none') {
            div.style.display = 'none';
        }
        else {
            div.style.display = 'block';
        }
    };

</script>

