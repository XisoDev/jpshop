<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://shopitpress.com
 * @since      1.0.0
 *
 * @package    SIP_Reviews_Shortcode
 * @subpackage SIP_Reviews_Shortcode/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    SIP_Reviews_Shortcode
 * @subpackage SIP_Reviews_Shortcode/includes
 * @author     Fran <hello@shopitpress.com>
 */
class SIP_Reviews_Shortcode_Deactivator {

	/**
	 * unset the value of sip_version_value
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		delete_option( 'sip_version_value' );
	}

}
