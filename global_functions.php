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

    if ( ( is_admin() && ! defined( 'DOING_AJAX' ) ) || ! is_checkout() )
        return;
    add_action("woocommerce_cart_contents", "get_cart");
    $total = WC()->cart->cart_contents_total;
    $fee = 0;
    if ($total >= 1 && $total <= 999) {
        {
            $fee = "130";
        }
    } elseif ($total >= 1000 && $total <= 1999) {
        {
            $fee = "150";
        }
    } elseif ($total >= 2000 && $total <= 2999) {
        {
            $fee = "180";
        }
    } elseif ($total >= 3000 && $total <= 9999) {
        {
            $fee = "210";
        }
    } elseif ($total >= 10000 && $total <= 29999) {
        {
            $fee = "270";
        }
    } elseif ($total >= 30000 && $total <= 99999) {
        {
            $fee = "410";
        }
    } elseif ($total >= 100000 && $total <= 149999) {
        {
            $fee = "560";
        }
    } elseif ($total >= 200000 && $total <= 299999) {
        {
            $fee = "770";
        }
    }
    $percent = 8;

    // Calculation
    $fee_fee = $fee * 0.08;
    $surcharge = ($total * $percent / 100)+$fee_fee;


    WC()->cart->add_fee( '税', ceil($surcharge), false ,'');

    $chosen_gateway = WC()->session->chosen_payment_method;

    if ( $chosen_gateway == 'zeus_cs' ) { //test with paypal method
        WC()->cart->add_fee( '代引き手数料(税込)', $fee, false, '' );
    }
}
add_action( 'woocommerce_cart_calculate_fees','woocommerce_custom_fee' );

add_action( 'wp_footer', 'cart_update_script', 999 );



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