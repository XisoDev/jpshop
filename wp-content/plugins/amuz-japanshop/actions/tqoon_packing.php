<?php

include("../load_excel.php");
$site_code = getSiteOrderCode();
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('패킹리스트');


//set heading fields
$objPHPExcel->getActiveSheet()->mergeCells('A1:A2')->setCellValue('A1', 'No.');
$objPHPExcel->getActiveSheet()->mergeCells('B1:B2')->setCellValue('B1', '발송');
$objPHPExcel->getActiveSheet()->mergeCells('C1:C2')->setCellValue('C1', '주문번호');
$objPHPExcel->getActiveSheet()->mergeCells('D1:D2')->setCellValue('D1', '송장번호'.PHP_EOL.'(숫자만기입)');
$objPHPExcel->getActiveSheet()->mergeCells('E1:E2')->setCellValue('E1', '상품명'.PHP_EOL.'(영문대문자)');
$objPHPExcel->getActiveSheet()->mergeCells('F1:F2')->setCellValue('F1', '재    질'.PHP_EOL.'(영문대문자)');
$objPHPExcel->getActiveSheet()->mergeCells('G1:G2')->setCellValue('G1', '상품갯수');
$objPHPExcel->getActiveSheet()->mergeCells('H1:H2')->setCellValue('H1', '박스수');
$objPHPExcel->getActiveSheet()->mergeCells('I1:K1')->setCellValue('I1', '박스사이즈(Cm)');
$objPHPExcel->getActiveSheet()->setCellValue('I2', '가로');
$objPHPExcel->getActiveSheet()->setCellValue('J2', '세로');
$objPHPExcel->getActiveSheet()->setCellValue('K2', '높이');
$objPHPExcel->getActiveSheet()->mergeCells('L1:L2')->setCellValue('L1', '판매가(엔)');
$objPHPExcel->getActiveSheet()->mergeCells('M1:M2')->setCellValue('M1', '묶음배송'.PHP_EOL.'분납배송');
$objPHPExcel->getActiveSheet()->mergeCells('N1:N2')->setCellValue('N1', '비고');

$objPHPExcel->getActiveSheet()->getStyle('A1:N2')->getAlignment()->setWrapText(true);
// set heading styles
cellColor('A1:N2', 'c9d9ef');
cellAlign('A1:N2');
cellFont("A1:N2",11,true,000000);
cellBorder("A1:N2");

cellWidth("A:B",7);
cellWidth("C:F",16);
cellWidth("G:H",6);
cellWidth("I:K",6);
cellWidth("L:N",13);
cellHeight("1:2",20);



//set Data
foreach($order_list as $no => $order_id){
    $order = new WC_Order( $order_id );


    $items = $order->get_items();
    reset($items);
    $first_item = current($items);
    $product_id = $first_item['product_id'];

    $cart_total = 0;
    $cart_tax_total = 0;
    foreach ( $items as $key => $item ) {
        //총소비세
        $cart_tax_total += $item->get_total_tax();
        //총상품가
        $cart_total    += $item->get_total();
    }

    //소비세 + 상품가 + 배송료 (결제수수료 제외한 토탈금액)
    $exclude_fee_total = number_format($cart_tax_total + $cart_total + $order->get_shipping_total());

    $objPHPExcel->getActiveSheet()->setCellValue("A" . ($no+3),($no+1));
    $objPHPExcel->getActiveSheet()->setCellValue("B" . ($no+3),"항공");
    $objPHPExcel->getActiveSheet()->setCellValue("C" . ($no+3),$site_code["order_code"] . trim(str_replace('#', '', $order->get_order_number())));
    $objPHPExcel->getActiveSheet()->setCellValueExplicit("D" . ($no+3),$order->get_meta('ywot_tracking_code'), PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValue("E" . ($no+3),get_post_meta( $product_id, '수출용_상품명', true));
    $objPHPExcel->getActiveSheet()->setCellValue("F" . ($no+3),get_post_meta( $product_id, '수출용_재질', true));
    $objPHPExcel->getActiveSheet()->setCellValue("G" . ($no+3),count($items));
    $objPHPExcel->getActiveSheet()->setCellValue("H" . ($no+3),"1");
    $objPHPExcel->getActiveSheet()->setCellValue("I" . ($no+3),"0");
    $objPHPExcel->getActiveSheet()->setCellValue("K" . ($no+3),"");
    $objPHPExcel->getActiveSheet()->setCellValue("K" . ($no+3),"");
    $objPHPExcel->getActiveSheet()->setCellValue("L" . ($no+3),$exclude_fee_total);
    $objPHPExcel->getActiveSheet()->setCellValue("M" . ($no+3),"");
    $objPHPExcel->getActiveSheet()->setCellValue("N" . ($no+3),get_post_meta( $product_id, '수출용_관세코드', true));

    $range_id = "A" . ($no+3) . ":N" . ($no+3);
    cellHeight($no+3,20);
    cellBorder($range_id);
    cellAlign($range_id);
    cellFont($range_id,10,false,000000);
}


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="packinglist_'.$site_code["fullname"].'_'.date('Ymd').'.xlsx"');
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
