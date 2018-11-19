<?php
include("../load_excel.php");
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('패킹리스트');
//set heading fields
//
$fields_list = explode(",","주문일시,주문번호,상품명,상품가격,배송비,대인수수료,편의점수수료,총결제액,관세율(%),결제방법,IP코드,입금상태,주문단계,발송일,동시발송,비고");
$th_address = range("A","P");
foreach($fields_list as $no => $title){
    $objPHPExcel->getActiveSheet()->setCellValue($th_address[$no].'1', $title);
}
$objPHPExcel->getActiveSheet()->getStyle('A1:N2')->getAlignment()->setWrapText(true);
// set heading styles
cellColor('A1:P1', 'D09896');
cellColor('H1:I1', 'AAAAAA');
cellColor('K1:L1', 'AAAAAA');
cellColor('N1', 'AAAAAA');
cellAlign('A1:P1');
cellFont("A1:P1",11,true,000000);
cellBorder("A1:P1");
cellWidth("A",18);
cellWidth("B",15);
cellWidth("C",40);
cellWidth("D:F",13);
cellWidth("G",16);
cellWidth("H",13);
cellWidth("I:L",20);
cellWidth("M:N",13);
cellWidth("O:P",13);
cellHeight("1",20);
$site_code = getSiteOrderCode();
//set Data

foreach($order_list as $no => $order_id){
    $order = new WC_Order( $order_id );
    $objPHPExcel->getActiveSheet()->setCellValue("A" . ($no+2),$order->get_date_created()->format("Y.m.d H:i"));
    $objPHPExcel->getActiveSheet()->setCellValue("B" . ($no+2),$site_code["order_code"] . trim(str_replace('#', '', $order->get_order_number())));
    // Sum line item costs.
    $cart_total = 0;
    $cart_tax_total = 0;
    $items = $order->get_items();
    $item_title = false;
    foreach ( $items as $key => $item ) {
        if(!$item_title){
            $item_title = $item["name"];
        }
        //총소비세
        $cart_tax_total += $item->get_total_tax();
        //총상품가
        $cart_total    += $item->get_total();
    }
    $item_subtotal = $cart_total+round($cart_tax_total);

    if(count($items) > 1){
        $item_title .= sprintf(" 외 %d종", count($items));
    }
    ##상품명
    $objPHPExcel->getActiveSheet()->setCellValue("C" . ($no+2),$item_title);
    ##상품가
    $objPHPExcel->getActiveSheet()->setCellValue("D" . ($no+2),$item_subtotal);
    ##배송비
    $shipping = number_format($order->get_shipping_total());
    $objPHPExcel->getActiveSheet()->setCellValue("E" . ($no+2),$shipping);

    ##대인수수료
    $fee_total=0;
    if($order->payment_method == "codpf"){
        $fee_total = 0;
        foreach ( $order->get_fees() as $fee ) {
            $fee_total += $fee->get_amount();
        }
    }
    $fee_totals = number_format(($fee_total*1.08));
    $objPHPExcel->getActiveSheet()->setCellValue("F" . ($no+2),$fee_totals);
    ##편의점수수료
    if ($order->payment_method == 'zeus_cs') {
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
    $objPHPExcel->getActiveSheet()->setCellValue("G" . ($no+2),$total_Convenience);
    ##총 결제액
    $objPHPExcel->getActiveSheet()->setCellValue("H" . ($no+2),($item_subtotal + $fee_totals + $total_Convenience + $shipping));

    if($order->get_meta('평균 관세율')!="")
    $perper = $order->get_meta('평균 관세율');
    else $perper = "확인 중";

    ##관세율
    $objPHPExcel->getActiveSheet()->setCellValue("I" . ($no+2),floatval($perper));
    ##결제방법
    if(get_payment_method($order->payment_method)=="신용카드")
        $payment='카드결제';
    elseif(get_payment_method($order->payment_method)=="대인결제")
        $payment='대인결제';
    elseif(get_payment_method($order->payment_method)=="편의점")
        $payment='편의점결제';
    elseif(get_payment_method($order->payment_method)=="기타")
        $payment='기타';
    $objPHPExcel->getActiveSheet()->setCellValue("J" . ($no+2),$payment);

    ##입금상태
    if($order->payment_method=="codpf") {
        if ($status_list["wc-" . $order->get_status()] == "완료" or $status_list["wc-" . $order->get_status()] == "환불") {
            $status = "입금";
            if($site_code["fullname"]=="sweetplus")
                $ip_address = '';
            elseif($site_code["fullname"]=="modernbuy")
                $ip_address = '';
        }
        else$status = "미입금";
    }
    elseif($order->payment_method=="zeus_cs") {
        if ($order->get_meta('payment_status')=="complete") {
            $status = "입금";
            if($site_code["fullname"]=="sweetplus")
                $ip_address = '2132000268';
            elseif($site_code["fullname"]=="modernbuy")
                $ip_address = '2132000299';
        }
        else$status = "미입금";

    }
    elseif($order->payment_method=="zeus_cc"){
        if ($order->get_meta('payment_status')=="complete") {
            $status = "입금";
            if($site_code["fullname"]=="sweetplus")
                $ip_address = '2012007001';
            elseif($site_code["fullname"]=="modernbuy")
                $ip_address = '2012007284';
        }
        else$status = "미입금";
    }
    else{
        if ($order->get_meta('payment_status')=="complete") {
            $status = "입금";
            if($site_code["fullname"]=="sweetplus")
                $ip_address = '2082000772';
            elseif($site_code["fullname"]=="modernbuy")
                $ip_address = '2082000820';
        }
        else$status = "미입금";
    }

    $objPHPExcel->getActiveSheet()->setCellValue("K" . ($no+2),$ip_address);
    $objPHPExcel->getActiveSheet()->setCellValue("L" . ($no+2),$status);
    /*##입금일시
    $objPHPExcel->getActiveSheet()->setCellValue("I" . ($no+2),get_post_meta( trim(str_replace('#', '', $order->get_order_number())), '_paid_date', true));
    */
    ##주문단계
    $objPHPExcel->getActiveSheet()->setCellValue("M" . ($no+2),$status_list["wc-".$order->get_status()]);
    ##발송일
    $objPHPExcel->getActiveSheet()->setCellValue("N" . ($no+2),$order->get_meta('ywot_pick_up_date' ));
    ##비고
    $objPHPExcel->getActiveSheet()->setCellValue("P" . ($no+2),$order->get_meta('비고' ));
    $range_id = "A" . ($no+2) . ":P" . ($no+2);
    cellHeight($no+2,20);
    cellBorder($range_id);
    cellAlign($range_id);
    cellFont($range_id,10,false,000000);
}
// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="orderlist_'.$site_code["fullname"].'_'.date('Ymd').'.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');
// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
ob_end_clean();
$objWriter->save('php://output');
exit;

?>