<?php

global $woocommerce;

include __DIR__.'./../define_arrays.php';

$site_code = getSiteOrderCode();

//데이터가 들어오는것과 상관없이 일단 쿠키를 세션에 집어넣음.
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
                update_option($status_method_str, 'N');
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

?>
<form id="wc-amuz-japanshop-datacenter-form" method="post" action="" enctype="multipart/form-data">
    <?php wp_nonce_field( 'my-nonce-key','wc-am-jp-datacenter');?>
    <h3><?php echo __( '커스텀 주문데이터 다운로드센터', 'amuz-japanshop' );?></h3>
    <div class="tablenav top">
        <div class="alignleft actions bulkactions">
            <label for="bulk-action-selector-top" class="screen-reader-text">일괄 작업 선택</label>
            <select name="action" id="bulk-action-selector-top" onchange="jQuery('#data_action_type').val(jQuery(this).val());">
                <option value="">일괄 작업</option>
                <option value="sagawa">운송장 - 사가와</option>
                <option value="tqoon_packing">패킹리스트 - 티쿤</option>
                <option value="tqoon_orderlist">주문내역 - 티쿤</option>
            </select>
            <input type="button" id="doaction" class="button action" value="저장" onclick="jQuery('#wc-amuz-japanshop-orderlist-cart').submit();" />
        </div>
        <div class="alignleft actions">
            <?php
            //조회일 지정
            foreach ($date_list as $key => $value) {
                $date_method_str = "wc-amuz-japanshop-".$key;
                echo '<input type="date" name="' . $date_method_str . '" value="'.$_SESSION[$date_method_str].'" /> &nbsp; ';
            }

            //조회할 상태지정
            $post_status = array();
            foreach ($status_list as $key => $value) {
                $status_method_str = "wc-amuz-japanshop-".$key;
                $options = get_option($status_method_str);
                if($options == "Y") $post_status[] = $key;
                echo '<label for="woocommerce_input_'.$key.'"><input type="checkbox" id="woocommerce_input_'.$key.'" name="' . $status_method_str . '" value="Y" ';
                checked($options, 'Y');
                echo '>' . $value . '</label> &nbsp; ';
            }
            ?>
            <input name="save" class="button-primary" type="submit" value="조회">
        </div>
        <?php

        //페이지네이션을 위해 쿼리를 미리 실행

        $date_arr = array();
        if($_SESSION['wc-amuz-japanshop-start_date']){
            $ymd = explode("-",$_SESSION['wc-amuz-japanshop-start_date']);
            $date_arr["after"] = array(
                'year'  => $ymd[0],
                'month' => $ymd[1],
                'day'   => $ymd[2],
            );
        }
        if($_SESSION['wc-amuz-japanshop-end_date']){
            $ymd = explode("-",$_SESSION['wc-amuz-japanshop-end_date']);
            $date_arr["before"] = array(
                'year'  => $ymd[0],
                'month' => $ymd[1],
                'day'   => $ymd[2],
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
        $order_query = new WP_Query( $args );
        $order_list = $order_query->posts;
        $maxItem = $order_query->found_posts;
        $maxPage = $order_query->max_num_pages;
        ?>
        <div class="tablenav-pages">
            <span class="displaying-num"><?php echo $maxItem ?> 주문 / 페이지당 <input type="text" name="wc-amuz-japanshop-list_count" value="<?=$list_count?>" size="3" /> 건</span>
            <span class="pagination-links">
                <a class="first-page <?php echo ($curPage <= 1) ? ' disabled' : ''; ?>" title="Go to the first page" href="admin.php?page=wc4amuz_japanshop_datacenter_output">«</a>
                <a class="prev-page <?php echo ($curPage <= 1) ? ' disabled' : ''; ?>" title="Go to the previous page" href="admin.php?page=wc4amuz_japanshop_datacenter_output&amp;paged=<?php echo ($curPage-1) ?>">‹</a>

                <span class="total-pages"><?php echo $curPage ?> / <?php echo $maxPage ?></span>

                <a class="next-page <?php echo ($curPage >= $maxPage) ? ' disabled' : ''; ?>" title="Go to the next page" href="admin.php?page=wc4amuz_japanshop_datacenter_output&amp;paged=<?php echo ($curPage+1) ?>">›</a>
                <a class="last-page <?php echo ($curPage >= $maxPage) ? ' disabled' : ''; ?>" title="Go to the last page" href="admin.php?page=wc4amuz_japanshop_datacenter_output&amp;paged=<?php echo $maxPage ?>">»</a>
            </span>
        </div>
    </div>
</form>

<form id="wc-amuz-japanshop-orderlist-cart" method="post" onsubmit="if(this.action.value ==''){ alert('내려받을 데이터 유형을 선택하세요.'); return false;}" action="/wp-content/plugins/amuz-japanshop/actions/savefile.php" enctype="multipart/form-data">
    <input type="hidden" id="data_action_type" name="action" value="" />
    <?php
    echo "<table class='wp-list-table widefat fixed striped posts'>";
    echo "<cols>
            <col width='30' />
        </cols>";
    echo "<thead><tr>
            <th><input id='cart_all' type='checkbox'></th>
			<th>주문번호</th>
			<th>회원</th>
			<th>수령자</th>
			<th>상태</th>
			<th>주문일자</th>
			<th>송장번호</th>
			<th>결제수단</th>
			<th>총할인</th>
			<th>배송료</th>
			<th>포함된세금</th>
            <th>결제총액</th>
			<th>비고</th>
		</tr></thead>";
    echo "<tbody>";
    $total = array();
    $total['discount'] = 0;
    $total['tax'] = 0;
    $total['delivery'] = 0;
    $total['amount'] = 0;

    $hs_codes = array();
    include"hscodes.php";
        foreach($order_list as $no => $order){
            echo "<tr>";
            echo "<th scope='row' class='check-column'><input type='checkbox' name='cart[]' class='cart' value='{$order->ID}'></th>";
            $order = new WC_Order( $order->ID );
            echo "<td>"."<a href='".get_edit_post_link( $order->get_order_number() )."' target='_blank'>".$site_code["order_code"]
                .trim(str_replace('#', '', $order->get_order_number())) ."</a></td>";

            if($order->get_customer_id() != 0){
                $user = get_userdata($order->get_customer_id());
                echo "<td>{$user->display_name} <br /><small>(<a href='mailto:{$user->user_email}'>{$user->user_email}</a>)</small></td>";
            }else{
                $user = false;
                echo "<td>비회원</td>";
            }
            echo "<td>{$order->get_shipping_last_name()} {$order->get_shipping_first_name()}</td>";
            echo "<td>". $status_list["wc-".$order->get_status()] . "</td>";
            echo "<td>{$order->get_date_created()->format("y년 m월 d일")}";
            echo "<br />{$order->get_date_created()->format("H시 i분")}</td>";

            if($order->get_meta('ywot_tracking_code' )){
                echo "<td>";
                echo $shipping_list[$order->get_meta('ywot_carrier_id')][0] . "<br />";
                echo "<a href='". sprintf($shipping_list[$order->get_meta('ywot_carrier_id')][1],$order->get_meta('ywot_tracking_code')) . "' target='_blank' title='{$order->get_meta('ywot_pick_up_date' )}에 발송'<small>" ."(".$order->get_meta('ywot_tracking_code').")</small>";
                echo "</td>";
            }else{
                echo "<td>미배송</td>";
            }
            echo "<td>".get_payment_method($order->payment_method)."</td>";

            $total['discount'] += $order->get_total_discount();
            $total['tax'] += $order->get_total_tax();
            $total['delivery'] += $order->get_shipping_total();
            $total['amount'] += $order->get_total();

            echo "<td>￥".number_format($order->get_total_discount())."</td>";
            echo "<td>{$order->get_shipping_total()}</td>";

            //가격 오차 발생시
            $errorcorrection=0;
                if ($order->get_meta('수수료 오차 수정') != "")
                    $errorcorrection = $order->get_meta('수수료 오차 수정');

            $error_total = floor($errorcorrection/1.08);
            $error_tax = round($errorcorrection*0.08);
            $order_total_tax = $order->get_total_tax() - $error_tax;
            $order_total = $order->get_total()-$errorcorrection;

            echo "<td>￥".number_format($order_total_tax)."</td>";
            echo "<td>￥".number_format($order_total)."</td>";

            echo "<td rowspan='". (count($order->get_items())+1) ."'>" . $order->get_meta('비고') . "</td>";
            echo "</tr>";
            $oHSInfo = getHsValues($hs_codes, $order->get_items());
            foreach ($order->get_items() as $item_key => $item_values){
                echo "<tr style='background:#fff8e1;' class='product_list'>";
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
                $bb = $oHSInfo['order_id'][$aa[0]];
                    echo "<td colspan='3'></td>";
                    echo "<td><a href='" . get_permalink($product_id) . "' target='_blank'>상품보기</a></td>";
                    echo "<td colspan='3'>[$product_code] $product_name x $quantity</td>";
                    echo "<td colspan='2'>[$hs_code] $hs_title // $hs_fabric</td>";
                    echo "<td>" . "관세율  " . $bb[$product_id] . "</td>";
                    echo "<td>" . $line_total_tax . "</td>";
                    echo "<td>" . number_format($line_total) . "</td>";

                echo "</tr>";
            }
        }
    echo "</tbody>";
    echo "<tfoot>";
    echo "<tr><td colspan='8'>합계</td>";
    echo "<td>" . number_format($total['discount']) . "</td>";
    echo "<td>" . number_format($total['delivery']) . "</td>";
    echo "<td>" . number_format($total['tax']) . "</td>";
    echo "<td>" . number_format($total['amount']) . "</td>";
    echo "<td></td>";
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
</script>
