<?php
global $wpdb;
global $woocommerce;

if($_FILES['upfile']['name']!="") {

// 저장될 디렉토리
    $upfile_dir = "./upfile/";

//CSV데이타 추출시 한글깨짐방지
//setlocale(LC_CTYPE, 'ko_KR.utf8');
    setlocale(LC_CTYPE, 'ko_KR.eucKR'); // CSV 한글 깨짐 문제

//장시간 데이터 처리될경우
    set_time_limit(0);

    echo('<meta http-equiv="content-type" content="text/html; charset=utf-8">');

    $upfile_name = $_FILES['upfile']['name']; // 파일이름
    $upfile_type = $_FILES['upfile']['type']; // 확장자
    $upfile_size = $_FILES['upfile']['size']; // 파일크기
    $upfile_tmp = $_FILES['upfile']['tmp_name']; // 임시 디렉토리에 저장된 파일명

    $UpFilePathInfo = pathinfo($upfile_name);
    $UpFileExt = strtolower($UpFilePathInfo["extension"]);

    if ($UpFileExt != "xls" && $UpFileExt != "xlsx" && $UpFileExt != "csv") {
        echo "<script>
		alert('엑셀파일 및 CSV파일만 업로드 가능합니다. (csv , xls , xlsx 확장자의 파일포멧)');
		location.href='javascript:history.back()';
		</script>";
        exit;
    }

    $upload_file = $upfile_dir . $upfile_name;

    if ($upfile_name) {

        if (strlen($upfile_size) < 7) {
            $filesize = sprintf("%0.2f KB", $upfile_size / 1000);
        } else {
            $filesize = sprintf("%0.2f MB", $upfile_size / 1000000);
        }

        if (move_uploaded_file($upfile_tmp, "$upload_file")) {
        } else {
            echo '디렉토리에 복사실패';
        }
    }


    require_once __DIR__.'./../Classes/PHPExcel.php';

    $objPHPExcel = new PHPExcel();

    require_once __DIR__.'./../Classes/PHPExcel/IOFactory.php';

    $filename = $upload_file;

    $wearcount=$_POST['wear-list-count'];
    $wearpage=$_POST['wear-list-page'];

    try {

        // 업로드 된 엑셀 형식에 맞는 Reader객체를 만든다.
        $objExcel = PHPExcel_IOFactory::load($filename);

        // 첫번째 시트를 선택

        $objExcel->setActiveSheetIndex(0);

        $objWorksheet = $objExcel->getActiveSheet();

        $rowIterator = $objWorksheet->getRowIterator();

        foreach ($rowIterator as $row) { // 모든 행에 대해서

            $cellIterator = $row->getCellIterator();

            $cellIterator->setIterateOnlyExistingCells(false);

        }

        $maxRow = $objWorksheet->getHighestRow();

        for ($i = 1; $i <= $maxRow; $i++) {


            $A = $objWorksheet->getCell('A' . $i)->getValue(); // NO 열
            $B = $objWorksheet->getCell('B' . $i)->getValue(); // 주문번호 열
            $C = $objWorksheet->getCell('C' . $i)->getValue(); // 배송비 열
            $D = $objWorksheet->getCell('D' . $i)->getValue();
            $E = $objWorksheet->getCell('E' . $i)->getValue();
            $F = $objWorksheet->getCell('F' . $i)->getValue();
            $G = $objWorksheet->getCell('G' . $i)->getValue();
            $H = $objWorksheet->getCell('H' . $i)->getValue();
            $I = $objWorksheet->getCell('I' . $i)->getValue();
            $J = $objWorksheet->getCell('J' . $i)->getValue();
            $K = $objWorksheet->getCell('K' . $i)->getValue();
            $L = $objWorksheet->getCell('L' . $i)->getValue();
            $M = $objWorksheet->getCell('M' . $i)->getValue();
            $N = $objWorksheet->getCell('N' . $i)->getValue();
            $O = $objWorksheet->getCell('O' . $i)->getValue();
            $P = $objWorksheet->getCell('P' . $i)->getValue();
            $Q = $objWorksheet->getCell('Q' . $i)->getValue();
            $R = $objWorksheet->getCell('R' . $i)->getValue();
            $S = $objWorksheet->getCell('S' . $i)->getValue();
            $T = $objWorksheet->getCell('T' . $i)->getValue();
            $U = $objWorksheet->getCell('U' . $i)->getValue();
            $V = $objWorksheet->getCell('V' . $i)->getValue();
            $W = $objWorksheet->getCell('W' . $i)->getValue();
            $X = $objWorksheet->getCell('Y' . $i)->getValue();
            $Y = $objWorksheet->getCell('X' . $i)->getValue();
            $Z = $objWorksheet->getCell('Z' . $i)->getValue();
            $AA = $objWorksheet->getCell('AA' . $i)->getValue();
            $AB = $objWorksheet->getCell('AB' . $i)->getValue();

            /* echo "<tr>";
             echo  "<td>" . $A . "</td>" . "<td>" . $B . "</td>" . "<td>" . $C . "</td>"
                 . "<td>" . $D . "</td>" . "<td>" . $E . "</td>" . "<td>" . $F . "</td>"
                 . "<td>" . $G . "</td>" . "<td>" . $H . "</td>" . "<td>" . $I . "</td>"
                 . "<td>" . $J . "</td>" . "<td>" . $K . "</td>" . "<td>" . $L . "</td>"
                 . "<td>" . $M . "</td>" . "<td>" . $N . "</td>" . "<td>" . $O . "</td>"
                 . "<td>" . $P . "</td>" . "<td>" . $Q . "</td>" . "<td>" . $R . "</td>"
                 . "<td>" . $S . "</td>" . "<td>" . $T . "</td>" . "<td>" . $U . "</td>"
                 . "<td>" . $V . "</td>" . "<td>" . $W . "</td>" . "<td>" . $X . "</td>"
                 . "<td>" . $Y . "</td>" . "<td>" . $Z . "</td>" . "<td>" . $AA . "</td>"
                 . "<td>" . $AB . "</td>";
             echo "</tr>";*/
            $read[$i] = array('A' => $A, 'B' => $B, 'C' => $C, 'D' => $D, 'E' => $E, 'F' => $F,
                'G' => $G, 'H' => $H, 'I' => $I, 'J' => $J, 'K' => $K, 'L' => $L, 'M' => $M,
                'N' => $N, 'O' => $O, 'P' => $P, 'Q' => $Q, 'R' => $R, 'S' => $S, 'T' => $T,
                'U' => $U, 'V' => $V, 'W' => $W, 'X' => $X, 'Y' => $Y, 'Z' => $Z, 'AA' => $AA, 'AB' => $AB);

        }
    } catch (exception $e) {

        echo '엑셀파일을 읽는도중 오류가 발생하였습니다.';

    }
    $a = 1;
    $b = 0;

    for ($i = 2; $i <= $maxRow; $i++) {
        if ($read[$i]['C'] == "") continue;

        $category = '';
        $category_id = '';
        $kocategory = '';
        $kocategory_id = '';
        ##상의
        if ((strpos($read[$i]['D'], 'Tシャツ') !== false || strpos($read[$i]['Z'], 'Tシャツ') !== false ||
                strpos($read[$i]['D'], 'カットソ') !== false || strpos($read[$i]['Z'], 'カットソ') !== false) &&
            (strpos($read[$i]['D'], 'セーター') !== false || strpos($read[$i]['Z'], 'セーター') !== false)
        ) {
            $category = 'トップス';
            $category_id = '101';
            $kocategory = 'Tシャツ・カットソー';
            $kocategory_id = '2001';
        }
        if ((strpos($read[$i]['D'], 'シャツ') !== false || strpos($read[$i]['Z'], 'シャツ') !== false) ||
            (strpos($read[$i]['D'], 'ブラウス') !== false || strpos($read[$i]['Z'], 'ブラウス') !== false)
        ) {
            $category = 'トップス';
            $category_id = '101';
            $kocategory = 'シャツ・ブラウス';
            $kocategory_id = '2004';
        } elseif (strpos($read[$i]['D'], 'ニット') !== false || strpos($read[$i]['Z'], 'ニット') !== false ||
            strpos($read[$i]['D'], 'セーター') !== false || strpos($read[$i]['Z'], 'セーター') !== false
        ) {
            $category = 'トップス';
            $category_id = '101';
            $kocategory = 'ニット・セーター';
            $kocategory_id = '2028';
        } elseif (strpos($read[$i]['D'], 'パーカー') !== false || strpos($read[$i]['Z'], 'パーカー') !== false) {
            $category = 'トップス';
            $category_id = '101';
            $kocategory = 'パーカー';
            $kocategory_id = '2021';
        } elseif (strpos($read[$i]['D'], 'スウェット') !== false || strpos($read[$i]['Z'], 'スウェット') !== false) {
            $category = 'トップス';
            $category_id = '101';
            $kocategory = 'スウェット';
            $kocategory_id = '2020';
        } elseif (strpos($read[$i]['D'], 'カーディガン') !== false || strpos($read[$i]['Z'], 'カーディガン') !== false) {
            $category = 'トップス';
            $category_id = '101';
            $kocategory = 'Tシャツ・カットソー';
            $kocategory_id = '2023';
        } elseif (strpos($read[$i]['D'], 'アンサンブル') !== false || strpos($read[$i]['Z'], 'アンサンブル') !== false) {
            $category = 'トップス';
            $category_id = '101';
            $kocategory = 'アンサンブル';
            $kocategory_id = '2310';
        } elseif (strpos($read[$i]['D'], 'ジャージ') !== false || strpos($read[$i]['Z'], 'ジャージ') !== false) {
            $category = 'トップス';
            $category_id = '101';
            $kocategory = 'ジャージ';
            $kocategory_id = '2192';
        } elseif (strpos($read[$i]['D'], 'チューブトップ') !== false || strpos($read[$i]['Z'], 'チューブトップ') !== false) {
            $category = 'トップス';
            $category_id = '101';
            $kocategory = 'チューブトップ';
            $kocategory_id = '2010';
        } elseif (strpos($read[$i]['D'], 'キャミソール') !== false || strpos($read[$i]['Z'], 'キャミソール') !== false) {
            $category = 'トップス';
            $category_id = '101';
            $kocategory = 'キャミソール';
            $kocategory_id = '2008';
        } elseif (strpos($read[$i]['D'], 'タンクトップ') !== false || strpos($read[$i]['Z'], 'タンクトップ') !== false) {
            $category = 'トップス';
            $category_id = '101';
            $kocategory = 'タンクトップ';
            $kocategory_id = '2009';
        } ##바지
        elseif ((strpos($read[$i]['D'], 'ボトムス') !== false || strpos($read[$i]['Z'], 'ボトムス') !== false) and
            (strpos($read[$i]['D'], 'デニム') !== false || strpos($read[$i]['Z'], 'デニム')) !== false
        ) {
            $category = 'パンツ';
            $category_id = '112';
            $kocategory = 'デニムパンツ';
            $kocategory_id = '2041';
        } elseif ((strpos($read[$i]['D'], 'ボトムス') !== false || strpos($read[$i]['Z'], 'ボトムス') !== false) and
            (strpos($read[$i]['D'], 'スラックス') !== false || strpos($read[$i]['Z'], 'スラックス') !== false)
        ) {
            $category = 'パンツ';
            $category_id = '112';
            $kocategory = 'スラックス';
            $kocategory_id = '2349';
        } elseif ((strpos($read[$i]['D'], 'パンツ') !== false || strpos($read[$i]['Z'], 'パンツ') !== false) &&
            (strpos($read[$i]['D'], 'ボトムス') !== false || strpos($read[$i]['Z'], 'ボトムス') !== false)
        ) {
            $category = 'パンツ';
            $category_id = '112';
            $kocategory = 'パンツ';
            $kocategory_id = '2040';
        } ##스커트
        elseif ((strpos($read[$i]['D'], 'デニム') !== false || strpos($read[$i]['Z'], 'デニム') !== false) &&
            (strpos($read[$i]['D'], 'スカート') !== false || strpos($read[$i]['Z'], 'スカート') !== false)
        ) {
            $category = 'スカート';
            $category_id = '113';
            $kocategory = 'デニムスカート';
            $kocategory_id = '2291';
        } elseif (strpos($read[$i]['D'], 'スカート') !== false || strpos($read[$i]['Z'], 'スカート') !== false) {
            $category = 'スカート';
            $category_id = '113';
            $kocategory = 'スカート';
            $kocategory_id = '2246';
        } ##속옷
        elseif ((strpos($read[$i]['D'], 'ブラ') !== false || strpos($read[$i]['Z'], 'ブラ') !== false) and
            (strpos($read[$i]['D'], 'ランジェリ') !== false || strpos($read[$i]['Z'], 'ランジェリ') !== false)
        ) {
            $category = 'マタニティ・ベビー';
            $category_id = '104';
            $kocategory = 'ブラ';
            $kocategory_id = '2012';
        } elseif ((strpos($read[$i]['D'], 'ショーツ') !== false || strpos($read[$i]['Z'], 'ショーツ') !== false) and
            (strpos($read[$i]['D'], 'ランジェリ') !== false || strpos($read[$i]['Z'], 'ランジェリ') !== false)
        ) {
            $category = 'マタニティ・ベビー';
            $category_id = '104';
            $kocategory = 'ショーツ';
            $kocategory_id = '2013';
        } elseif ((strpos($read[$i]['D'], 'ランジェリ') !== false || strpos($read[$i]['Z'], 'ランジェリ') !== false)
            && (strpos($read[$i]['D'], 'セット') !== false || strpos($read[$i]['Z'], 'セット') !== false)
        ) {
            $category = 'アンダーウェア';
            $category_id = '104';
            $kocategory = 'ブラ&ショーツ';
            $kocategory_id = '2015';
        } elseif (strpos($read[$i]['D'], 'ランジェリ') !== false || strpos($read[$i]['Z'], 'ランジェリ') !== false) {
            $category = 'アンダーウェア';
            $category_id = '104';
            $kocategory = 'その他アンダーウエア・インナー';
            $kocategory_id = '2016';
        } ##홈웨어
        elseif (strpos($read[$i]['D'], 'ホームウェア') !== false || strpos($read[$i]['Z'], 'ホームウェア') !== false &&
            (strpos($read[$i]['D'], 'セット') !== false || strpos($read[$i]['Z'], 'セット') !== false)
        ) {
            $category = 'アンダーウェア';
            $category_id = '104';
            $kocategory = 'ルームウェア';
            $kocategory_id = '2018';
        } ##원피스
        elseif ((strpos($read[$i]['D'], 'シャツ') !== false || strpos($read[$i]['Z'], 'シャツ') !== false) &&
            (strpos($read[$i]['D'], 'ワンピース') !== false || strpos($read[$i]['Z'], 'ワンピース') !== false)
        ) {
            $category = 'ワンピース';
            $category_id = '111';
            $kocategory = 'シャツワンピース';
            $kocategory_id = '2351';
        } elseif (strpos($read[$i]['D'], 'ワンピース') !== false || strpos($read[$i]['Z'], 'ワンピース') !== false) {
            $category = 'ワンピース';
            $category_id = '111';
            $kocategory = 'ワンピース';
            $kocategory_id = '2035';
        } ##수영복
        elseif ((strpos($read[$i]['D'], 'ラッシュガード') !== false || strpos($read[$i]['Z'], 'ラッシュガード') !== false)
            && strpos($read[$i]['Z'], '水着') !== false
        ) {
            $category = '水着・着物・浴衣';
            $category_id = '137';
            $kocategory = 'ラッシュガード';
            $kocategory_id = '2377';
        } elseif (strpos($read[$i]['D'], '水着') !== false || strpos($read[$i]['Z'], '水着') !== false) {
            $category = '水着・着物・浴衣';
            $category_id = '137';
            $kocategory = '水着';
            $kocategory_id = '2083';
        } ##레깅스
        elseif (strpos($read[$i]['D'], 'レギンス') !== false || strpos($read[$i]['Z'], 'レギンス') !== false) {
            $category = 'レッグウェア';
            $category_id = '132';
            $kocategory = 'レギンス・スパッツ';
            $kocategory_id = '2177';
        } elseif (strpos($read[$i]['D'], 'ストッキング') !== false || strpos($read[$i]['Z'], 'ストッキング') !== false ||
            strpos($read[$i]['D'], 'タイツ') !== false || strpos($read[$i]['Z'], 'タイツ') !== false
        ) {
            $category = 'レッグウェア';
            $category_id = '132';
            $kocategory = 'タイツ・ストッキング';
            $kocategory_id = '2176';
        } ##자켓
        elseif (strpos($read[$i]['D'], 'ジャケット') !== false || strpos($read[$i]['Z'], 'ジャケット') !== false) {
            $category = 'ジャケット・アウター';
            $category_id = '108';
            $kocategory = 'その他アウター';
            $kocategory_id = '2169';
        } ##마타니티 웨어
        elseif (strpos($read[$i]['D'], 'マタニティウェア') !== false || strpos($read[$i]['Z'], 'マタニティウェア') !== false) {
            $category = 'マタニティ・ベビー';
            $category_id = '126';
            $kocategory = 'マタニティウェア';
            $kocategory_id = '2156';
        }
        if ($read[$i]['H'] == "") {
            $description = "商品説明";
        } else $description = $read[$i]['H'];

        $A = $read[$i]['D'];
        $B = '';
        $C = "59473";
        $D = $read[$i]['C'];
        $E = "女性";
        $F = '2';
        $G = 'SWEETPLUS';
        $H = '29207';
        $I = $category;
        $J = $category_id;
        $K = $kocategory;
        $L = $kocategory_id;
        $M = '日本';
        $N = '1';
        $O = $description;
        $P = '通常';
        $Q = '1';
        $R = '通常';
        $S = '1';
        $T = '500';
        $U = '500';
        $V = 'その他';
        $W = '39';
        $X = 'FREE';
        $Y = '3216';
        $Z = $read[$i]['C'];
        $AA = 'サイトURL';
        $AB = '1';

        $b += $a;

        $rist[$b] = array('A' => $A, 'B' => $B, 'C' => $C, 'D' => $D, 'E' => $E, 'F' => $F,
            'G' => $G, 'H' => $H, 'I' => $I, 'J' => $J, 'K' => $K, 'L' => $L, 'M' => $M,
            'N' => $N, 'O' => $O, 'P' => $P, 'Q' => $Q, 'R' => $R, 'S' => $S, 'T' => $T,
            'U' => $U, 'V' => $V, 'W' => $W, 'X' => $X, 'Y' => $Y, 'Z' => $Z, 'AA' => $AA, 'AB' => $AB, 'NO' => $b);
    }
    $MaxRow = $b;

    $limit=$wearcount;
    $page=$wearpage;
    $Remainpage=$limit;
    if($page==1){
        $for=1;
        if($limit*$page > $MaxRow) {
            $limited = $MaxRow;
            $Remainpage = $limit * $page - $MaxRow;
        }
        else
            $limited=($limit*$page)-1;

    }
    else {
        $for=($limit*$page)-($limit)+2-$page;
        if ($limit * $page > $MaxRow){
            $limited = $MaxRow;
            $Remainpage = $limit*$page - $MaxRow;
        }
        else
            $limited = ($limit * $page)-$page;
    }
    $Remain = ($MaxRow - $limit*$page);
    $a=1;
    $b=2;

    echo "<div align='center'>";
    echo "총 상품 '".$MaxRow."' 개 중<br>";
    echo "선택된 상품들은 '".$wearpage."' 페이지의 총 '".$Remainpage."' 개 입니다.<br>";
    echo "남은 상품은 '".$Remain."' 개 입니다.<br>";

    echo "<table border='1'>";
    echo "<th>No</th><th>商品名</th><th>バーコードNo</th><th>取り扱いECサイトID</th><th>ブランド品番</th><th>商品性別</th>
    <th>商品性別ID</th><th>ブランド名</th><th>ブランドID</th><th>親カテゴリ</th><th>親カテID</th><th>子カテゴリ</th>
    <th>子カテID</th><th>販売国</th><th>販売国ID</th><th>商品説明</th><th>販売タイプ</th><th>販売タイプID</th>
    <th>価格タイプ</th><th>価格タイプID</th><th>定価</th><th>セール価格</th><th>色</th><th>色ID</th><th>サイズ</th>
    <th>サイズID</th><th>CS品番</th><th>ECサイト商品詳細ページURL</th><th>親アイテムフラグ</th>";

    for ($i=$for; $i<=$limited; $i++) {

        echo "<tr>";
        echo "<td>" . $b . "</td>";
        echo "<td>" . $rist[$i]['A'] . "</td>";
        echo "<td>" . $rist[$i]['B'] . "</td>";
        echo "<td>" . $rist[$i]['C'] . "</td>";
        echo "<td>" . $rist[$i]['D'] . "</td>";
        echo "<td>" . $rist[$i]['E'] . "</td>";
        echo "<td>" . $rist[$i]['F'] . "</td>";
        echo "<td>" . $rist[$i]['G'] . "</td>";
        echo "<td>" . $rist[$i]['H'] . "</td>";
        echo "<td>" . $rist[$i]['I'] . "</td>";
        echo "<td>" . $rist[$i]['J'] . "</td>";
        echo "<td>" . $rist[$i]['K'] . "</td>";
        echo "<td>" . $rist[$i]['L'] . "</td>";
        echo "<td>" . $rist[$i]['M'] . "</td>";
        echo "<td>" . $rist[$i]['N'] . "</td>";
        echo "<td>" . $rist[$i]['O'] . "</td>";
        echo "<td>" . $rist[$i]['P'] . "</td>";
        echo "<td>" . $rist[$i]['Q'] . "</td>";
        echo "<td>" . $rist[$i]['R'] . "</td>";
        echo "<td>" . $rist[$i]['S'] . "</td>";
        echo "<td>" . $rist[$i]['T'] . "</td>";
        echo "<td>" . $rist[$i]['U'] . "</td>";
        echo "<td>" . $rist[$i]['V'] . "</td>";
        echo "<td>" . $rist[$i]['W'] . "</td>";
        echo "<td>" . $rist[$i]['X'] . "</td>";
        echo "<td>" . $rist[$i]['Y'] . "</td>";
        echo "<td>" . $rist[$i]['Z'] . "</td>";
        echo "<td>" . $rist[$i]['AA'] . "</td>";
        echo "<td>" . $rist[$i]['AB'] . "</td>";
        $product = new WC_Product($rist[$i]['D']);
        echo $product->get_regular_price();
        echo $product->get_sale_price();
        echo $product->get_price();
        echo "</tr>";
        $b += $a;
    }

    echo "</table>";

    $rist1 = urlencode(serialize($rist));
    echo "<form method='POST' action='wear_csv_save.php'>";
    echo "<input type='hidden' name='rist' value='$rist1'>
            <input type='hidden' name='wear-list-count' value='$wearcount'>
            <input type='hidden' name='wear-list-page' value='$wearpage'>
            <input type='hidden' name='maxrow' value='$MaxRow'>
            <table><tr><th><input type='submit' value='변환'></th>";
    echo "</form><form method='POST'>";
    echo "<th><input type='submit' value='돌아가기' formaction='../../../../wp-admin/admin.php?page=wc4amuz_japanshop_datacenter_output&tab=product'></th></form></table>";
    echo "</div>";
}
?>
