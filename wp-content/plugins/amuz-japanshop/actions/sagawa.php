<?php
/**
 * Created by PhpStorm.
 * User: xiso
 * Date: 2018. 3. 15.
 * Time: PM 3:00
 */
$site_code = getSiteOrderCode();
foreach($order_list as $order_id){
    $order = new WC_Order( $order_id );

    $oData = array();
    $oData['orderNumber'] = $site_code["order_code"] . trim(str_replace('#', '', $order->get_order_number()));
    $oData['receiverName'] = $order->get_shipping_last_name();
    $oData['receiverName2'] = $order->get_shipping_first_name();
    $oData['receiverPost'] = $order->get_shipping_postcode();
    $oData['receiverAddress'] = $states_jp[$order->get_shipping_state()];
    $oData['receiverAddress'] .= " " . $order->get_shipping_city();
    $oData['receiverAddress'] .= $order->get_shipping_address_1();
    $oData['receiverAddress'] .= " " . $order->get_shipping_address_2();

    $oData['receiverPhone'] = get_post_meta($order_id,"_shipping_phone",true);
    $oData['ordererName'] = "スウィート プラス";
    $oData['ordererName2'] = "スウィート プラス";
    $oData['ordererPost'] = "542-0081";
    $oData['ordererAddress'] = "大阪府 大阪市中央区南船場3丁目7-27 NLC心斎橋 4F-I";
    $oData['ordererPhone'] = "050-5578-5509";
    $oData['payment'] = ($order->payment_method == "codpf") ? 1 : 0;
    $oData['sendDate'] = date("Y-m-d")." ★★★";
    $oData['remarks'] = $order->get_customer_note();
    $oData['packNumber'] = "1";
    $oData['amount'] = ($order->payment_method == "codpf") ? $order->get_total() : "";
    $oData['customer'] = $order->get_customer_id();
    $oData['sendType'] = "0";

    $oData['receiveDate'] = "";
    $receiveTime = get_post_meta($order_id,"wc4jp-delivery-time-zone",true);
    if($receiveTime == "08:00-12:00") {
        $oData['receiveTime'] = "1";
    }else if($receiveTime == "12:00-14:00"){
        $oData['receiveTime'] = "2";
    }else if($receiveTime == "14:00-16:00"){
        $oData['receiveTime'] = "3";
    }else if($receiveTime == "16:00-18:00"){
        $oData['receiveTime'] = "4";
    }else if($receiveTime == "18:00-20:00"){
        $oData['receiveTime'] = "5";
    }else if($receiveTime == "19:00-21:00"){
        $oData['receiveTime'] = "6";
    }else{
        $oData['receiveTime'] = "0";
    }

    $items = $order->get_items();
    reset($items);
    $oData['star'] = substr(current($items)['name'],0,30) . "...";
    if(count($items) > 1){
        $oData['star'] .= "他" . count($items)-1 . "件";
    }
    $oData['jopanNum'] = "";
    $oData['itemInfo'] = "";
    $oData['countInfo'] = "1x" . count($items);
    $oData['proxy'] = "0";
    $oData['barcode'] = "*".$oData['orderNumber']."*";

//    print_r($oData);
//    echo "<br />";
//    echo "<hr />";

    foreach($oData as $key => $val){
        $oData[$key] = mb_convert_encoding($val, 'SJIS-win','UTF-8');
    }
    $saver[] = $oData;
}
//exit();

header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=".date("Y-m-d_")."sagawa_".$site_code["fullname"]."_uploader.csv");
header("Pragma: no-cache");
header("Expires: 0");
$out = fopen("php://output", 'w');
foreach ($saver as $key => $data)
{
    if($key == 0){
        fputcsv($out, array_keys((array)$data));
    }
    fputcsv($out, $data);
}
// foreach($error_list as $code => $data){
// fputcsv($out,$data);
// }
fclose($out);