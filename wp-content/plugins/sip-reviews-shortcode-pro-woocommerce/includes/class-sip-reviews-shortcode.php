<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://shopitpress.com
 * @since      1.0.0
 *
 * @package    SIP_Reviews_Shortcode
 * @subpackage SIP_Reviews_Shortcode/includes
 */

/**
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    SIP_Reviews_Shortcode
 * @subpackage SIP_Reviews_Shortcode/includes
 * @author     ShopitPress <hello@shopitpress.com>
 */
class SIP_Reviews_Shortcode_Pro {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      SIP_Reviews_Shortcode_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'sip-reviews-shortcode';
		$this->version = '1.2.6';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		add_action( 'admin_init', array( $this, 'admin_init' ) );
	}


  	public function admin_init() {
    	register_setting( 'sip-rswc-affiliate-settings-group',  'sip-rswc-affiliate-check-box'  );
		register_setting( 'sip-rswc-affiliate-settings-group',  'sip-rswc-affiliate-radio' );
		register_setting( 'sip-rswc-affiliate-settings-group',  'sip-rswc-affiliate-affiliate-username' );
		register_setting( 'sip-rswc-settings-group',			'sip-rswc-setting-link-to-comments' );
		register_setting( 'sip-rswc-settings-group',			'sip-rswc-setting-show-product-name' );
		register_setting( 'sip-rswc-settings-group',			'sip-rswc-setting-limit-review-characters' );
		register_setting( 'sip-rswc-settings-group',			'sip-rswc-settings-radio' );
		register_setting( 'sip-rswc-settings-group',			'sip-rswc-notify-admin-reviews-submitted' );
		register_setting( 'sip-rswc-settings-group',			'sip-rswc-setting-customer-review' );
		register_setting( 'sip-rswc-settings-group',			'sip-rswc-setting-verified-customer' );
		register_setting( 'sip-rswc-settings-group',			'sip-rswc-setting-rated-icon' );
  	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - SIP_Reviews_Shortcode_Loader. Orchestrates the hooks of the plugin.
	 * - SIP_Reviews_Shortcode_i18n. Defines internationalization functionality.
	 * - SIP_Reviews_Shortcode_Admin. Defines all hooks for the admin area.
	 * - SIP_Reviews_Shortcode_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sip-reviews-shortcode-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-sip-reviews-shortcode-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-sip-reviews-shortcode-wc-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/class-sip-reviews-shortcode-wc-admin-setting.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-sip-reviews-shortcode-wc-public.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/plugin-reviews-shortcode-widgets.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/plugin-reviews-shortcode-widgets-aggregate.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/plugin-reviews-shortcode-carousel-style-display.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/plugin-reviews-shortcode-display-id.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/plugin-reviews-shortcode-display.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/plugin-form-shortcode-display.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/plugin-reviews-aggregate.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/plugin-reviews-avg.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/more-post-aggregate-rating.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/more-post-style3.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/more-post-rating.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/submit-comment.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/more-post.php';

		$this->loader = new SIP_Reviews_Shortcode_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the SIP_Reviews_Shortcode_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new SIP_Reviews_Shortcode_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new SIP_Reviews_Shortcode_WC_Admin( $this->get_plugin_name(), $this->get_version() );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new SIP_Reviews_Shortcode_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    SIP_Reviews_Shortcode_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}