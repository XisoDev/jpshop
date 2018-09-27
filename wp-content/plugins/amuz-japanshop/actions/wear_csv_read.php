<?php
include_once '../../../../wp-config.php';
include_once '../../../../global_functions.php';
global $wpdb;

$site_code = getSiteOrderCode();
$site_id = get_site_id();

$site_name = strtoupper($site_code["fullname"]);
$site_id[$site_name];

echo $site_id[$site_name];
echo "=";
echo $site_name;
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

    $site_code = $_POST['site_code'];

    try {

        $objReader = PHPExcel_IOFactory::createReaderForFile($filename);

        print_r($objReader);
        // 업로드 된 엑셀 형식에 맞는 Reader객체를 만든다.

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
            $X = $objWorksheet->getCell('X' . $i)->getValue();
            $Y = $objWorksheet->getCell('Y' . $i)->getValue();
            $Z = $objWorksheet->getCell('Z' . $i)->getValue();
            $AA = $objWorksheet->getCell('AA' . $i)->getValue();
            $AB = $objWorksheet->getCell('AB' . $i)->getValue();
            $AC = $objWorksheet->getCell('AF' . $i)->getValue();

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
                'U' => $U, 'V' => $V, 'W' => $W, 'X' => $X, 'Y' => $Y, 'Z' => $Z, 'AA' => $AA, 'AB' => $AB,'AC'=> $AC, 'NO'=>$i);

        }
    } catch (exception $e) {

        echo '엑셀파일을 읽는도중 오류가 발생하였습니다.';

    }
    $a = 1;
    $b = 0;
    $search_no = 1;
    for ($i = 2; $i <= $maxRow; $i++) {

        if ($read[$i]['B'] == "variation"){
            continue;
        }

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
        } elseif ((strpos($read[$i]['D'], 'カーディガン') !== false || strpos($read[$i]['Z'], 'カーディガン') !== false)&&
        (strpos($read[$i]['D'], 'カーディガン') !== false || strpos($read[$i]['Z'], 'CARDIGAN') !== false)) {
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
        }
        elseif (strpos($read[$i]['D'], 'タンクトップ') !== false || strpos($read[$i]['Z'], 'タンクトップ') !== false) {
            $category = 'トップス';
            $category_id = '101';
            $kocategory = 'タンクトップ';
            $kocategory_id = '2009';
        }
        elseif (strpos($read[$i]['D'], 'スリーブ') !== false || strpos($read[$i]['Z'], 'TOP') !== false) {
            $category = 'トップス';
            $category_id = '101';
            $kocategory = 'その他トップス';
            $kocategory_id = '2211';
        }

        ##바지
        elseif ((strpos($read[$i]['D'], 'ボトムス') !== false || strpos($read[$i]['Z'], 'ボトムス') !== false) and
            (strpos($read[$i]['D'], 'デニム') !== false || strpos($read[$i]['Z'], 'デニム')) !== false or
            (strpos($read[$i]['Z'], 'BOTTOM') !== false || strpos($read[$i]['Z'], 'PANTS')) !== false
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
        }
        ##신발
        elseif ((strpos($read[$i]['D'], 'サンダル') !== false || strpos($read[$i]['Z'], 'SHOES') !== false)
        ) {
            $category = 'シューズ';
            $category_id = '118';
            $kocategory = 'サンダル';
            $kocategory_id = '2090';
        }
        elseif ((strpos($read[$i]['D'], 'ブーツ') !== false || strpos($read[$i]['Z'], 'SHOES') !== false)
        ) {
            $category = 'シューズ';
            $category_id = '118';
            $kocategory = 'ブーツ';
            $kocategory_id = '2092';
        }
        elseif ((strpos($read[$i]['D'], 'スニーカー') !== false || strpos($read[$i]['Z'], 'SHOES') !== false)
        ) {
            $category = 'シューズ';
            $category_id = '118';
            $kocategory = 'スニーカー';
            $kocategory_id = '2093';
        }
        elseif ((strpos($read[$i]['D'], 'シューズ') !== false || strpos($read[$i]['Z'], 'SHOES') !== false)&&
            (strpos($read[$i]['D'], 'サボ') !== false || strpos($read[$i]['Z'], 'SHOES') !== false)&&
            (strpos($read[$i]['D'], 'ミュール') !== false || strpos($read[$i]['Z'], 'SHOES') !== false)
        ) {
            $category = 'シューズ';
            $category_id = '118';
            $kocategory = 'その他シューズ';
            $kocategory_id = '2094';
        }
        elseif ((strpos($read[$i]['D'], 'ビーチサンダル') !== false || strpos($read[$i]['Z'], 'SHOES') !== false)&&
            (strpos($read[$i]['D'], 'ビーサン') !== false || strpos($read[$i]['Z'], 'SHOES') !== false)
        ) {
            $category = 'シューズ';
            $category_id = '118';
            $kocategory = 'ビーチサンダル';
            $kocategory_id = '2352';
        }
        elseif ((strpos($read[$i]['D'], 'スリッポン') !== false || strpos($read[$i]['Z'], 'SHOES') !== false)
        ) {
            $category = 'シューズ';
            $category_id = '118';
            $kocategory = 'スリッポン';
            $kocategory_id = '2374';
        }
        elseif ((strpos($read[$i]['D'], 'ローファー') !== false || strpos($read[$i]['Z'], 'SHOES') !== false)
        ) {
            $category = 'シューズ';
            $category_id = '118';
            $kocategory = 'ローファー';
            $kocategory_id = '2375';
        }
        elseif ((strpos($read[$i]['D'], 'ヒール') !== false || strpos($read[$i]['Z'], 'SHOES') !== false) &&
            (strpos($read[$i]['D'], 'パンプス') !== false || strpos($read[$i]['Z'], 'SHOES') !== false)
        ) {
            $category = 'シューズ';
            $category_id = '118';
            $kocategory = 'パンプス';
            $kocategory_id = '2091';
        }
        ##양말

        elseif ((strpos($read[$i]['D'], 'ソックス') !== false || strpos($read[$i]['Z'], 'ACC') !== false)
        ) {
            $category = 'レッグウェア';
            $category_id = '132';
            $kocategory = 'ソックス/靴下';
            $kocategory_id = '2087';
        }


        ##스커트
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
        }
        elseif ((strpos($read[$i]['D'], 'バック') !== false || strpos($read[$i]['Z'], 'BAG') !== false)
        ) {
            $category = 'バック';
            $category_id = '114';
            $kocategory = 'ハンドバッグ';
            $kocategory_id = '2051';
        }

        ##악세사리
        elseif ((strpos($read[$i]['D'], 'ブレスレット') !== false || strpos($read[$i]['Z'], 'ACC') !== false)&&
            (strpos($read[$i]['D'], 'ミサンガ') !== false || strpos($read[$i]['Z'], 'ACC') !== false)
        ) {
            $category = 'アクセサリー';
            $category_id = '115';
            $kocategory = 'ブレスレット';
            $kocategory_id = '2056';
        }
        elseif ((strpos($read[$i]['D'], 'リング') !== false || strpos($read[$i]['Z'], 'ACC') !== false)
        ) {
            $category = 'アクセサリー';
            $category_id = '115';
            $kocategory = 'リング';
            $kocategory_id = '2058';
        }
        elseif ((strpos($read[$i]['D'], 'チョーカー') !== false || strpos($read[$i]['Z'], 'ACC') !== false)
        ) {
            $category = 'アクセサリー';
            $category_id = '115';
            $kocategory = 'チョーカー';
            $kocategory_id = '2372';
        }
        elseif ((strpos($read[$i]['D'], 'カチューシャ') !== false || strpos($read[$i]['Z'], 'HAIR') !== false)
        ) {
            $category = 'ヘアアクセサリー';
            $category_id = '135';
            $kocategory = 'カチューシャ';
            $kocategory_id = '2279';
        }
        elseif ((strpos($read[$i]['D'], 'ベルト') !== false || strpos($read[$i]['Z'], 'BELT') !== false)
        ) {
            $category = 'ファッション雑貨';
            $category_id = '117';
            $kocategory = 'ベルト';
            $kocategory_id = '2089';
        }

        ##모자
        elseif ((strpos($read[$i]['D'], 'キャップ') !== false || strpos($read[$i]['Z'], 'HAT') !== false)&&
            (strpos($read[$i]['D'], 'ハット') !== false || strpos($read[$i]['Z'], 'HAT') !== false)
        ) {
            $category = '帽子';
            $category_id = '119';
            $kocategory = 'キャップ';
            $kocategory_id = '2096';
        }
        elseif ((strpos($read[$i]['D'], 'ニット') !== false || strpos($read[$i]['Z'], 'HAT') !== false)
        ) {
            $category = '帽子';
            $category_id = '119';
            $kocategory = 'ニットキャップ・ビーニー';
            $kocategory_id = '2097';
        }

        ##속옷
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
        elseif ((strpos($read[$i]['D'], 'シャツ') !== false || strpos($read[$i]['Z'], 'シャツ') !== false)
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
        }
        elseif((strpos($read[$i]['D'], 'ワンピ') !== false || strpos($read[$i]['Z'], 'DRESS') !== false)&&
            (strpos($read[$i]['D'], 'エスニック') !== false || strpos($read[$i]['Z'], 'DRESS') !== false)&&
            (strpos($read[$i]['D'], 'トレーニング') !== false || strpos($read[$i]['Z'], 'DRESS') !== false)&&
            (strpos($read[$i]['D'], 'ワンピース') !== false || strpos($read[$i]['Z'], 'DRESS') !== false)) {
            $category = 'ワンピース';
            $category_id = '111';
            $kocategory = 'ワンピース';
            $kocategory_id = '2035';
        }
        ##수영복
        elseif ((strpos($read[$i]['D'], 'ラッシュガード') !== false || strpos($read[$i]['Z'], 'ラッシュガード') !== false)
            && strpos($read[$i]['Z'], '水着') !== false
        ) {
            $category = '水着・着物・浴衣';
            $category_id = '137';
            $kocategory = 'ラッシュガード';
            $kocategory_id = '2377';
        } elseif(strpos($read[$i]['D'], '水着') !== false || strpos($read[$i]['Z'], '水着') !== false)
        {
            $category = '水着・着物・浴衣';
            $category_id = '137';
            $kocategory = '水着';
            $kocategory_id = '2083';
        }
        elseif(strpos($read[$i]['D'], 'キニ') !== false || strpos($read[$i]['Z'], 'SUMMER') !== false)
        {
            $category = '水着・着物・浴衣';
            $category_id = '137';
            $kocategory = '水着';
            $kocategory_id = '2083';
        }
        ##레깅스
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
        }
        ##자켓
        ##코드
        elseif ((strpos($read[$i]['D'], 'トレンチコート') !== false || strpos($read[$i]['Z'], 'COAT') !== false)&&
            (strpos($read[$i]['D'], 'ロング') !== false || strpos($read[$i]['Z'], 'COAT') !== false)&&
            (strpos($read[$i]['D'], 'チェックコート') !== false || strpos($read[$i]['Z'], 'COAT') !== false))
        {
            $category = 'ジャケット・アウター';
            $category_id = '108';
            $kocategory = 'トレンチコート';
            $kocategory_id = '2228';
        }
        elseif ((strpos($read[$i]['D'], 'テーラード') !== false || strpos($read[$i]['Z'], 'JACKET') !== false))
        {
            $category = 'ジャケット・アウター';
            $category_id = '108';
            $kocategory = 'テーラードジャケット';
            $kocategory_id = '2196';
        }
        elseif ((strpos($read[$i]['D'], 'デニム') !== false || strpos($read[$i]['Z'], 'JACKET') !== false))
        {
            $category = 'ジャケット・アウター';
            $category_id = '108';
            $kocategory = 'デニムジャケット';
            $kocategory_id = '2219';
        }
        elseif ((strpos($read[$i]['D'], 'ダウン') !== false || strpos($read[$i]['Z'], 'OUTER') !== false))
        {
            $category = 'ジャケット・アウター';
            $category_id = '108';
            $kocategory = 'ダウンジャケット/コート';
            $kocategory_id = '2182';
        }
        elseif ((strpos($read[$i]['D'], 'ジャケット') !== false || strpos($read[$i]['Z'], 'JACKET') !== false)&&
            (strpos($read[$i]['D'], 'フェイクファー') !== false || strpos($read[$i]['Z'], 'OUTER') !== false)&&
            (strpos($read[$i]['D'], 'ジャケット') !== false || strpos($read[$i]['Z'], 'ジャケット') !== false))
        {
            $category = 'ジャケット・アウター';
            $category_id = '108';
            $kocategory = 'その他アウター';
            $kocategory_id = '2169';
        }
        elseif ((strpos($read[$i]['D'], 'ライダース') !== false || strpos($read[$i]['Z'], 'JACKET') !== false))
        {
            $category = 'ジャケット・アウター';
            $category_id = '108';
            $kocategory = 'ライダースジャケット';
            $kocategory_id = '2217';
        }
        ##마타니티 웨어
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
        if($site_code=='sweetplus') {
            $C = "59473";
        }
        elseif($site_code=='modernbuy'){
            $C = "60508";
        }

        $D = $read[$i]['C'];
        $E = "女性";
        $F = '2';
        $G = $site_name;
        $H = $site_id[$site_name];
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

        $post_id = $read[$i]['A'];
        $url = get_permalink( $post_id );
        if($post_id!="") $AA = $url;
        else $AA = 'site url';

        $AB = '1';
        $ID = $read[$i]['A'];
        $b += $a;
        $rist[$b] = array('A' => $A, 'B' => $B, 'C' => $C, 'D' => $D, 'E' => $E, 'F' => $F,
            'G' => $G, 'H' => $H, 'I' => $I, 'J' => $J, 'K' => $K, 'L' => $L, 'M' => $M,
            'N' => $N, 'O' => $O, 'P' => $P, 'Q' => $Q, 'R' => $R, 'S' => $S, 'T' => $T,
            'U' => $U, 'V' => $V, 'W' => $W, 'X' => $X, 'Y' => $Y, 'Z' => $Z, 'AA' => $AA, 'AB' => $AB, 'NO' => $b,'ID'=> $ID);
    }

for ($i = 2; $i <= $maxRow; $i++) {
    for ($j = 1; $j <= $b; $j++) {
        if (in_array($read[$i]['AC'], $rist[$j])) {
            if ($read[$i]['AC'] == $rist[$j]['D']) {
                //echo $read[$i]['AC']." : ".$read[$j]['C'];
                //echo $read[$j]['NO'].$read[$j]['C']."<br>";
                //echo $read[$j]['X']." : ".$read[$j]['X'] . "<br>";
                //print_r($read[$i]);
                echo $read[$i]['X'];
                if ($read[$i]['X'] == "")
                    $rist[$j]['U'] = $read[$i]['Y'];
                else$rist[$j]['U'] = $read[$i]['X'];
                $rist[$j]['T'] = $read[$i]['Y'];
                break;
            }
        }
    }
}

    $MaxRow = $b;
    $page=$wearpage;
    if($wearcount>50){
        $limit = 50;
    }
    else $limit=$wearcount;

    if($page==1){
        $for = 1;
    }
    else{$for = ($limit * $page)-$limit;}
    $Remainpage = $MaxRow-($limit*$page)+1;

    if($Remainpage<0){
        $Remainpage=0;
    }

    $a=1;
    $b=2;

    $PP = ($MaxRow)/($limit);
    if($PP>floor($PP)) {
        $PP = (floor($PP) + 1);
    }
    if($limit*$page>$MaxRow){
        $limit=$MaxRow-$for;
    }

    echo "<div align='center'>";
    echo $site_name."의 총 ".$PP."페이지의 상품 '".$MaxRow."' 개 중<br>";
    echo "선택된 상품은 '".$wearpage."' 페이지의 총 '".($limit)."' 개 에서 <br>'1'번째 카테고리 열을 제외한
     '".($limit-1)."'개 입니다.<br>";
    echo "남은 상품은 '".($Remainpage)."' 개 ,".($PP-$page)."페이지 입니다.<br>";
/*
    echo "<table border='1'>";
    echo "<th>No</th><th>商品名</th><th>バーコードNo</th><th>取り扱いECサイトID</th><th>ブランド品番</th><th>商品性別</th>
    <th>商品性別ID</th><th>ブランド名</th><th>ブランドID</th><th>親カテゴリ</th><th>親カテID</th><th>子カテゴリ</th>
    <th>子カテID</th><th>販売国</th><th>販売国ID</th><th>商品説明</th><th>販売タイプ</th><th>販売タイプID</th>
    <th>価格タイプ</th><th>価格タイプID</th><th>定価</th><th>セール価格</th><th>色</th><th>色ID</th><th>サイズ</th>
    <th>サイズID</th><th>CS品番</th><th>ECサイト商品詳細ページURL</th><th>親アイテムフラグ</th><th>ID</th>";
    $p=1;
    for ($i=$for; $i<=$limited; $i++) {


        if($p>4) {
            if($p<$limit-2) {
                if($p==(round($limit/2))){
                    echo "<tr><th colspan='30'> <h2 align='center'>이하 생략</h2> </th></tr>";
                }
                $p += 1;
                $b += $a;
                continue;
            }
        }
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
            echo "<td>" . $rist[$i]['ID'] . "</td>";
            echo "<td>".$i."</td>";
            $product_id = $rist[$i]['ID'];
            echo "</tr>";
        $p+=1;
        $b += $a;
    }

    echo "</table>";
*/
    $rist1 = urlencode(serialize($rist));
    echo "<form method='POST' action='wear_csv_save.php'>";
    echo "<input type='hidden' name='rist' value='$rist1'>
            <input type='hidden' name='wear-list-count' value='$wearcount'>
            <input type='hidden' name='wear-list-page' value='$page'>
            <input type='hidden' name='maxrow' value='$MaxRow'>
            <input type='hidden' name='full_name' value='$site_code'>
            <table><tr><th><input type='submit' value='변환'></th>";
    echo "</form><form method='POST'>";
    echo "<th><input type='submit' value='돌아가기' formaction='../../../../wp-admin/admin.php?page=wc4amuz_japanshop_datacenter_output&tab=product'></th></form></table>";
    echo "</div>";
}
else{
    echo "<script>
		alert('엑셀 파일을 선택해주세요.');
		location.href='javascript:history.back()';
		</script>";
}
?>
