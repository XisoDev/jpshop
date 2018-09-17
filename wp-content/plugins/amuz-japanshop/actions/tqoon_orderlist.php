<?php
include("../load_excel.php");
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('패킹리스트');
//set heading fields
//
$fields_list = explode(",","주문일시,주문번호,상품명,상품가격,대인수수료,배송비,결제방법,입금상태,입금일시,발송일,수취완료여부,수취일,주문단계,비고");
$th_address = range("A","N");
foreach($fields_list as $no => $title){
    $objPHPExcel->getActiveSheet()->setCellValue($th_address[$no].'1', $title);
}
$objPHPExcel->getActiveSheet()->getStyle('A1:N2')->getAlignment()->setWrapText(true);
// set heading styles
cellColor('A1:N1', 'D09896');
cellColor('H1:I1', 'AAAAAA');
cellColor('K1:L1', 'AAAAAA');
cellColor('N1', 'AAAAAA');
cellAlign('A1:N1');
cellFont("A1:N1",11,true,000000);
cellBorder("A1:N1");
cellWidth("A",18);
cellWidth("B",15);
cellWidth("C",40);
cellWidth("D:H",13);
cellWidth("I:L",20);
cellWidth("M:N",13);
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
    $cart = $cart_total+round($cart_tax_total);
    if(count($items) > 1){
        $item_title = sprintf(" 외 %d종", count($items));
    }
    $objPHPExcel->getActiveSheet()->setCellValue("C" . ($no+2),$item_title);
    $objPHPExcel->getActiveSheet()->setCellValue("D" . ($no+2),number_format($cart));
    if($order->payment_method == "codpf"){
        $fee_total = 0;
        foreach ( $order->get_fees() as $fee ) {
            $fee_total += $fee->get_amount();
        }
        $objPHPExcel->getActiveSheet()->setCellValue("E" . ($no+2),number_format(($fee_total*1.08)));
    }else{
        $objPHPExcel->getActiveSheet()->setCellValue("E" . ($no+2),"0");
    }
    $objPHPExcel->getActiveSheet()->setCellValue("F" . ($no+2),number_format($order->get_shipping_total()));
    $objPHPExcel->getActiveSheet()->setCellValue("G" . ($no+2),get_payment_method($order->payment_method));
    $objPHPExcel->getActiveSheet()->setCellValue("H" . ($no+2),$status_list["wc-".$order->get_status()]);
    $objPHPExcel->getActiveSheet()->setCellValue("I" . ($no+2),get_post_meta( trim(str_replace('#', '', $order->get_order_number())), '_paid_date', true));
    $objPHPExcel->getActiveSheet()->setCellValue("J" . ($no+2),$order->get_meta('ywot_pick_up_date' ));
    $objPHPExcel->getActiveSheet()->setCellValue("N" . ($no+2),$order->get_meta('비고' ));
    $range_id = "A" . ($no+2) . ":N" . ($no+2);
    cellHeight($no+2,20);
    cellBorder($range_id);
    cellAlign($range_id);
    cellFont($range_id,10,false,000000);
}
// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="orderlist_'.$site_code["fullname"].'_'.date('Ymd').'.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');
// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;