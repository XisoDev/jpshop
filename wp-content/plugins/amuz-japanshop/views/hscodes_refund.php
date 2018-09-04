<?php
global $wpdb;

foreach($order_list as $no => $order) {
    $order = new WC_Order($order->ID);

    foreach ($order->get_refunds() as $item_key => $item_values) {
        $item_data = $item_values->get_items();

        foreach ($item_data as $item_key => $item_values) {
            $item_data_refund = $item_values->get_data();
        }

        $product_id = $item_data_refund['product_id'];
        $hscode_refund = get_post_meta($product_id, '수출용_관세코드', true);
        $hs_codes_refund[$hscode_refund] = $hscode_refund;
    }
}

$hs_in_refund= "'".join("','",$hs_codes_refund)."'";

$get_hscode_db_refund = $wpdb->get_results("select * from wp_hscode where HScode in ({$hs_in_refund})");


foreach ( $get_hscode_db_refund as $hs_code_refund )
{
    $hs_codes_refund[$hs_code_refund->HScode] = $hs_code_refund;
}

function getHsRefundValues($hs_codes_refund, $refund)
{
    $oHsRefundInfo = array();
    $oHsRefundInfo["items"] = array();

    foreach ($refund as $item_key => $item_values) {
        $item_data = $item_values->get_items();
        foreach ($item_data as $item_key => $item_values) {
            $item_data = $item_values->get_data();
            $product_id = $item_data['product_id'];
            $hscode_refund = get_post_meta($product_id, '수출용_관세코드', true);
            $hs_in_refund = $hs_codes_refund[$hscode_refund];

            #환불 된 상품가격
            $refund = $item_data['total'] * -1;
            #환불 된 상품의 갯수
            $refund_quantity = $item_data['quantity'] * -1;

            #관세 값이 적용된 총 가격
            $item_total = 0;

            $item_tax = 0;
            #상품 가격에 붙는 관세
            $tax = 0;

            #무세 면세
            $free = $hs_in_refund->free;

            #%값
            $per = $hs_in_refund->person;

            #또는
            $or = $hs_in_refund->or;

            #+
            $plus = $hs_in_refund->plus;

            #고정값이 존재할떄
            $fix = $hs_in_refund->fix;

            #/kg같은 조건이 존재할때
            if($hs_in_refund->unit) {
                $unit = 1 * $refund_quantity;
            }else $unit = 0;
            # 높은세율을 선택해야할떄
            if ($hs_in_refund->taxrate != "") {
                $taxrate = $hs_in_refund->taxrate;
            } #둘다 아니라면 공백
            else {
                $taxrate = "";
            }

            #특수조건 세율이 일정 이하로 떨어질때 고정
            if ($hs_in_refund->min != "0") {
                $min = $hs_in_refund->min . "엔 /L";
            } else {
                $min = "";
            }

            # 특수조건 유당 함유량
            if ($hs_in_refund->lactose == "0") {
                $lactose = "0";
            } else {
                $lactose = "1";
            }

            #계산식
            if ($free != "") {
                $item_total = $refund;
                $tax = 0;
                $item_tax = 0;
            }
            if ($per != "0" and $or == "Y" and $fix != "0") {     # 또는 높은세율 낮은세율 조건이 필요할때
                $D = $refund + (($refund * $per) / 100);
                $E = $refund + ($fix *  $unit);
                /* 1. 제품값 + ??% ,  2. 제품값 + 고정값 * 물건의 양 * /?? 둘 중 하나 조건에 맞춰 하나만 출력*/

                /* 제품값 + ??%  제품값 + 고정값 /?? 둘중 값이 높은것 출력*/
                if ($taxrate == "높은 세율") {
                    if ($D > $E) {
                        $D = $item_total;
                    } else {
                        $E = $item_total;
                    }
                } /* 제품값 + ??%  제품값 + 고정값 /?? 둘중 값이 낮은것 출력*/
                elseif ($taxrate == "낮은 세율") {
                    if ($D < $E) {
                        $E = $item_total;
                    } else {
                        $D = $item_total;
                    }
                }
            } else { # 관세 = %값과 fix값을 +
                if ($per != "0" and $hs_in_refund->{'plus'} == "1" and $fix != "0") {
                    $item_total = ($refund + ($refund * $per) / 100) + ($fix * $unit);
                    $tax = (($refund * $per) / 100) + $fix;
                    /* ( 제품값 + ??% ) + (고정값 * (무게 * /??)) */
                } elseif ($per == "0" and $fix != "0") {    #관세 = fix
                    $item_total = ($fix * $unit) + $refund;
                    $tax = $fix * $unit;
                    /*제품값 + 고정값 * /?? */
                } elseif ($per != "0" and $fix == "0") {                    # 관세 = 물건의 % 값
                    $item_total = $refund + ($refund * $per / 100);
                    $tax = $per;
                    $item_tax = $refund * $per / 100;
                    /*제품값 + ??% */
                } elseif ($free != "") {
                    $item_total = $refund;
                    $tax = 0;
                    $item_tax = 0;
                    /*무세 면세 일 경우 제품가격 그대로*/
                } elseif ($lactose != "0" and $lactose_content >= "10") {   #만약 유당 함유량이 10% 이상이라면
                    $item_total = ($refund + ($fix * $refund_quantity)) + ($lactose_content * 7);
                    $tax = ($fix * $refund_quantity) + ($lactose_content * 7);
                    /* 물건값 + 70엔 * /kg 유당함유율 + 10% 이상일시 1%당 + 7엔*/
                }
            }
///
            $oHsRefundInfo['total'] = $item_data['total'];
            $oHsRefundInfo['ttax'][$item_data['product_id']] = $tax;
            $oHsRefundInfo["items"][$item_data['product_id']] = $item_tax;
            $oHsRefundInfo['product_code'][$item_data['product_id']] = $item_data['product_id'];
            $oHsRefundInfo['total'] += $item_total;
            $oHsRefundInfo['tax'] += round($item_tax);
            $oHsRefundInfo['item_data'] = $item_data;

            $readdprint=($refund-(round($refund*0.08)+round(round($refund*0.08)/1.08)));
            $oHsRefundInfo['addprint']=($readdprint-(250*(1+($tax/100))))/(1+($tax/100));

            //티쿤 요구 관세 (제휴사 공급가 + 국제송료(250))*관세율
            $oHsRefundInfo['tqoon_tax']+=round($oHsRefundInfo['addprint']+250)*($tax/100);

            //관세 확률


            $oHsRefundInfo['tqoon_per']+=$item_tax;
        }
    }
    return $oHsRefundInfo;
}
?>