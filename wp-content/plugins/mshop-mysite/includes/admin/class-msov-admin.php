<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'MSOV_Admin' ) ) :

    class MSOV_Admin {

        function __construct(){
            add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        }

        function admin_menu(){
            add_menu_page( __( '엠샵 마이사이트', 'mshop-mysite' ), __( '엠샵 마이사이트', 'mshop-mysite' ), 'manage_options', 'mshop_ownership_verification', array( $this, 'mshop_ownership_verification_settings' ), MSOV()->plugin_url() . '/assets/images/mshop-icon.png', '20.876642' );
        }

        function mshop_ownership_verification_settings(){
            include_once 'settings/class-msov-settings.php';
            MSOV_Settings::output();
        }
    }

    return new MSOV_Admin();

endif;
