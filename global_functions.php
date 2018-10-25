<?php

function getSiteOrderCode(){
    $blog_id = get_current_blog_id();
    $site_codes = array(
        1 => array("order_code" => "off-", "fullname" => "official", "url" => "japanshop.amuz.co.kr"),
        2 => array("order_code" => "swp-", "fullname" => "sweetplus", "url" => "sweetplus.jp"),
        3 => array("order_code" => "mod-", "fullname" => "modernbuy", "url" => "modernbuy.jp")
    );
    return $site_codes[$blog_id];
}


function get_site_id(){

    $site_id = array(
            "OFFICIAL"=>"00000",
            "SWEETPLUS"=>"29207",
            "MODERNBUY"=>"29987",
    );
    return $site_id;
}
//  편의점 결제 용

function woocommerce_custom_fee() {

    if ( ( is_admin() && ! defined( 'DOING_AJAX' ) ) || ! is_checkout() )
        return;

    global $woocommerce;

    $deli = $woocommerce->cart->shipping_total;
    $total = $woocommerce->cart->cart_contents_total;
    $fee = 0;

    if ($total+$deli < 1000) $fee = 130;
    elseif ($total+$deli < 2000) $fee = 150;
    elseif ($total+$deli < 3000) $fee = 180;
    elseif ($total+$deli < 10000) $fee = 210;
    elseif ($total+$deli < 30000) $fee = 270;
    elseif ($total+$deli < 100000) $fee = 410;
    elseif ($total+$deli < 150000) $fee = 560;
    elseif ($total+$deli < 300000) $fee = 770;
    $fee_fee = round(($fee / 1.08));
    $chosen_gateway = $woocommerce->session->chosen_payment_method;


    if ( $chosen_gateway == 'zeus_cs' ) {
        $woocommerce->cart->add_fee( 'コンビニの手数料', $fee_fee, true, '' );
    }
}

add_action( 'woocommerce_cart_calculate_fees','woocommerce_custom_fee' );


function cart_update_script() {
    if (is_checkout()) :
        ?>
        <script>
            jQuery( function( $ ) {

                if ( typeof woocommerce_params === 'undefined' ) {
                    return false;
                }

                $checkout_form = $( 'form.checkout' );

                $checkout_form.on( 'change', 'input[name="payment_method"]', function() {
                    $checkout_form.trigger( 'update' );
                });

            });
        </script>
        <?php
    endif;
}
add_action( 'wp_footer', 'cart_update_script', 999 );
?>

<?php

function color()
{
    for ($i = 0; $i <= $color_count; $i++) {
        $array[$i] = strtoupper($array[$i]);
        if ($array[$i] == "WHITE") $array[$i] = "#FFFFFF";
        elseif ($array[$i] == "BEIGE") $array[$i] = "#d1c1a1";
        elseif ($array[$i] == "BLACK") $array[$i] = "#242424";
        elseif ($array[$i] == "BLUE") $array[$i] = "#0f4ad0";
        elseif ($array[$i] == "BLUEGREEN") $array[$i] = "#0d97b4";
        elseif ($array[$i] == "BROWN") $array[$i] = "#832b13";
        elseif ($array[$i] == "CAMEL") $array[$i] = "#d27028";
        elseif ($array[$i] == "CHARCOAL") $array[$i] = "#4f566d";
        elseif ($array[$i] == "CHECK") $array[$i] = "#8a8a8a";
        elseif ($array[$i] == "DARKBLUE") $array[$i] = "#021b76";
        elseif ($array[$i] == "DARKGRAY") $array[$i] = "#4b4e5b";
        elseif ($array[$i] == "DARKBEIGE") $array[$i] = "#c6ac93";
        elseif ($array[$i] == "GRAY") $array[$i] = "#a8a8a8";
        elseif ($array[$i] == "GREEN") $array[$i] = "#056e16";
        elseif ($array[$i] == "IVORY") $array[$i] = "#fbfaf7";
        elseif ($array[$i] == "KHAKI") $array[$i] = "#89907a";
        elseif ($array[$i] == "LIME") $array[$i] = "#e5ffcc";
        elseif ($array[$i] == "LACE") $array[$i] = "#ffffff";
        elseif ($array[$i] == "LEATHERBLACK") $array[$i] = "#000000";
        elseif ($array[$i] == "MINT") $array[$i] = "#a3e09e";
        elseif ($array[$i] == "MUSTARD") $array[$i] = "#ffbe0e";
        elseif ($array[$i] == "ORABGE") $array[$i] = "#ff7e15";
        elseif ($array[$i] == "PINK") $array[$i] = "#ff81a5";
        elseif ($array[$i] == "PINKBEIGE") $array[$i] = "#f0dddb";
        elseif ($array[$i] == "PURPLE") $array[$i] = "#eba1f8";
        elseif ($array[$i] == "RED") $array[$i] = "#FF0000";
        elseif ($array[$i] == "WINE") $array[$i] = "#bb0f38";
        elseif ($array[$i] == "YELLOW") $array[$i] = "#ffd200";
        elseif ($array[$i] == "SKY") $array[$i] = "#6eadde";
        elseif ($array[$i] == "SKYBLUE") $array[$i] = "#87CEEB";
        elseif ($array[$i] == "SUEDEBLACK") $array[$i] = "#000000";
        elseif ($array[$i] == "PURPLE") $array[$i] = "#eba1f8";
        elseif ($array[$i] == "PEACH") $array[$i] = "#F98B88";
        elseif ($array[$i] == "NAVY") $array[$i] = "#233263";
        elseif ($array[$i] == "PEACH") $array[$i] = "#F98B88";
        elseif ($array[$i] == "LIGHTBLUE") $array[$i] = "#ADD8E6";
        elseif ($array[$i] == "LIGHTGARY") $array[$i] = "#D3D3D3";
        elseif ($array[$i] == "LIGHTBEIGE") $array[$i] = "#eee6de";
    }
    return $array;
}


## 품절 상태일 때 글자 회색표시
add_filter( 'woocommerce_variation_option_name', 'customizing_variations_terms_name', 10, 1 );

function customizing_variations_terms_name( $term_name ) {

    global $product;
    // Get available product variations
    $product_variations = $product->get_available_variations();

    foreach ( $product_variations as $product_variation ) {
        if( isset( $product_variation['attributes'] ) ) {
            $key = array_search($term_name, $product_variation['attributes']);

            if( $key !== false && ! $product_variation['is_in_stock'] ) {
                return $term_name . ' - Out of Stock';
            }
        }
    }

    return $term_name;
}
## 품절 상태일 때 선택 불가
add_filter( 'woocommerce_variation_is_active', 'grey_out_variations_when_out_of_stock', 10, 2 );

function grey_out_variations_when_out_of_stock( $grey_out, $variation ) {

    if ( ! $variation->is_in_stock() )
        return false;

    return true;
}

?>