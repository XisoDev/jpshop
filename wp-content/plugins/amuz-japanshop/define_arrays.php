<?php

    $status_list = array(
        "wc-pending" => "대기",
        "wc-processing" => "처리중",
        "wc-on-hold" => "보류",
        "wc-completed" => "완료",
        "wc-cancelled" => "취소",
        "wc-refunded" => "환불",
        "wc-failed" => "실패",
    );

function get_payment_method($method){
    if($method == ""||$method =="other" || !$method) return "기타";
    $payment_list = array(
        "codpf" => "대인결제",
        "zeus_cc" => "신용카드",
        "zeus_cs" => "편의점",
        "zeus_bt" => "은행결제",
    );
    return $payment_list[$method];
}
$date_list = array(
    "start_date" => "검색 시작일",
    "end_date" => "검색 종료일"
);
$shipping_list = array(
    "SAGAWA_EX" => array("사가와", "https://track.aftership.com/sagawa/%s"),
);

$states_jp = array(
    'JP01' => "北海道",
    'JP02' => "青森県",
    'JP03' => "岩手県",
    'JP04' => "宮城県",
    'JP05' => "秋田県",
    'JP06' => "山形県",
    'JP07' => "福島県",
    'JP08' => "茨城県",
    'JP09' => "栃木県",
    'JP10' => "群馬県",
    'JP11' => "埼玉県",
    'JP12' => "千葉県",
    'JP13' => "東京都",
    'JP14' => "神奈川県",
    'JP15' => "新潟県",
    'JP16' => "富山県",
    'JP17' => "石川県",
    'JP18' => "福井県",
    'JP19' => "山梨県",
    'JP20' => "長野県",
    'JP21' => "岐阜県",
    'JP22' => "静岡県",
    'JP23' => "愛知県",
    'JP24' => "三重県",
    'JP25' => "滋賀県",
    'JP26' => "京都府",
    'JP27' => "大阪府",
    'JP28' => "兵庫県",
    'JP29' => "奈良県",
    'JP30' => "和歌山県",
    'JP31' => "鳥取県",
    'JP32' => "島根県",
    'JP33' => "岡山県",
    'JP34' => "広島県",
    'JP35' => "山口県",
    'JP36' => "徳島県",
    'JP37' => "香川県",
    'JP38' => "愛媛県",
    'JP39' => "高知県",
    'JP40' => "福岡県",
    'JP41' => "佐賀県",
    'JP42' => "長崎県",
    'JP43' => "熊本県",
    'JP44' => "大分県",
    'JP45' => "宮崎県",
    'JP46' => "鹿児島県",
    'JP47' => "沖縄県",
);
?>