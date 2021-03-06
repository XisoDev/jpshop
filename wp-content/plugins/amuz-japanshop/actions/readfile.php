<?php
global $wpdb;
if($_FILES['upfile']['name']!=""){

// 저장될 디렉토리
$upfile_dir ="./upfile/";

//CSV데이타 추출시 한글깨짐방지
//setlocale(LC_CTYPE, 'ko_KR.utf8');
setlocale(LC_CTYPE, 'ko_KR.eucKR'); // CSV 한글 깨짐 문제

//장시간 데이터 처리될경우
set_time_limit(0);

echo ('<meta http-equiv="content-type" content="text/html; charset=utf-8">');

$upfile_name = $_FILES['upfile']['name']; // 파일이름
$upfile_type = $_FILES['upfile']['type']; // 확장자
$upfile_size = $_FILES['upfile']['size']; // 파일크기
$upfile_tmp  = $_FILES['upfile']['tmp_name']; // 임시 디렉토리에 저장된 파일명
/*echo "upfile_name = ". $upfile_name ."<br>";
echo "upfile_type = ". $upfile_type ."<br>";
echo "upfile_size = ". $upfile_size ."<br>";
echo "upfile_tmp  = ". $upfile_tmp ."<br>";*/
    $UpFilePathInfo = pathinfo($upfile_name);
    $UpFileExt  = strtolower($UpFilePathInfo["extension"]);

    if($UpFileExt != "xls" && $UpFileExt != "xlsx") {
        echo "<script>
		alert('엑셀파일만 업로드 가능합니다. (xls, xlsx 확장자의 파일포멧)');
		location.href='javascript:history.back()';
		</script>";
        exit;
    }

$upload_file = $upfile_dir.$upfile_name;

if ($upfile_name){

    if ( strlen($upfile_size) < 7 ) {
        $filesize = sprintf("%0.2f KB", $upfile_size/1000);
    } else{
        $filesize = sprintf("%0.2f MB", $upfile_size/1000000);
    }

    if (move_uploaded_file($upfile_tmp,"$upload_file")) {
    } else {
        echo '디렉토리에 복사실패';
    }
}

require_once __DIR__.'./../Classes/PHPExcel.php';

$objPHPExcel = new PHPExcel();

require_once __DIR__.'./../Classes/PHPExcel/IOFactory.php';

$filename = $upload_file;

try {


    $objReader = PHPExcel_IOFactory::createReaderForFile($filename);

    // 읽기전용으로 설정

    $objReader->setReadDataOnly(true);

    // 엑셀파일을 읽는다

    $objExcel = $objReader->load($filename);

    // 첫번째 시트를 선택

    $objExcel->setActiveSheetIndex(0);

    $objWorksheet = $objExcel->getActiveSheet();

    $rowIterator = $objWorksheet->getRowIterator();

    foreach ($rowIterator as $row) { // 모든 행에 대해서

        $cellIterator = $row->getCellIterator();

        $cellIterator->setIterateOnlyExistingCells(false);

    }

    $maxRow = $objWorksheet->getHighestRow();
    echo "<div align='center'>";
    echo "<table style='width: 500px' border = '1' >";
    for ($i = 0 ; $i <= $maxRow ; $i++) {

        $NO = $objWorksheet->getCell('A' . $i)->getValue(); // NO 열
        $addr1 = $objWorksheet->getCell('C' . $i)->getValue(); // 주문번호 열
        $addr2 = $objWorksheet->getCell('I' . $i)->getValue(); // 배송비 열
        $order = array();


        if($addr2 ==0 )$addr2="0";
        if(!is_numeric($NO))continue;

        if ($addr1 != "" && $addr2 != "") {
            echo "<tr><td style='width: 20%'>".$NO." 번열</td>";

            $order_id = preg_replace("/[^0-9]*/s", "", $addr1);

            echo "<td  style='width: 40%' >주문번호 : " . $addr1."</td>";

            echo "<td  style='width: 40%'> 배송비 : " . $addr2 . " 엔 </td></tr>";

            $order['id']['order_id'] = $order_id;

            $order['id']['delivery'] = $addr2;

            $pp[]=$order['id'];
            }


        }
    echo "</table>";
}
catch (exception $e) {

    echo '엑셀파일을 읽는도중 오류가 발생하였습니다.';

}
    $x1 = urlencode(serialize($pp));
    $number = $maxRow - 2;
echo "이상이 엑셀 파일에서 추출한 값입니다 맞습니까?";

echo "<form id='aa' method='POST' action='../../../../wp-admin/admin.php?page=wc4amuz_japanshop_datacenter_output&tab=calculate'>";
echo "<input type='hidden' id = 'number' name = 'number' value = '$number'>";
echo "<input type='hidden' id = 'pirce' name = 'view' value = '$x1'>";
echo "<table>";
echo "<tr><td><input type='submit' value = '네'></td>";
echo "</form><form method='POST'>";
echo "<td><input type='submit' value='아니오' formaction='../../../../wp-admin/admin.php?page=wc4amuz_japanshop_datacenter_output&tab=calculate'></td></tr> ";
    echo "</form></table></div>";

unlink($upload_file);
}
else
{
    echo "<script>
		alert('엑셀 파일을 올려주세요');
		location.href='javascript:history.back()';
		</script>";
}
    ?>