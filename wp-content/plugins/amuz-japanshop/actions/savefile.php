<?php


/**
 * In WordPress Administration Screens
 *
 * @since 2.3.2
 */
if ( ! defined( 'WP_ADMIN' ) ) {
    define( 'WP_ADMIN', true );
}

if ( ! defined('WP_NETWORK_ADMIN') )
    define('WP_NETWORK_ADMIN', false);

if ( ! defined('WP_USER_ADMIN') )
    define('WP_USER_ADMIN', false);

if ( ! WP_NETWORK_ADMIN && ! WP_USER_ADMIN ) {
    define('WP_BLOG_ADMIN', true);
}

if ( isset($_GET['import']) && !defined('WP_LOAD_IMPORTERS') )
    define('WP_LOAD_IMPORTERS', true);

require_once('../../../../wp-load.php');
require_once('../define_arrays.php');

nocache_headers();

$order_list = $_POST['cart'];
$saver = array();


if($_POST['action'] == "sagawa"){
    include('./sagawa.php');
}else if($_POST['action'] == "tqoon_orderlist") {
    include('./tqoon_orderlist.php');
}else if($_POST['action'] == "tqoon_packing") {
    include('./tqoon_packing.php');
}else if($_POST['action'] == "tqoon_orderlist"){
    include('./tqoon_orderlist.php');
}
/*
else if($_POST['action'] == "calculate") {
    include('./calculate.php');
}
*/

?>