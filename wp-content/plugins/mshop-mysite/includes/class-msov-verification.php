<?php

if ( ! defined( 'ABSPATH' ) ){
	exit;
}

if ( ! class_exists( 'MSOV_Verification' ) ) {

	class MSOV_Verification {

		static function init() {
			add_action( 'wp_head', __CLASS__ . '::output_verification_information', 1 );
		}

		static function enabled() {
			return 'yes' == get_option( 'msov_enabled', 'no' );
		}

		static function get_verfication_params() {
			return get_option( 'msov_verification_params', array() );
		}

		static function output_verification_information() {

			if ( self::enabled() ) {
				$params = self::get_verfication_params();

				if ( ! empty( $params ) ) {
					foreach ( $params as $param ) {
						if ( ! empty( $param['service'] ) && ! empty( $param['content'] ) ) {
							echo '<meta name="' . self::get_meta_key($param['service']) . '" content="' . $param['content'] . '"/>' . "\n";
						}
					}
				}
			}
		}

		static function get_meta_key($service) {
			switch($service) {
				case 'naver':
					return 'naver-site-verification';
					break;
				case 'google':
					return 'google-site-verification';
					break;
				default:
					return $service;
			}
		}
	}

	MSOV_Verification::init();

}