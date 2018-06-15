<?php

if (!defined('WP_UNINSTALL_PLUGIN')) exit;

if (get_option('channel_io_plugin_key') != false) {
    delete_option('channel_io_plugin_key');
}
?>