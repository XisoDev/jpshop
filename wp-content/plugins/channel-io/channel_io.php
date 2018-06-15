<?php
/*
Plugin Name: Channel.io
Version: 0.8
Description: Channel is a conversational CRM solution that helps online businesses to capture potential customers before they leave the websites.
*/
if (!defined('ABSPATH')) {
  exit;
}

define( 'CHANNEL_IO_VERSION', '0.8' );

// Add font to font icon to set menu icon

add_action('wp_enqueue_scripts','channel_io_register_style');

function channel_io_replace_admin_menu_icons_css() {
?>
  <style>
    #adminmenu #toplevel_page_channel_io div.wp-menu-image::before {
      font-family: 'channelicons';
      content: '\0041';
    }
  </style>
<?php
}

add_action('admin_head', 'channel_io_replace_admin_menu_icons_css');

// add menu

add_action('admin_menu', 'channel_io_plugin_create_menu');
add_action('admin_init', 'channel_io_register_plugin_settings');
add_action('wp_enqueue_scripts','channel_io_plugin_init');

function channel_io_plugin_create_menu() {
  add_menu_page('Channel.io Settings', 'Channel.io', 'administrator', 'channel_io', 'channel_io_plugin_settings_page', '', 80);
}

function channel_io_plugin_settings_page() {
?>
<div class="wrap">
<h1>Channel.io Settings</h1>

<form method="post" action="options.php">
  <?php settings_fields('channel-plugin-settings-group'); ?>
  <?php do_settings_sections('channel-plugin-settings-group'); ?>
  <p>How to get plugin key: <a target="_blank" href="https://developers.channel.io/docs/what-is-a-channel-plugin">See here</a></p>
  <table class="form-table">
    <tr valign="top">
      <th scope="row">Plugin key</th>
      <td>
        <input
          type="text"
          name="channel_io_plugin_key"
          placeholder="Enter here"
          style="min-width: 350px;"
          value="<?php echo sanitize_text_field(get_option('channel_io_plugin_key')); ?>" />
      </td>
    </tr>
  </table>
<?php submit_button(); ?>
</form>
</div>
<?php }

function channel_io_register_plugin_settings() {
  register_setting('channel-plugin-settings-group', 'channel_io_plugin_key');
}

function channel_io_register_style() {
  wp_register_style('channel_io_dashicons', plugins_url( '/css/channelicons.css', __FILE__));
  wp_enqueue_style('channel_io_dashicons');
}

function channel_io_plugin_init() {
  wp_register_script('channel-io-plugin-js', plugins_url( '/channel_plugin_script.js', __FILE__ ), array(), false, true);

  if (is_user_logged_in()) {
    $current_user = wp_get_current_user();
    $channel_io_options = array(
      'channel_io_plugin_key' => sanitize_text_field(get_option('channel_io_plugin_key')),
      'login' => true,
      'id' => $current_user->ID,
      'display_name' => $current_user->display_name,
      'user_email' => $current_user->user_email,
    );
  } else {
    $channel_io_options = array(
      'channel_io_plugin_key' => sanitize_text_field(get_option('channel_io_plugin_key')),
      'login' => false,
    );
  }
  wp_localize_script('channel-io-plugin-js', 'channel_io_options', $channel_io_options);
  wp_enqueue_script('channel-io-plugin-js');
}

// Hook activation

add_action('activated_plugin', 'channel_io_activation_redirect');

function channel_io_activation_redirect( $plugin ) {
  if ($plugin == plugin_basename( __FILE__ )) {
    exit(wp_redirect(admin_url('admin.php?page=channel_io')));
  }
}
?>
