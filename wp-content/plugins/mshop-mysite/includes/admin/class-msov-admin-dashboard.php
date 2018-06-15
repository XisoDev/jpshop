<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'MSOV_Admin_Dashboard' ) ) :
    class MSOV_Admin_Dashboard {
        public function __construct() {
            if ( current_user_can( 'manage_options' ) ) {
                add_action( 'wp_dashboard_setup', array( $this, 'init' ), 1 );
            }
        }

        public function init() {
            wp_add_dashboard_widget( 'msov_dashboard_notices', __( '엠샵 마이사이트 공지사항', 'mshop-mysite' ), array(
                $this,
                'notices'
            ) );
        }

        public function notices() {
	        wp_enqueue_style('msov-admin-dashboard', MSOV()->plugin_url() . '/assets/css/admin-dashboard.css', array(), MSOV()->version);
	        wp_enqueue_script('msov-admin-dashboard', MSOV()->plugin_url() . '/assets/js/admin-dashboard.js', array('jquery', 'jquery-blockui'), MSOV()->version);
	        wp_localize_script('msov-admin-dashboard', 'msov_admin_dashboard', array(
		        'url' => 'http://pgall.co.kr/msb-get-posts/?bid=plugin-notices&mbcat=mshop-common,' . MSOV()->slug()
	        ));

	        ?>
            <table class="msov-notices">
            </table>
	        <?php
        }
    }
    return new MSOV_Admin_Dashboard();

endif;