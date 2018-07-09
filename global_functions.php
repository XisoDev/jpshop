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

//  편의점 결제 용


function woocommerce_custom_fee( ) {
    global $woocommerce;
    if ( ( is_admin() && ! defined( 'DOING_AJAX' ) ) || ! is_checkout() )
        return;
    add_action("woocommerce_cart_contents", "get_cart");

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

    $fee_fee = $fee / 1.08;
    $chosen_gateway = $woocommerce->session->chosen_payment_method;


    if ( $chosen_gateway == 'zeus_cs' ) {
        $woocommerce->cart->add_fee( '代引き手数料(税込)', $fee_fee, true, '' );
    }
}

add_action( 'wp_footer', 'cart_update_script', 999 );
add_action( 'woocommerce_cart_calculate_fees','woocommerce_custom_fee' );


function cart_update_script() {
    if (is_checkout()) :
        ?>
        <script>
            jQuery( function( $ ) {

                // woocommerce_params is required to continue, ensure the object exists
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