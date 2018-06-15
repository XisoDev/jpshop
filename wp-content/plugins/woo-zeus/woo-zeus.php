<?php
/**
 * Plugin Name: WooCommerce For Zeus Payments
 * Plugin URI: http://wordpress.org/plugins/woo-zeus/
 * Description: Woocommerce Zeus payment 
 * Version: 0.9.4
 * Author: Artisan Workshop
 * Author URI: http://wc.artws.info/
 * Requires at least: 4.0
 * Tested up to: 4.3
 *
 * Text Domain: woo-zeus
 * Domain Path: /i18n/
 *
 * @package woo-zeus
 * @category Core
 * @author Artisan Workshop
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Load plugin functions.
 */
add_action( 'plugins_loaded', 'wc4jp_zeus_plugin', 0 );
add_action( 'woocommerce_api_zeus_receive', 'zeus_recieved_func' );

if ( ! class_exists( 'WC_Zeus' ) ) :

class WC_Zeus{

	/**
	 * WooCommerce Constructor.
	 * @access public
	 * @return WooCommerce
	 */
	public function __construct() {
		// Include required files
		$this->includes();
		$this->init();
	}
	/**
	 * Include required core files used in admin and on the frontend.
	 */
	private function includes() {
		// Module
		define('WC_ZEUS_PLUGIN_PATH',plugin_dir_path( __FILE__ ));
		define('WC_ZEUS_CC_API_URL','https://linkpt.cardservice.co.jp/cgi-bin/secure/api.cgi');
		define('WC_ZEUS_SECURE_API_URL','https://linkpt.cardservice.co.jp/cgi-bin/secure.cgi');
		define('WC_ZEUS_CS_URL','https://linkpt.cardservice.co.jp/cgi-bin/cvs.cgi');
		define('WC_ZEUS_CP_URL','https://linkpt.cardservice.co.jp/cgi-bin/carrier/order.cgi');
		define('WC_ZEUS_BT_URL','https://linkpt.cardservice.co.jp/cgi-bin/ebank.cgi');

		// Zeus Payment Gateway
		if(get_option('wc-zeus-cc')) {
			include_once( plugin_dir_path( __FILE__ ).'/includes/gateways/zeus/class-wc-gateway-zeus-cc.php' );	// Credit Card
			include_once( plugin_dir_path( __FILE__ ).'/includes/gateways/zeus/class-wc-addons-gateway-zeus-cc.php' );	// Credit Card Subscriptions
		}
		if(get_option('wc-zeus-cs')) include_once( plugin_dir_path( __FILE__ ).'/includes/gateways/zeus/class-wc-gateway-zeus-cs.php' );	// Convenience store
		if(get_option('wc-zeus-bt')) include_once( plugin_dir_path( __FILE__ ).'/includes/gateways/zeus/class-wc-gateway-zeus-bt.php' );	// Entrusted payment
		if(get_option('wc-zeus-pe')) include_once( plugin_dir_path( __FILE__ ).'/includes/gateways/zeus/class-wc-gateway-zeus-pe.php' );	// Pay-easy
		if(get_option('wc-zeus-cp')) include_once( plugin_dir_path( __FILE__ ).'/includes/gateways/zeus/class-wc-gateway-zeus-cp.php' );	// Carrier payment

		// Admin Setting Screen 
		include_once( plugin_dir_path( __FILE__ ).'/includes/class-wc-admin-screen-zeus.php' );
	}
	/**
	 * Init WooCommerce when WordPress Initialises.
	 */
	public function init() {
		// Set up localisation
		$this->load_plugin_textdomain();
	}

	/*
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'woo-zeus' );
		// Global + Frontend Locale
		load_plugin_textdomain( 'woo-zeus', false, plugin_basename( dirname( __FILE__ ) ) . "/i18n" );
	}
}

endif;

function get_ip_address() {
    $ipaddress = '';
    if ($_SERVER['HTTP_CLIENT_IP'])
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if($_SERVER['HTTP_X_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if($_SERVER['HTTP_X_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if($_SERVER['HTTP_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if($_SERVER['HTTP_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if($_SERVER['REMOTE_ADDR'])
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

function zeus_recieved_func(){
    $log_txt = "";
    foreach($_GET as $key => $val){
        $log_txt .= $key . " : " . $val . "\r\n";
    }
    $log_dir = "/home/jpshop/web/wp-content/zeus_log";
    $log_file = fopen($log_dir."/".$_GET['sendpoint'].date("m-d H:i:s")."log.txt", "a");
    fwrite($log_file, $log_txt."\r\n"."ipaddress:".get_ip_address());
    fclose($log_file);
    $allow_ipaddresses = array(
        "210.164.6.67",
        "202.221.139.50",
        "14.42.243.117",
        "172.31.21.237"
    );
    if(!in_array(get_ip_address(),$allow_ipaddresses)) return;

    global $woocommerce;
    global $wpdb;

    $output = new stdClass();
    $output->error = "0";
    $output->message = "update success";
    if(!$_GET){
        $output->method = "i don't know.";
        $output->error = -1;
        $output->message = "not received datas";
    }else{
        $woocommerce_zeus_cs = get_option('woocommerce_zeus_cs_settings');
        $woocommerce_zeus_bt = get_option('woocommerce_zeus_bt_settings');
        $clientip_array = array(
            'cs' => $woocommerce_zeus_cs['authentication_clientip'],
            'bt' => $woocommerce_zeus_bt['authentication_clientip']
        );

        $get_order_id = substr($_GET['sendpoint'],3);
        $order = new WC_Order( $get_order_id );
        $order_status = $order->status;

        //편의점결제 완료 수신
        if(isset($_GET['clientip']) and $_GET['clientip'] == $clientip_array['cs']){
            $output->method = "Convenience store";
            //기본정보 업데이트
            //save pay infomation

            update_post_meta( $get_order_id, '_zeus_pay_no1', wc_clean( $_GET[ 'pay_no1' ] ) );
            update_post_meta( $get_order_id, '_zeus_cvs_id', wc_clean( $_GET['pay_cvs'] ) );

            if(isset($_GET[ 'pay_no2' ])){
                update_post_meta( $get_order_id, '_zeus_pay_no2', wc_clean( $_GET[ 'pay_no2' ] ) );
            }
            if(isset($_GET[ 'pay_limit' ])){
                update_post_meta( $get_order_id, '_zeus_pay_limit', wc_clean( $_GET[ 'pay_limit' ] ) );
            }

            //Note for Message
            $message = '';
            if(wc_clean( $_GET['pay_cvs']) == 'D001'){
                $message = '('.__( 'Seven Eleven', 'woo-zeus' ).') ,'.__('Payment slip number :','woo-zeus').$_GET['pay_no1'];
            }elseif(wc_clean( $_GET['pay_cvs'])=='D002'){
                $message = '('.__( 'Lawson', 'woo-zeus' ).')'.__('Receipt number :','woo-zeus').$_GET['pay_no1'].', '.__('Authorization number :','woo-zeus').$_GET['pay_no2'];
            }elseif(wc_clean( $_GET['pay_cvs'])=='D030'){
                $message = '('.__( 'Family Mart', 'woo-zeus' ).') ,'.__('Order number :','woo-zeus').$_GET['pay_no1'].', '.__('Corporate code :','woo-zeus').$_GET['pay_no2'];
            }elseif(wc_clean( $_GET['pay_cvs'])=='D040'){
                $message = '('.__( 'Circle K', 'woo-zeus' ).') ,'.__('Payment receipt number :','woo-zeus').$_GET['pay_no1'];
            }elseif(wc_clean( $_GET['pay_cvs'])=='D015'){
                $message = '('.__( 'Seicomart', 'woo-zeus' ).') ,'.__('Payment receipt number :','woo-zeus').$_GET['pay_no1'];
            }elseif(wc_clean( $_GET['pay_cvs'])=='D050'){
                $message = '('.__( 'Mini Stop', 'woo-zeus' ).') ,'.__('Receipt number :','woo-zeus').$_GET['pay_no1'].', '.__('Authorization number :','woo-zeus').$_GET['pay_no2'];
            }elseif(wc_clean( $_GET['pay_cvs'])=='D060'){
                $message = '('.__( 'Daily Yamazaki', 'woo-zeus' ).') ,'.__('Online payment number :','woo-zeus').$_GET['pay_no1'];
            }
//            update_post_meta( $get_order_id, '_zeus_cvs_description', $message );

            //set transaction id for Zeus Order Number
            update_post_meta( $get_order_id, '_transaction_id', wc_clean( $_GET[ 'order_no' ] ) );

            //상태변경
            //Convenience Store Payments
            if($_GET['status'] == '01'){//Finish process
                $output->message = "process finish";
                $order->update_status( 'on-hold', __( 'Awaiting Convenience Store payment', 'woo-zeus' ).$message );
            }elseif($_GET['status'] == '04'){//Finish payment
                $output->message = "payment finish";
                    $order->update_status( 'processing' );
            }elseif($_GET['status'] == '06'){//Cancelled
                $output->message = "cancel";
                    $order->update_status( 'cancelled' );
            }elseif($_GET['status'] == '05'){//Finish sales
                $output->message = "completed";
                    $order->update_status( 'completed' );
            }
            $output->txt_message = $message;
            //은행결제 수신
        }elseif(isset($_GET['clientip']) and $_GET['clientip'] == $clientip_array['bt']){
            $output->method = "Bank transper";

            //Bank transfer
            if($_GET['status'] == '02'){//Finish process
                $output->message = "process finish";
                $order->update_status( 'on-hold' );
            }elseif($_GET['status'] == '03'){//Finish payment
                $output->message = "payment finish";
                $order->update_status( 'processing' );
            }

            //set transaction id for Zeus Order Number
            update_post_meta( $get_order_id, '_transaction_id', wc_clean( $_GET[ 'order_no' ] ) );
        }
    }
    exit(json_encode($output));
}


//If WooCommerce Plugins is not activate notice

function wc4jp_zeus_fallback_notice(){
	?>
    <div class="error">
        <ul>
            <li><?php echo __( 'WooCommerce for Zeus Payment is enabled but not effective. It requires WooCommerce in order to work.', 'woo-zeus' );?></li>
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




function wc4jp_zeus_plugin() {
//    if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    if(is_woocommerce_active()){
        $wc_zeus = new WC_Zeus();
    } else {
        add_action( 'admin_notices', 'wc4jp_zeus_fallback_notice' );
    }
}