<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class MSOV_Ajax{
    static $slug;

    public static function init() {
        self::$slug = MSOV()->slug();
        self::add_ajax_events();
    }
    public static function add_ajax_events() {

        $ajax_events = array(
        );

        if( is_admin() ){
            $ajax_events = array_merge( $ajax_events, array(
                'update_settings'            => false,
            ) );
        }

        foreach ( $ajax_events as $ajax_event => $nopriv ) {
            add_action( 'wp_ajax_' . self::$slug . '-' . $ajax_event, array( __CLASS__, $ajax_event ) );

            if ( $nopriv ) {
                add_action( 'wp_ajax_nopriv_' . self::$slug . '-' . $ajax_event, array( __CLASS__, $ajax_event ) );
            }
        }
    }

    public static function update_settings(){
        include_once 'admin/settings/class-msov-settings.php';
        MSOV_Settings::update_settings();
    }
}

MSOV_Ajax::init();
