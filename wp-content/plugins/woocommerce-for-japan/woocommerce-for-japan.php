<?php
/**
 * Plugin Name: WooCommerce For Japan
 * Plugin URI: https://wordpress.org/plugins/woocommerce-for-japan/
 * Description: Woocommerce toolkit for Japanese use.
 * Version: 1.2.20
 * Author: Artisan Workshop
 * Author URI: https://wc.artws.info/
 * Requires at least: 4.7.0
 * Tested up to: 4.9.5
 * WC requires at least: 2.4.0
 * WC tested up to: 3.3.5
 *
 * Text Domain: woocommerce-for-japan
 * Domain Path: /i18n/
 *
 * @package woocommerce-for-japan
 * @category Core
 * @author Artisan Workshop
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WooCommerce4jp' ) ) :

class WooCommerce4jp{

	/**
	 * WooCommerce for Japan version.
	 *
	 * @var string
	 */
	public $version = '1.2.20';

	/**
	 * WooCommerce for Japan Constructor.
	 * @access public
	 * @return WooCommerce4jp
	 */
	public function __construct() {
		// Include required files
		$this->includes();
		$this->init();
		// change paypal bn for japan
		add_filter( 'woocommerce_paypal_args',array( &$this,  'wc4jp_paypal_bn'));
		// rated appeal
		add_action( 'wp_ajax_wc4jp_rated', array( __CLASS__, 'wc4jp_rated') );
		add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 1 );
		// WooCommerce for Japan version
		define( 'WC4JP_VERSION', $this->version );
		// WC4JP Framework version
		define( 'WC4JP_FRAMEWORK_VERSION', '1.0.3' );
	}
	/**
	 * Include required core files used in admin and on the frontend.
	 */
	private function includes() {
		// Payment Gateway For Bank
		if(get_option('wc4jp-bankjp')) include_once( 'includes/gateways/bank-jp/class-wc-gateway-bank-jp.php' );
		// Payment Gateway For Post Office Bank
		if(get_option('wc4jp-postofficebank')) include_once( 'includes/gateways/postofficebank/class-wc-gateway-postofficebank-jp.php' );
		// Payment Gateway at Real Store
		if(get_option('wc4jp-atstore')) include_once( 'includes/gateways/atstore/class-wc-gateway-atstore-jp.php' );
		// Payment Gateway For COD subscriptions
		if(get_option('wc4jp-cod2')){
			include_once( dirname( __FILE__ ) . '/includes/gateways/cod/class-wc-gateway-cod-4sub.php' );
			include( dirname( __FILE__ ) . '/includes/gateways/cod/class-wc-addons-gateway-cod.php' );
		}
		// Address field
		include_once( 'includes/class-wc-address-field-4jp.php' );
		// Admin Setting Screen
		include_once( 'includes/class-wc-admin-screen-4jp.php' );
		// ADD COD Fee 
		include_once( 'includes/class-wc-cod-fee-4jp.php' );
		// Add Free Shipping display
		if(get_option('wc4jp-free-shipping')) include_once( 'includes/class-wc-free-shipping-4jp.php' );
	}
	/**
	 * Init WooCommerce when WordPress Initialises.
	 */
	public function init() {
		// Set up localisation
		$this->load_plugin_textdomain();
		// Address Fields Class load
		new AddressField4jp();
		// ADD COD Fee  Class load
		new WooCommerce_Cod_Fee();
	}
	/*
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'woocommerce-for-japan' );
		// Global + Frontend Locale
		load_plugin_textdomain( 'woocommerce-for-japan', false, plugin_basename( dirname( __FILE__ ) ) . "/i18n" );
	}
	/**
	 * Init WooCommerce when WordPress Initialises.
	 */
	public function wc4jp_paypal_bn( $fields ) {
		$fields['bn'] = 'ArtisanWorkshop_Cart_WPS_JP';
		return $fields;
	}
	/**
	 * Change the admin footer text on WooCommerce for Japan admin pages.
	 *
	 * @since  1.2
	 * @param  string $footer_text
	 * @return string
	 */
	public function admin_footer_text( $footer_text ) {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}
		$current_screen = get_current_screen();
		$wc4jp_pages       = 'woocommerce_page_wc4jp-options';
		// Check to make sure we're on a WooCommerce admin page
		if ( isset( $current_screen->id ) && $current_screen->id == $wc4jp_pages ) {
			if ( ! get_option( 'wc4jp_admin_footer_text_rated' ) ) {
				$footer_text = sprintf( __( 'If you like <strong>WooCommerce for Japan</strong> please leave us a %s&#9733;&#9733;&#9733;&#9733;&#9733;%s rating. A huge thanks in advance!', 'woocommerce-for-japan' ), '<a href="https://wordpress.org/support/plugin/woocommerce-for-japan/reviews/#postform" target="_blank" class="wc4jp-rating-link" data-rated="' . esc_attr__( 'Thanks :)', 'woocommerce-for-japan' ) . '">', '</a>' );
				wc_enqueue_js( "
					jQuery( 'a.wc4jp-rating-link' ).click( function() {
						jQuery.post( '" . WC()->ajax_url() . "', { action: 'wc4jp_rated' } );
						jQuery( this ).parent().text( jQuery( this ).data( 'rated' ) );
					});
				" );
			}else{
				$footer_text = __( 'Thank you for selling with WooCommerce for Japan.', 'woocommerce-for-japan' );
			}
		}
		return $footer_text;
	}
	/**
	 * Triggered when clicking the rating footer.
	 */
	public static function wc4jp_rated() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			die(-1);
		}

		update_option( 'wc4jp_admin_footer_text_rated', 1 );
		die();
	}

    /**
     * Display template woocommerce page in front end
     *
     * @param obj $template, $template_name, $template_path
	 */
	public static function wc4jp_locate_template( $template, $template_name, $template_path ) {
		global $woocommerce;
		$_template = $template;
		if ( ! $template_path ) $template_path = $woocommerce->template_url;
		$plugin_path  = untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/woocommerce/';
		// Look within passed path within the theme - this is priority
		$template = locate_template(
			array(
				$template_path . $template_name,
				$template_name
			)
		);
		// Modification: Get the template from this plugin, if it exists
		if ( ! $template && file_exists( $plugin_path . $template_name ) )
			$template = $plugin_path . $template_name;
		// Use default template
		if ( ! $template )
			$template = $_template;
		// Return what we found
		return $template;
	}
}

endif;

/**
 * Load plugin functions.
 */
add_action( 'plugins_loaded', 'WooCommerce4jp_plugin');

function wc4jp_fallback_notice() {
	?>
    <div class="error">
        <ul>
            <li><?php echo __( 'WooCommerce for Japanese is enabled but not effective. It requires WooCommerce in order to work.', 'woocommerce-for-japan' );?></li>
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

function WooCommerce4jp_plugin() {
    if ( is_woocommerce_active() ) {
        new WooCommerce4jp();
        $postoffice_setting = get_option('woocommerce_postofficebankjp_settings');
        if(!empty($postoffice_setting)){
	        update_option( 'woocommerce_postofficebank_settings', $postoffice_setting);
	        delete_option( 'woocommerce_postofficebankjp_settings' );
        }
    } else {
        add_action( 'admin_notices', 'wc4jp_fallback_notice' );
    }
}
// Change Template
add_filter( 'woocommerce_locate_template', 'WooCommerce4jp::wc4jp_locate_template', 10, 5 );

//Garbled characters in e-mail
add_filter( 'woocommerce_order_shipping_to_display', 'wc4jp_display_shipping',10,1);
//Change from &nbsp; to space
function wc4jp_display_shipping($shipping) {
	$shipping = str_replace('&nbsp;',' ',$shipping);
	return $shipping;
}
