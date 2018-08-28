<?php
$maxRow=$_POST['maxrow'];
$read= unserialize(urldecode($_POST['rist']));
$wearcount=$_POST['wear-list-count'];
$wearpage=$_POST['wear-list-page'];
include("../load_excel.php");

$objPHPExcel->getActiveSheet()->setTitle('商品情報入力シート');

$objPHPExcel->getActiveSheet()->setCellValue("A" . 1,'商品名');
$objPHPExcel->getActiveSheet()->setCellValue("B" . 1,'バーコードNo');
$objPHPExcel->getActiveSheet()->setCellValue("C" . 1,'取り扱いECサイトID');
$objPHPExcel->getActiveSheet()->setCellValue("D" . 1,'ブランド品番');
$objPHPExcel->getActiveSheet()->setCellValue("E" . 1,'商品性別');
$objPHPExcel->getActiveSheet()->setCellValue("F" . 1,'商品性別ID');
$objPHPExcel->getActiveSheet()->setCellValue("G" . 1,'ブランド名');
$objPHPExcel->getActiveSheet()->setCellValue("H" . 1,'ブランドID');
$objPHPExcel->getActiveSheet()->setCellValue("I" . 1,'親カテゴリ');
$objPHPExcel->getActiveSheet()->setCellValue("J" . 1,'親カテID');
$objPHPExcel->getActiveSheet()->setCellValue("K" . 1,'子カテゴリ');
$objPHPExcel->getActiveSheet()->setCellValue("L" . 1,'子カテID');
$objPHPExcel->getActiveSheet()->setCellValue("M" . 1,'販売国');
$objPHPExcel->getActiveSheet()->setCellValue("N" . 1,'販売国ID');
$objPHPExcel->getActiveSheet()->setCellValue("O" . 1,'商品説明');
$objPHPExcel->getActiveSheet()->setCellValue("P" . 1,'販売タイプ');
$objPHPExcel->getActiveSheet()->setCellValue("Q" . 1,'販売タイプID');
$objPHPExcel->getActiveSheet()->setCellValue("R" . 1,'価格タイプ');
$objPHPExcel->getActiveSheet()->setCellValue("S" . 1,'価格タイプID');
$objPHPExcel->getActiveSheet()->setCellValue("T" . 1,'定価');
$objPHPExcel->getActiveSheet()->setCellValue("U" . 1,'セール価格');
$objPHPExcel->getActiveSheet()->setCellValue("V" . 1,'色');
$objPHPExcel->getActiveSheet()->setCellValue("W" . 1,'色ID');
$objPHPExcel->getActiveSheet()->setCellValue("X" . 1,'サイズ');
$objPHPExcel->getActiveSheet()->setCellValue("Y" . 1,'サイズID');
$objPHPExcel->getActiveSheet()->setCellValue("Z" . 1,'CS品番');
$objPHPExcel->getActiveSheet()->setCellValue("AA" . 1,'ECサイト商品詳細ページURL');
$objPHPExcel->getActiveSheet()->setCellValue("AB" . 1,'親アイテムフラグ');



$limit=$wearcount;
$page=$wearpage;

if($page==1){
    $for=1;
    if($limit*$page > $maxRow)
        $limited=$maxRow;
    else
        $limited=($limit*$page)-1;

}
else {
    $for=($limit*$page)-($limit)+2-$page;
    if ($limit * $page > $maxRow)
        $limited = $maxRow;
    else
        $limited = ($limit * $page)-$page;
}

$a=1;
$b=2;

for($i=$for; $i<=$limited; $i++) {

    $objPHPExcel->getActiveSheet()->setCellValue("A" . ($b),$read[$i]['A']);
    $objPHPExcel->getActiveSheet()->setCellValue("B" . ($b),$read[$i]['B']);
    $objPHPExcel->getActiveSheet()->setCellValue("C" . ($b),$read[$i]['C']);
    $objPHPExcel->getActiveSheet()->setCellValue("D" . ($b),$read[$i]['D']);
    $objPHPExcel->getActiveSheet()->setCellValue("E" . ($b),$read[$i]['E']);
    $objPHPExcel->getActiveSheet()->setCellValue("F" . ($b),$read[$i]['F']);
    $objPHPExcel->getActiveSheet()->setCellValue("G" . ($b),$read[$i]['G']);
    $objPHPExcel->getActiveSheet()->setCellValue("H" . ($b),$read[$i]['H']);
    $objPHPExcel->getActiveSheet()->setCellValue("I" . ($b),$read[$i]['I']);
    $objPHPExcel->getActiveSheet()->setCellValue("J" . ($b),$read[$i]['J']);
    $objPHPExcel->getActiveSheet()->setCellValue("K" . ($b),$read[$i]['K']);
    $objPHPExcel->getActiveSheet()->setCellValue("L" . ($b),$read[$i]['L']);
    $objPHPExcel->getActiveSheet()->setCellValue("M" . ($b),$read[$i]['M']);
    $objPHPExcel->getActiveSheet()->setCellValue("N" . ($b),$read[$i]['N']);
    $objPHPExcel->getActiveSheet()->setCellValue("O" . ($b),$read[$i]['O']);
    $objPHPExcel->getActiveSheet()->setCellValue("P" . ($b),$read[$i]['P']);
    $objPHPExcel->getActiveSheet()->setCellValue("Q" . ($b),$read[$i]['Q']);
    $objPHPExcel->getActiveSheet()->setCellValue("R" . ($b),$read[$i]['R']);
    $objPHPExcel->getActiveSheet()->setCellValue("S" . ($b),$read[$i]['S']);
    $objPHPExcel->getActiveSheet()->setCellValue("T" . ($b),$read[$i]['T']);
    $objPHPExcel->getActiveSheet()->setCellValue("U" . ($b),$read[$i]['U']);
    $objPHPExcel->getActiveSheet()->setCellValue("V" . ($b),$read[$i]['V']);
    $objPHPExcel->getActiveSheet()->setCellValue("W" . ($b),$read[$i]['W']);
    $objPHPExcel->getActiveSheet()->setCellValue("X" . ($b),$read[$i]['X']);
    $objPHPExcel->getActiveSheet()->setCellValue("Y" . ($b),$read[$i]['Y']);
    $objPHPExcel->getActiveSheet()->setCellValue("Z" . ($b),$read[$i]['Z']);
    $objPHPExcel->getActiveSheet()->setCellValue("AA" . ($b),$read[$i]['AA']);
    $objPHPExcel->getActiveSheet()->setCellValue("AB" . ($b),$read[$i]['AB']);
    $b+=$a;
}
// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="orderlist_'.date('Ymd').'.xlsx"');
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
?>
