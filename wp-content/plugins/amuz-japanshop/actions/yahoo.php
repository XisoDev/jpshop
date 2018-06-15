<?php

include ("./_common_loader.php");

$img_url = "https://shopping.geocities.jp/sweetplus/images/";

// size - size|24181| xxs|236815| xs|210765| s|210762| m|210763| l|210764| xl|210766| xxl|210767| xxxl|236816| xxxxl|236817|
// color - カラー|10011| black|10676| yellow|10673| red|10677| white|10678| green|10680| brown|10681| orange|10682| pink|10683| gray|10684| silver|10685| gold|10686| purple|10687| blue|10688| beige|10690| navy|58106| multicolor|81577| kaki|236353| clear|236354|
$option_arr = array(
    "S" => "210762",
    "SMALL" => "210762",
    "M" => "210763",
    "MEDIUM" => "210763",
    "L" => "210764",
    "LARGE" => "210764",
    "NORMAL" => "210764",
    "XL" => "210766",
    "XXL" => "210767",
    "3XL" => "236816",
    "75" => "210765",
    "80" => "210762",
    "85" => "210763",
    "90" => "210764",
    "95" => "210766",
    "100" => "210767",
    "32" => "210765",
    "34" => "210762",
    "36" => "210763",
    "38" => "210764",
    "BLACK" => "10676",
    "WHITE" => "10678",
    "GRAY" => "10684",
    "KHAKI" => "236353",
    "CHARCOAL" => "10684",
    "NAVY" => "58106",
    "PINK" => "10683",
    "IVORY" => "10678",
    "MINT" => "10688",
    "BROWN" => "10681",
    "WINE" => "10687",
    "ORANGE" => "10682",
    "BEIGE" => "10690",
    "GREEN" => "10680",
    "BLUE" => "10688",
    "YELLOW" => "10673",
    "OATMEAL" => "10678",
    "SKYBLUE" => "10688",
    "DIPGRAY" => "10684",
    "PURPLE" => "10687",
    "POWDERPINK" => "10683",
    "LIGHTPINK" => "10683",
    "BLACK/BLACK" => "10676",
    "CHARCOAL/BLACK" => "10676",
    "BLACK/CHARCOAL" => "10676",
    "LIME" => "10680",
    "RED" => "10677",
    "BRIGHTGRAY" => "10684",
    "BLOUSE" => "10678",
    "HALFGRAY" => "10684",
    "DARKGRAY" => "10684",
    "BLUISHGREEN" => "10688",
    "VIOLET" => "10687",
    "MUSTARD" => "10673",
    "COCOA" => "10681",
    "DEEPBLUE" => "58106",
    "SCARLET" => "10677",
    "LIGHTBLUE" => "10688",
    "LIGHTGREEN" => "10680",
    "CAMEL" => "10681",
    "APRICOT" => "10677",
    "COBLATBLUE" => "10688",
    "REDBROWN" => "10681",
    "SKIN" => "10690",
    "PEACH" => "10683",
    "ROSE" => "10683",
    "HEART" => "10673",
    "GRAYBIRD" => "10684",
    "INDYPINK" => "10683",
    "PEACOCK" => "10677",
    "PAISLEY" => "10680",
    "GREENBIRD" => "10680",
    "BLUESEA" => "10688",
    "FLOWER" => "10673",
    "MESH" => "10690",
    "STRIPE" => "10690",
    "LASE3SET" => "81577",
    "VIVID3SET" => "81577",
    "CLASSIC3SET" => "81577"
);

$product_arr = array("特集:あったか裏起毛:トップス"=>"41482",
    "特集:あったか裏起毛:ワンピース"=>"44394",
    "特集:あったか裏起毛:ボトムス"=>"44394",
    "特集:あったか裏起毛:ホームウェア"=>"44394",
    "特集:あったか裏起毛:ジャケット・コート"=>"44394",
    "特集:おすすめアイテム"=>"44394",

    "特集:ベストアイテム"=>"44394",

    "特集:新着アイテム:F/W"=>"44394",
    "特集:新着アイテム:S/S"=>"44394",

    "マタニティウェア:ワンピース:ミディアム"=>"41483",
    "マタニティウェア:ワンピース:ロング"=>"41483",
    "マタニティウェア:ワンピース:半袖"=>"41483",
    "マタニティウェア:ワンピース:授乳服"=>"41483",
    "マタニティウェア:ワンピース:ニット"=>"41483",
    "マタニティウェア:ワンピース:ノースリーブ"=>"41483",
    "マタニティウェア:ワンピース:フォーマル"=>"41483",

    "マタニティウェア:トップス:Tシャツ"=>"44394",
    "マタニティウェア:トップス:シャツ・ブラウス"=>"44394",
    "マタニティウェア:トップス:ニット"=>"44394",
    "マタニティウェア:トップス:スウェット・トレーナー・パーカー"=>"44394",
    "マタニティウェア:トップス:タンクトップ・キャミソール"=>"44394",
    "マタニティウェア:トップス:授乳服"=>"44394",

    "マタニティウェア:ボトムス:パンツ"=>"4347",
    "マタニティウェア:ボトムス:デニム"=>"4347",
    "マタニティウェア:ボトムス:スカート"=>"5043",

    "マタニティウェア:アウター:カーディガン"=>"4344",
    "マタニティウェア:アウター:ジャケット・コート"=>"4344",

    "マタニティウェア:ホームウェア:ワンピース"=>"44394",
    "マタニティウェア:ホームウェア:セット"=>"44394",
    "マタニティウェア:ホームウェア:トップス"=>"44394",
    "マタニティウェア:ホームウェア:ボトムス"=>"44394",

    "マタニティレギンス:3分丈・5分丈・7分丈"=>"4338",
    "マタニティレギンス:9分丈・10分丈"=>"4338",
    "マタニティレギンス:スカート・半ズボン"=>"4338",

    "マタニティ水着:水着"=>"5044",
    "マタニティ水着:ラッシュガード"=>"5044",
    "マタニティ水着:セット"=>"5044",

    "産後・授乳服:授乳口付き:トップス"=>"44394",
    "産後・授乳服:授乳口付き:ワンピース"=>"44394",
    "産後・授乳服:授乳口付き:タンクトップ"=>"41481",
    "産後・授乳服:授乳口付き:セット"=>"44394",
    "産後・授乳服:授乳ランジェリー:ブラ"=>"4342",
    "産後・授乳服:授乳ランジェリー:タンクトップ"=>"41481",

    "マタニティランジェリー:ブラ"=>"4342",
    "マタニティランジェリー:ショーツ"=>"13584",
    "マタニティランジェリー:タンクトップ"=>"41481",
    "マタニティランジェリー:セット"=>"4342",
    "マタニティランジェリー:ストッキング"=>"4338",
    "マタニティランジェリー:妊婦帯"=>"4341");


$category_ids = getCategoryIds(":");
$taxonomy = array();
$args = array(
	'status'      => 'publish',
    'limit'     => $_POST['wc-amuz-japanshop-list_count'],
    'paged'     => $_POST['wc-amuz-japanshop-page'],
);
$products = wc_get_products( $args );
$output = array();

foreach($products as $oProduct){
    if($oProduct->get_stock_status() == "outofstock") continue;
    $ori_code = $oProduct->get_sku();

    $save_arr = array();
    $ids = $oProduct->get_category_ids();
    $paths = array();
    $product_code = "";
    foreach($ids as $id){
        $paths[] = $category_ids[$id];
        if($product_arr[$category_ids[$id]])
            $product_code = $product_arr[$category_ids[$id]];
    }
    $save_arr['path'] = join("\n",$paths);
    $save_arr['path'] .= "\nhiddenpage";
    $save_arr['name'] = $oProduct->get_title();
    $save_arr['code'] = str_replace("_","-",$ori_code);
    $save_arr['sub-code'] = "";
    $save_arr['original-price'] = "";
    $save_arr['price'] = round($oProduct->get_price() * 1.08);
    $save_arr['sale-price'] = round($save_arr['price'] * 0.9);
//    $save_arr['member_price'] = round($save_arr['price'] * 0.8);

    $save_arr['options'] = "";// $options
    $attributes = $oProduct->get_attributes();
    if(count($attributes)){
        $value_arr = array();
        foreach($attributes as $attr){
            $option_text = "";
            $option_name = $attr->get_name();
            if(!$taxonomy[$option_name]){
                $taxonomy[$option_name] = getCategory($option_name);
            }

            if(strtolower($option_name) == "pa_color"){
                $option_text .= "カラー|10011|";
            }else if(strtolower($option_name) == "pa_size"){
                $option_text .= "サイズ|24181|";
            }else{
                $option_text .= $option_name . "||";
            }

            foreach($attr->get_options() as $option){
                $v = $taxonomy[$attr->get_name()][$option]->title;
                $v = preg_replace("/\s+/", "", $v);
                if(isset($option_arr[$v])){
                    $option_text .= " " . $v . "|". $option_arr[$v] ."|";
                }else{
                    $option_text .= " " . $v . "||";
                }
            }
            $value_arr[] = $option_text;
        }
        $save_arr["options"] = join("\n\n",$value_arr);
    }
    $save_arr['headline'] = "安全な生地を使用したマタニティウェア！";
    $save_arr['caption'] = $oProduct->get_description();
    $save_arr['abstract'] = $oProduct->get_short_description();
    $save_arr['explanation'] = $oProduct->get_short_description();
    $save_arr['additional1'] = "";
    $save_arr['additional2'] = "";
    $save_arr['additional3'] = "";
    //추천상품의 상품코드 입력.
    $save_arr['relevant-links'] = "";
    $save_arr['ship-weight'] = "";
    $save_arr['taxable'] = 1;
    $save_arr['release-date'] = "";
    $save_arr['point-code'] = "";
    $save_arr['meta-desc'] = "最高のマタニティーウェア - " . $category_ids[$ids[0]];

    $save_arr['display'] = 1;
    $save_arr['template'] = "IT03";
    $save_arr['sale-period-start'] = date("Ymd")."0000";
    $save_arr['sale-period-end'] = 	date("Ymd",time() + (60*60*24*13))."2359";
    $save_arr['sale-limit'] = "";
    $save_arr['sp-code'] = "";
    $save_arr['brand-code'] = "";
    $save_arr['product-code'] = $ori_code;
    $save_arr['jan'] = "";
    $save_arr['delivery'] = 1;
    $save_arr['astk-code'] = 0;
    $save_arr['condition'] = 0;
    $save_arr['product-category'] = $product_code;

    $save_arr['spec1'] = "";
    $save_arr['spec2'] = "";
    $save_arr['spec3'] = "";
    $save_arr['spec4'] = "";
    $save_arr['spec5'] = "";
    $save_arr['spec6'] = "";
    $save_arr['spec7'] = "";
    $save_arr['spec8'] = "";
    $save_arr['spec9'] = "";
    $save_arr['spec10'] = "";

    $save_arr['sp-additional'] = $save_arr['caption'];

    $save_arr['sort_priority'] = "";
    $save_arr['original-price-evidence'] = "";

    $save_arr['lead-time-instock'] = 2000;
    $save_arr['lead-time-outstock'] = 3000;
    $save_arr['keep-stock'] = 0;
//    print_r($save_arr);
    foreach($save_arr as $key => $val){
        $save_arr[$key] = mb_convert_encoding($val, 'SJIS-win','UTF-8');
    }
    $output[] = $save_arr;

}
//exit();

header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=update_yahoo_".date("Y-m-d H:i").".csv");
header("Pragma: no-cache");
header("Expires: 0");
$out = fopen("php://output", 'w');
foreach ($output as $key => $data)
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
?>