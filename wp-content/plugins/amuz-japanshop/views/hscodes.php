<?php
global $wpdb;
foreach($order_list as $no => $order) {
    $order = new WC_Order($order->ID);
    $order_code = $site_code["order_code"] . trim(str_replace('#', '', $order->get_order_number()));

    foreach ($order->get_items() as $item_key => $item_values) {
        $item_data = $item_values->get_data();
        $product_id = $item_data['product_id'];
        $hscode = get_post_meta($product_id, '수출용_관세코드', true);
        $hs_codes[$hscode] = $hscode;

    }
}

$hs_in= "'".join("','",$hs_codes)."'";
$get_hscode_db = $wpdb->get_results("select * from wp_hscode where HScode in ({$hs_in})");


foreach ( $get_hscode_db as $hs_code )
{
    $hs_codes[$hs_code->HScode] = $hs_code;
}

function getHsValues($hs_codes, $items){
    $oHsInfo = array();
    $oHSInfo["items"] = array();
    $oHSInfo['tqoon_per']=0;
    foreach ($items as $item_key => $item_values) {

        $item_data = $item_values->get_data();
        $order_id=$item_data['order_id'];
        $product_id = $item_data['product_id'];
        $hscode = get_post_meta($product_id, '수출용_관세코드', true);
        $hs_info = $hs_codes[$hscode];
        ///식
        /// $items = $order->get_items();
        ///
        #주문 된 상품가격
        $prices = $item_data['total'];
        #주문 된 상품의 갯수
        $quantity = $item_data['quantity'];
        #관세 값이 적용된 총 가격
        $item_total = 0;


        $item_tax = 0;
        #상품 가격에 붙는 관세
        $tax = 0;

        #무세 면세
        $free = $hs_info->free;

        #%값
        $per = $hs_info->person;

        #또는
        $or = $hs_info->or;

        #+
        $plus = $hs_info->plus;

        #고정값이 존재할떄
        $fix = $hs_info->fix;

        #/kg같은 조건이 존재할때
        if($hs_info->unit !="") {
            $unit = 1 * $quantity;
        }else $unit = 0;
        # 높은세율을 선택해야할떄
        if ($hs_info->taxrate != ""){
            $taxrate = $hs_info->taxrate;
        }

        #둘다 아니라면 공백
        else {
            $taxrate = "";
        }

        #특수조건 세율이 일정 이하로 떨어질때 고정
        if ($hs_info->min != "0") {
            $min = $hs_info->min . "엔 /L";
        } else {
            $min = "";
        }

        # 특수조건 유당 함유량
        if ($hs_info->lactose == "0") {
            $lactose = "0";
        } else {
            $lactose = "1";
        }

        #계산식
        if ($free != ""){
            $item_total = $prices;
            $tax = 0;
            $item_tax = 0;
        }

        if ($per != "0" and $or == "Y" and $fix != "0") {     # 또는 높은세율 낮은세율 조건이 필요할때
            $D = $prices + (($prices * $per) / 100);
            $E = $prices + ($fix * $unit);
            /* 1. 제품값 + ??% ,  2. 제품값 + 고정값 * 물건의 양 * /?? 둘 중 하나 조건에 맞춰 하나만 출력*/

            /* 제품값 + ??%  제품값 + 고정값 /?? 둘중 값이 높은것 출력*/
            if ($taxrate == "높은 세율") {
                if ($D > $E) {
                    $D = $item_total;
                } else {
                    $E = $item_total;
                }
            }

            /* 제품값 + ??%  제품값 + 고정값 /?? 둘중 값이 낮은것 출력*/
            elseif ($taxrate == "낮은 세율") {
                if ($D < $E) {
                    $E = $item_total ;
                } else {
                    $D = $item_total ;
                }
            }
        } else { # 관세 = %값과 fix값을 +
            if ($per != "0" and $hs_info->{'plus'} == "1" and $fix != "0") {
                $item_total = ($prices + ($prices * $per) / 100) + ($fix * $unit);
                $tax = (($prices * $per) / 100)."% ,".$fix;
                $item_tax = (($prices * $per) / 100) + ($fix * $unit);

                /* ( 제품값 + ??% ) + (고정값 * (무게 * /??)) */
            } elseif ($per == "0" and $fix != "0") {    #관세 = fix
                $item_total = ($fix * $unit) + $prices;
                $tax = $fix * $unit;
                $item_tax = $prices + $tax;
                /*제품값 + 고정값 * /?? */
            } elseif ($per != "0" and $fix == "0") {                    # 관세 = 물건의 % 값
                $item_total = $prices + (($prices * $per) / 100);
                $tax = $per."%";
                $item_tax = $prices  * $per / 100;
                /*제품값 + ??% */
            } elseif ($free != "") {
                $item_total = $prices;
                $tax = 0;
                $item_tax = 0;
                /*무세 면세 일 경우 제품가격 그대로*/
            } elseif ($lactose != "0" and $lactose_content >= "10") {   #만약 유당 함유량이 10% 이상이라면
                $item_total = ($prices + ($fix * $quantity)) + ($lactose_content * 7);
                $tax = ($fix * $quantity) + ($lactose_content * 7);
                /* 물건값 + 70엔 * /kg 유당함유율 + 10% 이상일시 1%당 + 7엔*/
            }
        }


///
        if($product_id == 6256)
            $tax = "10.90%";

        $oHSInfo['total']=$item_data['total'];
        $oHSInfo['ttax'][$item_data['product_id']]=$tax;
        $oHSInfo["items"][$item_data['product_id']]=$item_tax;
        $oHSInfo['product_code'][$item_data['product_id']]=$item_tax;
        $oHSInfo['total'] += $item_total;
        $oHSInfo['tax'] +=round($item_tax);
        $oHSInfo['item_data'] = $item_data;
        $oHSInfo['itemtax'][$hscode]=$order_id;
        $oHSInfo['itemtax'][$order_id]=$tax;

        $oHSInfo['product_id'][$item_data['product_id']]=$tax;
        $oHSInfo['order_id'][$order_id]=$oHSInfo['product_id'];


        //orderlist
        //카운트
        $oHSInfo['order_count']+=count($item_data);
        $oHSInfo['tax_total']+= $tax;
        //판매대행수수료
        $sale_fee = $prices*0.08;
        //일본소비세
        $japan_fee = $prices*(1+0.08);

        $oHSInfo['sale_fee']+=$sale_fee;
        $oHSInfo['japan_fee']+=$japan_fee;
        $oHSInfo['prices']+=$prices;

        // 애드프린트수입가 => 판매가(-소비세 - 수수료) , 국제송료 250 ￥/kg 고정
        // 제휴사 공급가 => (애드프린트수입가 -(국제송료 × (1 + 관세율)) / (1 + 관세율)
        //제휴사 공급가
        $product_price = $prices+round($prices*0.08);
        $addprint=($product_price-(floor($product_price*0.08)+round(floor($product_price*0.08)/1.08)));
        $oHSInfo['addprint']=($addprint-(250*(1+($tax/100))))/(1+($tax/100));
        //티쿤 요구 관세 (제휴사 공급가 + 국제송료(250))*관세율
        $oHSInfo['tqoon_tax']+=round($oHSInfo['addprint']+250)*($tax/100);



        //관세 확률 합
        $oHSInfo['tqoon_per']+=$tax;
        $a+=1;

    }
    $oHSInfo['tqoon_per'];
    $oHSInfo_count=count($items);
    $oHSInfo['per'] = $oHSInfo['tqoon_per']/$oHSInfo_count;

    //카운트 총합
    $oHSInfo['total_count']+=$oHSInfo['item_count'];
    //관세 확률 총합
    $oHSInfo['tqoon_itemtax_total']+=$oHSInfo['tqoon_per'];
    return $oHSInfo;
}
?>