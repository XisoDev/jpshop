<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://shopitpress.com
 * @since      1.0.0
 *
 * @package    SIP_Reviews_Shortcode
 * @subpackage SIP_Reviews_Shortcode/admin
 */

define( 'SIP_RSWC_UTM_CAMPAIGN', 'sip-reviews-shortcode' );
define( 'SIP_RSWC_ADMIN_VERSION' , '1.0.3' );

if ( ! defined( 'SIP_SPWC_PLUGIN' ) )
  define( 'SIP_SPWC_PLUGIN',  'SIP Social Proof for WooCommerce' );

if ( ! defined( 'SIP_FEBWC_PLUGIN' ) )
  define( 'SIP_FEBWC_PLUGIN', 'SIP Front End Bundler for WooCommerce' );

if ( ! defined( 'SIP_RSWC_PLUGIN' ) )
  define( 'SIP_RSWC_PLUGIN',  'SIP Reviews Shortcode for WooCommerce' );

if ( ! defined( 'SIP_AENWC_PLUGIN' ) )
  define( 'SIP_AENWC_PLUGIN',  'Sip Advanced Email Notification For Woocommerce' );

if ( ! defined( 'SIP_CCWC_PLUGIN' ) )
  define( 'SIP_CCWC_PLUGIN',  'SIP Cookie Check for WooCommerce' );

if ( ! defined( 'SIP_WPGUMBY_THEME' ) )
  define( 'SIP_WPGUMBY_THEME','WPGumby' );

if ( ! defined( 'SIP_SPWC_PLUGIN_URL' ) )
  define( 'SIP_SPWC_PLUGIN_URL',  'https://shopitpress.com/plugins/sip-social-proof-woocommerce/' );

if ( ! defined( 'SIP_FEBWC_PLUGIN_URL' ) )
  define( 'SIP_FEBWC_PLUGIN_URL', 'https://shopitpress.com/plugins/sip-front-end-bundler-woocommerce/' );

if ( ! defined( 'SIP_RSWC_PLUGIN_URL' ) )
  define( 'SIP_RSWC_PLUGIN_URL',  'https://shopitpress.com/plugins/sip-reviews-shortcode-woocommerce/' );

if ( ! defined( 'SIP_AENWC_PLUGIN_URL' ) )
  define( 'SIP_AENWC_PLUGIN_URL',  'https://shopitpress.com/plugins/sip-advanced-email-notifications-for-woocommerce/' );

if ( ! defined( 'SIP_WPGUMBY_THEME_URL' ) )
  define( 'SIP_WPGUMBY_THEME_URL','https://shopitpress.com/themes/wpgumby/' );

if ( ! defined( 'SIP_CCWC_PLUGIN_URL' ) )
  define( 'SIP_CCWC_PLUGIN_URL',  'https://shopitpress.com/plugins/sip-cookie-check-woocommerce/' );

$get_optio_version = get_option( 'sip_version_value' );
if( $get_optio_version == "" ) {
	add_option( 'sip_version_value', SIP_RSWC_ADMIN_VERSION );
}
if ( version_compare( SIP_RSWC_ADMIN_VERSION , $get_optio_version , ">=" ) ) {
	update_option( 'sip_version_value', SIP_RSWC_ADMIN_VERSION );
}

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    SIP_Reviews_Shortcode
 * @subpackage SIP_Reviews_Shortcode/admin
 * @author     ShopitPress <hello@shopitpress.com>
 */
class SIP_Reviews_Shortcode_WC_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
    
    // Build the custom admin page for managing addons, themes and licenses.
    add_action( 'admin_menu', array( $this, 'sip_rswc_admin_menu' ) );
    add_action( 'admin_init',    array( $this, 'sip_rswc_settings_init' ) );
    add_action( 'admin_footer',  array( $this, 'sip_rswc_media_button_popup' ) );
    add_action( 'admin_footer',  array( $this, 'sip_rswc_add_shortcode_to_editor' ) );
    add_action( 'media_buttons_context', array( $this, 'sip_rswc_tinymce_media_button' ) );
    add_action( 'admin_menu', array( $this, 'sip_rswc_config_menu' ), 20 );
    add_action( 'admin_menu', array( $this, 'sip_spwc_sip_extras_admin_menu' ), 2000 );
    add_action( 'admin_enqueue_scripts', array( $this, 'sip_rswc_add_styles_scripts' ) );
    add_filter( 'plugin_action_links_' . SIP_RSWC_BASENAME, array( $this, 'sip_rswc_action_links' ) );
    add_action( 'admin_enqueue_scripts', array( $this, 'sip_rswc_admin_enqueue') );

	}

  /**
   * add the button to the tinymce editor
   *
   * @since     1.0.0
   */
  public function sip_rswc_tinymce_media_button( $context ) {
    return $context .= __("<a href=\"#TB_inline?width=180&inlineId=shortcode_popup&width=540&height=153\" class=\"button thickbox\" id=\"shortcode_popup_button\" title=\"Product Reviews\">Product Reviews</a>");
  }


  /**
   * javascript code needed to make shortcode appear in TinyMCE edtor
   *
   * @since     1.0.0
   */
  public function sip_rswc_add_shortcode_to_editor() { ?>
    <script>
      jQuery('#id_of_button_clicked ').on('click',function(){
        var shortcode_id      = jQuery('select[name="woocommerce_review_id"]').val();
        var shortcode_title     = jQuery('#woocommerce_review_title').val();
        var shortcode_comments  = jQuery('#woocommerce_review_comments').val();

        var shortcode = '[woocommerce_reviews id="'+shortcode_id+'"  product_title="'+shortcode_title+'"  no_of_reviews="'+shortcode_comments+'"]';
        if( !tinyMCE.activeEditor || tinyMCE.activeEditor.isHidden()) {
          jQuery('textarea#content').val(shortcode);
        } else {
          tinyMCE.execCommand('mceInsertContent', false, shortcode);
        }
        //close the thickbox after adding shortcode to editor
        self.parent.tb_remove();
      });
    </script>
    <?php
  }

  /**
   * Generate inline content for the popup window when the "shortcode" button is clicked
   *
   * @since     1.0.0
   */
    public function sip_rswc_media_button_popup() { 

    $screen = get_current_screen();
    if ( $screen->parent_base == 'edit' ) { ?>
      <div id="shortcode_popup" style="display:none;">
        <div class="wrap">
          <div>
            <h2><?php _e('Insert Product Reviews','');?></h2>
            <div class="shortcode_add">
              <table>
                <tr>
                  <th><label for="woocommerce_review_id"><?php _e('Product','sip-reviews-shortcode');?> :</label></th>
                  <td>
                  <?php
                    $args = array(
                      'post_type' => 'product',
                      'posts_per_page' => -1
                    );
                    $res = '';
                    foreach( get_posts($args) as $val ) {
                      $post_title = str_replace( "'", "", $val->post_title );
                      $res .= '<option value="'.$val->ID.'">'.$post_title.'</options>';
                    }
                    echo '<select id="woocommerce_review_id" name="woocommerce_review_id" class="sip-value">'.$res.'</select>';
                  ?>
                  <br />
                  </td>
                </tr>
                <tr>
                  <th><label for="woocommerce_review_title"><?php _e('Custom Product Title','sip-reviews-shortcode');?> :</label></th>
                  <td><input type="text" id="woocommerce_review_title"><br /></td>
                </tr>
                <tr>
                  <th><label for="woocommerce_review_comments"><?php _e('No. of Reviews','sip-reviews-shortcode');?> :</label></th>
                  <td>
                    <input type="number" id="woocommerce_review_comments" min="1" value="5">
                    <button class="button-primary" id="id_of_button_clicked"><?php _e('Insert Reviews','sip-reviews-shortcode');?></button>
                  </td>
                </tr>
              </table>
            </div>
          </div>
        </div>
      </div>
    <?php } ?>
  <?php
  }

  /**
   * Register settings, add a settings section, and add our color fields.
   *
   * @since     1.0.0
   */
  public function sip_rswc_settings_init(){

    register_setting(
      'wp_color_picker_options',
      'color_options',
      'validate_options'
    );
  }

  /**
   * Add the script file.
   *
   * @since     1.0.0
   */
  public function sip_rswc_add_styles_scripts( ) {
    //Access the global $wp_version variable to see which version of WordPress is installed.
    global $wp_version;

    //If the WordPress version is greater than or equal to 3.5, then load the new WordPress color picker.
    if ( 3.5 <= $wp_version ){
    //Both the necessary css and javascript have been registered already by WordPress, so all we have to do is load them with their handle.
      wp_enqueue_style( 'wp-color-picker' );
      wp_enqueue_script( 'wp-color-picker' );
    }
    //If the WordPress version is less than 3.5 load the older farbtasic color picker.
    else {
    //As with wp-color-picker the necessary css and javascript have been registered already by WordPress, so all we have to do is load them with their handle.
      wp_enqueue_style( 'farbtastic' );
      wp_enqueue_script( 'farbtastic' );
    }

    //Load our custom Javascript file
    wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'assets/js/settings.js', array( 'jquery' ), $this->version, false );

  }

  /**
   * Plugin admin enqueue.
   *
   * @since 1.0.0
   */
  public function sip_rswc_admin_enqueue( ) {

    wp_enqueue_script( 'sip_rswc_admin_js_script', plugin_dir_url( __FILE__ ) . 'assets/js/admin.js' , array('jquery'), '1.0.0', true );
    wp_enqueue_script( 'sip_rswc_sip-iconpicker_script', plugin_dir_url( __FILE__ ) . 'assets/js/sip-iconpicker.js' , array('jquery'), '1.0.0', true );
  }


	/**
   	 * Plugin page menus.
   	 *
   	 * @since 1.0.0
   	 */
  	public function sip_rswc_action_links( $links ) {
    	$plugin_links = array(
      		'<a href="' . admin_url( 'admin.php?page=sip-reviews-shortcode-settings' ) . '">' . __( 'Settings', 'sip-reviews-shortcode' ) . '</a>'
    	);
    	$plugin_links[] = '<a target="_blank" href="https://shopitpress.com/docs/' .SIP_RSWC_PLUGIN_SLUG. '/?utm_source=wordpress.org&utm_medium=SIP-panel&utm_content=v'. SIP_RSWC_VERSION .'&utm_campaign='.SIP_RSWC_UTM_CAMPAIGN.'">' . __( 'Docs', 'sip-reviews-shortcode' ) . '</a>';

    	return array_merge( $links, $plugin_links );
  	}

	/**
   	 * Registers the admin menu for managing the ShopitPress options.
   	 *
   	 * @since 1.0.0
   	 */
	public function sip_rswc_admin_menu() {
	    $icon_svg = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz48IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4iICJodHRwOi8vd3d3LnczLm9yZy9HcmFwaGljcy9TVkcvMS4xL0RURC9zdmcxMS5kdGQiPjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiIHdpZHRoPSI0MHB4IiBoZWlnaHQ9IjMycHgiIHZpZXdCb3g9IjAgNTAgNzI1IDQ3MCIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAwIDAgNzI1IDQ3MCIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+PGc+PHBhdGggZmlsbD0iI0ZGRkZGRiIgZD0iTTY0MC4zMjEsNDguNTk4YzI4LjU0LDAsNDMuNzI5LDI5Ljc5MiwzMi4xNzIsNTUuMTU4bC03Ni40MTYsMTY2Ljk1NGMtMTIuMDMyLTMyLjM0Ni01MC41NjUtNTUuNzU3LTg3LjktNjkuMTczYy00OC44NjItMTcuNjAyLTEyNy44NDMtMjEuODE5LTE5MC4wOTQtMzAuMzc5Yy0zNC4zMjEtNC42NjEtMTEwLjExOC0xMi43NS05Ny43OC01My4xMTVjMTMuMjM5LTQzLjA3NCw5Ni40ODEtNDcuNTkxLDEzMy44OC00Ny41OTFjODYuMTI5LDAsMTYwLjk1NCwxOS43NzEsMTYwLjk1NCw4My44NjZoOTkuNzQxVjQ4LjU5OEg2NDAuMzIxeiBNNTQzLjc5NiwxMDUuNTk0Yy03LjEwNS0yNy40NTgtMzIuMjc3LTQ4LjcxNy01OS4xNjktNTYuOTk3aDgyLjc3NkM1NjYuMjgxLDY2LjYxMyw1NTUuNDQ4LDk0LjE4MSw1NDMuNzk2LDEwNS41OTRMNTQzLjc5NiwxMDUuNTk0eiBNNTUwLjY0MSwzNzAuMTIzbC0xMy42MTEsMjkuNzIzYy02LjAzOCwxMy4yNzktMTkuMzI3LDIxLjYzNS0zMy45MjcsMjEuNjM1SDIyMS45NjljLTE0LjY2NiwwLTI3Ljk1NS04LjM1NS0zNC4wMDMtMjEuNjM1bC0xNS44NDQtMzQuNzIzYzEwLjkxMiwxNC43NDgsMjkuMzMxLDIzLjA4LDQ5LjA5OCwyOC4yODFDMzEzLjE1LDQxNy43MzIsNDY4LjUzNSw0MjEuNDgsNTUwLjY0MSwzNzAuMTIzTDU1MC42NDEsMzcwLjEyM3ogTTE2My43NjEsMzQ2Ljk5bC01OC4xNi0xMjcuMjQzYzE0LjY0MSwxNS42NTUsMzcuNjAxLDI3LjM2LDY2LjcyNCwzNi4yOTdjODUuNDA5LDI2LjI0MiwyMTMuODI1LDIyLjIyOSwyOTYuMjU0LDM1LjExN2M0MS45NDksNi41NjEsNDMuODU3LDQ3LjA4OCwxMy4yODksNjEuOTQ3Yy01Mi4zMzQsMjUuNTA2LTEzNS4yNDUsMjUuMzU5LTE5NC45NTcsMTEuNjk1QzIzNy4yMTksMjg1LjI1LDE1NS44MTksMzA0LjQ5LDE2My43NjEsMzQ2Ljk5TDE2My43NjEsMzQ2Ljk5eiBNODUuODY4LDE3Ni42OTJsLTMzLjM0Ni03Mi45MzdDNDAuOTQ5LDc4LjM5LDU2LjEzMSw0OC41OTgsODQuNjY5LDQ4LjU5OGgxMzYuOTY2QzE1OS43NTEsNjYuMTU0LDc3LjEwNSwxMTAuNjcsODUuODY4LDE3Ni42OTJMODUuODY4LDE3Ni42OTJ6Ii8+PHBhdGggZmlsbD0iI0ZGRkZGRiIgZD0iTTM2Mi41MywwLjA4NmgyNzcuNzkyYzYzLjk2NiwwLDEwMi4xODUsNjYuNzk1LDc2LjEzNSwxMjMuNzI2TDU4MS4wMzEsNDE5Ljk4NEM1NjcuMTQ3LDQ1MC4yODEsNTM2LjQzNSw0NzAsNTAzLjEwMyw0NzBIMzYyLjUzSDIyMS44OTJjLTMzLjM0NSwwLTY0LjA0My0xOS43MTktNzcuOTE3LTUwLjAxNkw4LjUzNSwxMjMuODEyQy0xNy40OTMsNjYuODgyLDIwLjY5MywwLjA4Niw4NC42NjksMC4wODZIMzYyLjUzeiBNMzYyLjUzLDIzLjk0Mkg4NC42NjljLTQ2LjIxOCwwLTczLjU2OCw0OC4yNjYtNTQuNDMsOTAuMDExbDEzNS4zNjIsMjk2LjA3OGMxMC4wNzIsMjEuOTYxLDMyLjIyNSwzNi4xMDUsNTYuMjkxLDM2LjEwNUgzNjIuNTNoMTQwLjU3M2MyNC4wNjcsMCw0Ni4yMTktMTQuMTQ1LDU2LjI3Ny0zNi4xMDVsMTM1LjM4Ni0yOTYuMDc4YzE5LjE0LTQxLjc0NS04LjIyNi05MC4wMTEtNTQuNDQ0LTkwLjAxMUgzNjIuNTN6Ii8+PC9nPjwvc3ZnPg==';
	    //add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
	    $this->hook = add_menu_page(
	        __( 'SIP Plugin Panel', 'sip-reviews-shortcode' ),
	        __( 'SIP Plugins', 'sip-reviews-shortcode' ),
	        'manage_options',
	        'sip_plugin_panel',
	        NULL,
	        $icon_svg,
	        62.25
	    );

	    // Load global assets if the hook is successful.
	    if ( $this->hook ) {
	      // Enqueue custom styles and scripts.
	      add_action( 'admin_enqueue_scripts',  array( $this, 'sip_rswc_admin_tab_style' ) );
	    }
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
  	public function sip_rswc_admin_tab_style() {
      wp_enqueue_style( 'sip_rswc_custom_css', esc_url( SIP_RSWC_URL . 'admin/assets/css/custom.css'), false, '1.0.0' );
      wp_enqueue_style( 'sip-iconpicker_css', esc_url( SIP_RSWC_URL . 'admin/assets/css/sip-iconpicker.css'), false, '1.0.0' );
      wp_enqueue_style( 'font-awesome', esc_url( SIP_RSWC_URL . 'public/css/font-awesome.min.css'), false, '4.7.0' );
  	}


	/**
	 * Loads assets for the settings page.
	 *
	 * @since 1.0.0
	 */
	public function sip_rswc_admin_assets() {

	    wp_register_style( 'sip_rswc_layout', esc_url( SIP_RSWC_URL .   '/admin/assets/css/layout.css', false, '1.0.0' ) );
	    wp_enqueue_style( 'sip_rswc_layout' );
	}

  	public function sip_rswc_remove_duplicate_submenu() {
    	/* === Duplicate Items Hack === */
    	remove_submenu_page( 'sip_plugin_panel', 'sip_plugin_panel' );
  	}

  	public function sip_rswc_config_menu() {
    	global $parent;
    	$args = array(
			'create_menu_page' => true,
			'parent_slug'   => '',
			'page_title'    => __( 'Reviews Shortcode', 'sip-reviews-shortcode' ),
			'menu_title'    => __( 'Reviews Shortcode', 'sip-reviews-shortcode' ),
			'capability'    => 'manage_options',
			'parent'        => '',
			'parent_page'   => 'sip_plugin_panel',
			'page'          => 'sip_plugin_panel',
    	);

    	$parent = $args['parent_page'];

		if ( ! empty( $parent ) ) {
		  	add_submenu_page( $parent , 'Reviews Shortcode', 'Reviews Shortcode', 'manage_options', 'sip-reviews-shortcode-settings', array( $this, 'sip_rswc_settings_page' ) );
		} else {
		  	add_menu_page( $args['page_title'], $args['menu_title'], $args['capability'], $args['page'], array( $this, 'sip_rswc_admin_menu_ui' ), NULL , 62.25 );
		}
		/* === Duplicate Items Hack === */
		$this->sip_rswc_remove_duplicate_submenu();
	}

  	/**
   	 * To avoide the duplication of ShopitPress Extras menue and run the latest sip panel
   	 *
   	 * @since 1.0.1
   	 */
  	public function sip_spwc_sip_extras_admin_menu() {
    	global $parent;
    	$get_optio_version = get_option( 'sip_version_value' );

    	if ( version_compare( $get_optio_version , SIP_RSWC_ADMIN_VERSION , "<=" ) ) {

      		if ( ! defined( 'SIP_PANEL_EXTRAS' ) ) {
          		define( 'SIP_PANEL_EXTRAS' , TRUE);
          		add_submenu_page( $parent , 'ShopitPress Extras', '<span style="color:#FF8080 ">ShopitPress Extras</span>', 'manage_options', 'sip-extras', array( $this, 'sip_rswC_admin_menu_ui' ) );
          		add_action( 'admin_enqueue_scripts',  array( $this, 'sip_rswc_admin_assets' ) );
      		}
    	}
  	}

	/**
	 * On deactivation of plugin null the sip_versin_value
	 *
	 * @since 1.0.1
	 */
  	public function sip_rswc_deactivate(){
    	delete_option( 'sip_version_value' );
  	}

	/**
	 * Outputs the main UI for handling and managing addons, themes and licenses.
	 *
	 * @since 1.0.0
	 */
  	public function sip_rswc_admin_menu_ui() { ?>
    	<div class="wrap">
      		<h2><?php _e('Shopitpress extras','sip-reviews-shortcode'); ?></h2>
	      	<h2 class="nav-tab-wrapper">
	        	<a class="nav-tab<?php if ( !isset( $_GET['action'] ) ) echo ' nav-tab-active'; ?>" href="admin.php?page=sip-extras"><?php _e( 'Plugins', 'sip-reviews-shortcode' ); ?></a>
	        	<a class="nav-tab<?php if ( isset( $_GET['action'] ) && 'themes' == $_GET['action'] ) echo ' nav-tab-active'; ?>" href="admin.php?page=sip-extras&amp;action=themes"><?php _e( 'Themes', 'sip-reviews-shortcode' ); ?></a>
	      	</h2>
      		<?php
      			if ( ! isset( $_GET['action'] ) ) {
        			include("partials/ui/plugin.php");
      			} elseif ( 'themes' == $_GET['action'] ) {
        			include("partials/ui/themes.php");
      			}
      		?>
    	</div>
    	<?php
  	} // END menu_ui()

    /**
   	 * After loding this function global page show the admin panel
   	 *
   	 * @since     1.0.0
   	 */
	public function sip_rswc_settings_page() { ?>

  		<div class="sip-panel-wrapper wrap">
    		<h2><?php _e('Sip Reviews Shortcode for Woocommerce','sip-reviews-shortcode'); ?></h2>
    		<div class="sip-container">
      			<h2 class="nav-tab-wrapper">
		        	<a class="nav-tab<?php if ( !isset( $_GET['action'] ) ) echo ' nav-tab-active'; ?>" href="admin.php?page=sip-reviews-shortcode-settings"><?php _e( 'Settings', 'sip-reviews-shortcode' ); ?></a>
		        	<a class="nav-tab<?php if ( isset( $_GET['action'] ) && 'help' == $_GET['action'] ) echo ' nav-tab-active'; ?>" target="_blank" href="https://shopitpress.com/docs/sip-reviews-shortcode-woocommerce/"><?php _e( 'Help', 'sip-reviews-shortcode' ); ?></a>
      			</h2>
      			<?php
        			if ( ! isset( $_GET['action'] ) ) {
          				sip_rswc_settings_page_ui();          
        			} elseif ( 'help' == $_GET['action'] ) {
          				include("partials/ui/help.php");
        			}
      			?>
    		</div><!-- .container -->
  		</div>
  		<?php
  	}
}