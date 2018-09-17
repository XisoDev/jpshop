<?php
/*
Plugin Name: MSHOP MY SITE – Easy Website Verify Ownership
Plugin URI: 
Description: Site Owner Verification Plugin for Google, Naver Search Engine Site Register.
Version: 1.0.16
Author: CodeMShop
Author URI: www.codemshop.com
License: GPLv2 or later
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MShop_Ownership_Verification' ) ) {

	class MShop_Ownership_Verification {

		protected static $_instance = null;

		protected $slug;
		public $version = '1.0.16';
		public $plugin_url;
		public $plugin_path;
		public function __construct() {
			// Define version constant
			define( 'MSHOP_OWNERSHIP_VERIFICATION_VERSION', $this->version );

			$this->slug = 'mshop-mysite';

			add_action( 'init', array( $this, 'init' ), 0 );
			add_action( 'plugins_loaded', array($this, 'load_plugin_textdomain'), 0);
		}

        public function slug(){
            return $this->slug;
        }

		public function plugin_url() {
			if ( $this->plugin_url ) {
				return $this->plugin_url;
			}

			return $this->plugin_url = untrailingslashit( plugins_url( '/', __FILE__ ) );
		}


		public function plugin_path() {
			if ( empty( $this->plugin_path ) ) {
                $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
			}

			return $this->plugin_path;
		}

		function includes() {
			if ( is_admin() ) {
				$this->admin_includes();
			}

			if ( defined( 'DOING_AJAX' ) ) {
				$this->ajax_includes();
			}
			include_once( 'includes/class-msov-verification.php' );
            include_once('includes/class-msov-conversation.php');
		}

		public function ajax_includes() {
			include_once( 'includes/class-msov-ajax.php' );
		}

		public function admin_includes() {
            include_once('includes/admin/class-msov-admin.php');
            include_once('includes/admin/class-msov-admin-dashboard.php');
		}

		public function init() {
			$this->includes();
		}

        public static function instance() {
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

		public function load_plugin_textdomain() {
			$locale = apply_filters( 'plugin_locale', get_locale(), 'mshop-mysite' );
			load_textdomain( 'mshop-mysite', WP_LANG_DIR . '/mshop-mysite/mshop-mysite-' . $locale . '.mo' );
			load_plugin_textdomain( 'mshop-mysite', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
		}

	}

    function MSOV() {
        return MShop_Ownership_Verification::instance();
    }

    return MSOV();
}