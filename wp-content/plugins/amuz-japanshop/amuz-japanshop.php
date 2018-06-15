<?php
/*
 Plugin Name: AMUZ JAPANSHOP
 Description: 아뮤즈 일본 수출플랫폼 커스터마이징 플러그인
 Version: 1.0
 Author: XISO
 Author URI: http://www.amuz.co.kr/
 */


add_action( 'plugins_loaded', 'amuz_japanshop_load', 0 );

class Amuz_Japanshop{

    /**
     * Constructor
     */

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'wc_admin_japanshop_menu' ) ,55);
        add_action('admin_init',array($this,'register_options_japanshop'));
//        add_action('admin_enqueue_scripts','enqueue_date_picker');
    }

    public function register_options_japanshop(){
        add_option('wc-amuz-japanshop-wc-pending');
        add_option('wc-amuz-japanshop-wc-processing');
        add_option('wc-amuz-japanshop-wc-on-hold');
        add_option('wc-amuz-japanshop-wc-completed');
        add_option('wc-amuz-japanshop-wc-cancelled');
        add_option('wc-amuz-japanshop-wc-refunded');
        add_option('wc-amuz-japanshop-wc-failed');
    }

    /**
     * Admin Menu
     */
    public function wc_admin_japanshop_menu() {
        $page = add_submenu_page( 'woocommerce', __( '데이터센터', 'amuz-japanshop' ), __( '데이터센터', 'amuz-japanshop' ), 'manage_woocommerce', 'wc4amuz_japanshop_datacenter_output', array( $this, 'wc_japanshop_datacenter_output' ) );
    }
	
	function enqueue_date_picker(){
			wp_enqueue_script(
				'field-date-js', 
				'Field_Date.js', 
				array('jquery', 'jquery-ui-core', 'jquery-ui-datepicker'),
				time(),
				true
			);	

			wp_enqueue_style( 'jquery-ui-datepicker' );
	}
	
    /**
     * Admin Screen output
     */
    public function wc_japanshop_datacenter_output() {
        $tab = ! empty( $_GET['tab'] ) && $_GET['tab'] == 'product' ? 'product' : 'data';
        include( 'views/html-admin-screen.php' );
    }

}


//에러
function amuz_japanshop_load_error(){
    ?>
    <div class="error">
        <ul>
            <li><?php echo __( '아뮤즈 재팬샵 플러그인이 활성화 되기 위해선, 우커머스가 활성화 되어야 합니다', 'amuz-japanshop' );?></li>
        </ul>
    </div>
    <?php
}

/**
 * WC Detection
 */
if ( ! function_exists( 'is_woocommerce_active' ) ) {
    function is_woocommerce_active() {
        if ( ! isset($active_plugins) ) {
            $active_plugins = (array) get_option( 'active_plugins', array() );

            if ( is_multisite() )
                $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
        }
        return in_array( 'woocommerce/woocommerce.php', $active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php',$active_plugins );
    }
}


function amuz_japanshop_load() {
    if(is_woocommerce_active()){
        $japanshop = new Amuz_Japanshop();
    } else {
        add_action( 'admin_notices', 'amuz_japanshop_load_error' );
    }
}