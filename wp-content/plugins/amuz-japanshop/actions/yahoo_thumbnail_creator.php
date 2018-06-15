<?php

include ("./_common_loader.php");

function imageCreateFromAny($filepath) {
    $type = exif_imagetype($filepath); // [] if you don't have exif you could use getImageSize()
    $allowedTypes = array(
        1,  // [] gif
        2,  // [] jpg
        3,  // [] png
        6   // [] bmp
    );
    if (!in_array($type, $allowedTypes)) {
        return false;
    }
    switch ($type) {
        case 1 :
            $im = imageCreateFromGif($filepath);
            break;
        case 2 :
            $im = imageCreateFromJpeg($filepath);
            break;
        case 3 :
            $im = imageCreateFromPng($filepath);
            break;
        case 6 :
            $im = imageCreateFromBmp($filepath);
            break;
    }
    return $im;
}

function deleteDirectory($dir) {
    if (!file_exists($dir)) {
        return true;
    }

    if (!is_dir($dir)) {
        return unlink($dir);
    }

    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }

        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }

    }

    return rmdir($dir);
}


$blog_id = get_current_blog_id();
$dir = "../../../download_files/" . $blog_id;

//폴더 삭제 후 재생성.
deleteDirectory($dir);
mkdir($dir, 0777, true);
mkdir($dir."/sources", 0777, true);

$page = $_GET['page'] ? $_GET['page'] : 1;
$args = array(
    'status'      => 'publish',
    'limit'     => 9999,
    'paged'     => $page,
);
$products = wc_get_products( $args );
$output = array();
$image_list = array();
$square_size = 600;
foreach($products as $oProduct){
    if($oProduct->get_stock_status() == "outofstock") continue;
    if(!$oProduct->get_image_id()) continue;
    $image = wp_get_attachment_image_src( $oProduct->get_image_id(), 'full' );
    $image_url = parse_url($image[0]);
    $image_url = "../../../../" . $image_url['path'];
    if(!file_exists($image_url)) continue;

    $ori_code = $oProduct->get_sku();
    $code = str_replace("_","-",$ori_code);

    $loadimg = $image_url;
    $saveimg = $dir . "/sources/" . $code;


    //리사이즈 하고
    list($width, $height) = getimagesize($loadimg);

    //가로로길면
    if($width > $height){
        //기준사이즈보다 큰경우에
        if($width > $square_size){
            $percent = $square_size / $width;
        }else{
            $percent = 1;
        }
        $percent = $square_size / $width;
    }else{
        //기준사이즈보다 큰경우에
        if($height > $square_size){
            $percent = $square_size / $height;
        }else{
            $percent = 1;
        }
        //무조건 늘림
        $percent = $square_size / $height;
    }
    $newwidth = $width * $percent;
    $newheight = $height * $percent;

    //중앙으로 오게 정렬
    $x = ($square_size - $newwidth) / 2;
    $y = ($square_size - $newheight) / 2;
    // Load
    $thumb = imagecreatetruecolor($square_size, $square_size);
    $source = imageCreateFromAny($loadimg);
    //작을땐 여백이생기니까 화이트로 채움.
    $white = imagecolorallocate($thumb, 255, 255, 255);
    imagefill($thumb, 0, 0, $white);
    // Resize
    if($percent != 1){
        imagecopyresized($thumb, $source, $x, $y, 0, 0, $newwidth, $newheight, $width, $height);
    }else{
        // no resize
        imagecopy($thumb, $source, $x, $y, 0, 0, $newwidth, $newheight);
    }
    // save
    imagejpeg($thumb, $saveimg, 100);
    //remove
    imagedestroy($thumb);
    imagedestroy($source);


    $image_list[] = $code;
}


//제한용량 (MB단위)
$blog_id = get_current_blog_id();
$dir = "../../../download_files/" . $blog_id;

$limit = $_POST['wc-amuz-japanshop-limit_storage'] - 2;
$results = array();
$handler = opendir($dir . "/sources/");
while ($file = readdir($handler)) {
    if ($file != '.' && $file != '..' && is_dir($file) != '1') {
        $results[] = array($file, filesize($dir . "/sources/" . $file));
    }
}

closedir($handler);
//바이트로 변환
$limit = $limit * 1024 * 1024;


$oZip = array();
$file_list = array();
$size = 0;
foreach($results as $file){
    if($size > $limit){
        //사이즈초기화
        $size = 0;
        //현재까지의 목록을 구분지어서 담아줌.
        $oZip[] = $file_list;
        $file_list = array();
    }
    //사이즈누적
    $size += $file[1];
    //단위묶어줌.
    $file_list[] = $dir . "/sources/" . $file[0];
}
if(count($file_list)){
    $oZip[] = $file_list;
}

//압축하자
include('../Classes/pclzip.lib.php');
$i = 0;
foreach($oZip as $key => $val){
//    $zipfile = new PclZip('image_zips/img' . date("YmdHis",time() + (60* (10 + $i++))) .'.zip');
    $zipfile = new PclZip($dir . "/" . $key .'.zip');
//    $create = $zipfile->create($val,PCLZIP_OPT_REMOVE_ALL_PATH);
    $create = $zipfile->create($val,PCLZIP_OPT_REMOVE_ALL_PATH);

    if ($create == 0) {
        die("ERROR : '".$zipfile->errorInfo(true)."'");
    }else{
        echo "<a href='/wp-content/download_files/". $blog_id ."/" . $key . ".zip'>" . $key . "번파일</a>";
    }
}

?>

<style>
    a {
        display:block;
        width:50%;
        float:left;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        padding:5px;
        background:#EEE;
        border:1px solid #aaa;
    }
</style>
