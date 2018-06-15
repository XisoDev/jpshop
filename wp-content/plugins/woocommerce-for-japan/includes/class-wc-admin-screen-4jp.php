<?php
/**
 * WooCommerce For Japan

 * @author 		ArtisanWorkshop
 * @package 	Admin Screen
 * @version     1.2.18
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WC_4JP_Admin_Screen {
	/**
	 * Error messages.
	 *
	 * @var array
	 */
	public $errors   = array();

	/**
	 * Update messages.
	 *
	 * @var array
	 */
	public $messages = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 80 );
		add_action( 'admin_menu', array( $this, 'wc4jp_admin_menu' ) ,60 );
		add_action( 'admin_init', array( $this, 'wc4jp_setting_init') );
		if(!class_exists('WC4JP_Admin_Setting')){
			require_once( plugin_dir_path( __FILE__ ).'wc4jp-framework/class-wc4jp-setting.php' );
		}
		$this->wc4jp_setting = new WC4JP_Admin_Setting();
		$this->prefix =  'wc4jp-';
	}
	/**
	 * Admin Menu
	 */
	public function wc4jp_admin_menu() {
		$page = add_submenu_page( 'woocommerce', __( 'For Japanese', 'woocommerce-for-japan' ), __( 'For Japanese', 'woocommerce-for-japan' ), 'manage_woocommerce', 'wc4jp-options', array( $this, 'wc4jp_output_display' ) );
	}

	/**
	 * Admin Screen output
	 */
	public function wc4jp_output_display() {
		$tab = ! empty( $_GET['tab'] )  ? $_GET['tab'] : 'setting';
		//enable only user for manage options
		if ( ! current_user_can( 'administrator' ) )
		return;

		include( 'views/html-admin-screen.php' );
	}

	/**
	 * Admin page for Setting
	 */
	public function admin_setting_page() {
		include( 'views/html-admin-setting-screen.php' );
	}

	/**
	 * Admin page for infomation
	 */
	public function admin_info_page() {
		include( 'views/html-admin-info-screen.php' );
	}
	
	function wc4jp_setting_init(){
		
		register_setting( 'wc4jp_options', 'wc4jp_options_name', array( $this, 'validate_options' ) );
		// Address Display Setting
		add_settings_section( 'wc4jp_general', __( 'Address Display Setting', 'woocommerce-for-japan' ), '', 'wc4jp_setting' );
		
		add_settings_field( 'wc4jp_options_yomigana', __( 'Name Yomigana', 'woocommerce-for-japan' ), array( $this, 'wc4jp_options_yomigana' ), 'wc4jp_setting', 'wc4jp_general' );
		add_settings_field( 'wc4jp_options_honorific_suffix', __( 'Honorific suffix(Sama)', 'woocommerce-for-japan' ), array( $this, 'wc4jp_options_honorific_suffix' ), 'wc4jp_setting', 'wc4jp_general' );
		add_settings_field( 'wc4jp_options_company_name', __( 'Company Name', 'woocommerce-for-japan' ), array( $this, 'wc4jp_options_company_name' ), 'wc4jp_setting', 'wc4jp_general' );
		add_settings_field( 'wc4jp_options_free_shipping', __( 'Free Shipping Display', 'woocommerce-for-japan' ), array( $this, 'wc4jp_options_free_shipping' ), 'wc4jp_setting', 'wc4jp_general' );
		add_settings_field( 'wc4jp_options_zip2address', __( 'Automatic zip code entry', 'woocommerce-for-japan' ), array( $this, 'wc4jp_options_zip2address' ), 'wc4jp_setting', 'wc4jp_general' );
		add_settings_field( 'wc4jp_options_yahoo_app_id', __( 'Yahoo APP ID', 'woocommerce-for-japan' ), array( $this, 'wc4jp_options_yahoo_app_id' ), 'wc4jp_setting', 'wc4jp_general' );

		// Delivery date designation
		add_settings_section( 'wc4jp_delivery_date', __( 'Delivery date designation', 'woocommerce-for-japan' ), '', 'wc4jp_shipment' );
		add_settings_field( 'wc4jp_delivery_date_designation', __( 'Preferred delivery date', 'woocommerce-for-japan' ), array( $this, 'wc4jp_delivery_date_designation' ), 'wc4jp_shipment', 'wc4jp_delivery_date' );
		add_settings_field( 'wc4jp_start_date', __( 'Start Date', 'woocommerce-for-japan' ), array( $this, 'wc4jp_start_date' ), 'wc4jp_shipment', 'wc4jp_delivery_date' );
		add_settings_field( 'wc4jp_reception_period', __( 'Delivery Term', 'woocommerce-for-japan' ), array( $this, 'wc4jp_reception_period' ), 'wc4jp_shipment', 'wc4jp_delivery_date' );
		add_settings_field( 'wc4jp_unspecified_date', __( 'Unspecified date description', 'woocommerce-for-japan' ), array( $this, 'wc4jp_unspecified_date' ), 'wc4jp_shipment', 'wc4jp_delivery_date' );
		add_settings_field( 'wc4jp_date_format', __( 'Date format', 'woocommerce-for-japan' ), array( $this, 'wc4jp_date_format' ), 'wc4jp_shipment', 'wc4jp_delivery_date' );
		add_settings_field( 'wc4jp_delivery_deadline', __( 'Delivery deadline', 'woocommerce-for-japan' ), array( $this, 'wc4jp_delivery_deadline' ), 'wc4jp_shipment', 'wc4jp_delivery_date' );
		add_settings_field( 'wc4jp_no_delivery_weekday', __( 'No delivery weekday', 'woocommerce-for-japan' ), array( $this, 'wc4jp_no_delivery_weekday' ), 'wc4jp_shipment', 'wc4jp_delivery_date' );
		add_settings_field( 'wc4jp_holiday_term', __( 'Holiday term', 'woocommerce-for-japan' ), array( $this, 'wc4jp_holiday_term' ), 'wc4jp_shipment', 'wc4jp_delivery_date' );

		// Delivery time designation
		add_settings_section( 'wc4jp_delivery_time', __( 'Delivery Time designation', 'woocommerce-for-japan' ), '', 'wc4jp_shipment' );
		add_settings_field( 'wc4jp_delivery_time_zone', __( 'Delivery time zone', 'woocommerce-for-japan' ), array( $this, 'wc4jp_delivery_time_zone' ), 'wc4jp_shipment', 'wc4jp_delivery_time' );
		add_settings_field( 'wc4jp_unspecified_time', __( 'Unspecified Time description', 'woocommerce-for-japan' ), array( $this, 'wc4jp_unspecified_time' ), 'wc4jp_shipment', 'wc4jp_delivery_time' );
		add_settings_field( 'wc4jp_delivery_time_zone_mgn', __( 'Delivery time zone management', 'woocommerce-for-japan' ), array( $this, 'wc4jp_delivery_time_zone_mgn' ), 'wc4jp_shipment', 'wc4jp_delivery_time' );		

		// Delivery time designation
//		add_settings_section( 'wc4jp_delivery_tracking', __( 'Delivery tracking setting', 'woocommerce-for-japan' ), '', 'wc4jp_shipment' );
//		add_settings_field( 'wc4jp_delivery_time_zone', __( 'Delivery tracking', 'woocommerce-for-japan' ), array( $this, 'wc4jp_delivery_tracking' ), 'wc4jp_shipment', 'wc4jp_delivery_tracking' );

		// Payment Method
		add_settings_section( 'wc4jp_payments', __( 'Payment Method', 'woocommerce-for-japan' ), '', 'wc4jp_payment' );
		add_settings_field( 'wc4jp_options_bankjp', __( 'BANK PAYMENT IN JAPAN', 'woocommerce-for-japan' ), array( $this, 'wc4jp_options_bankjp' ), 'wc4jp_payment', 'wc4jp_payments' );
		add_settings_field( 'wc4jp_options_postofficebank', __( 'Postal transfer', 'woocommerce-for-japan' ), array( $this, 'wc4jp_options_postofficebank' ), 'wc4jp_payment', 'wc4jp_payments' );
		add_settings_field( 'wc4jp_options_atstore', __( 'Pay at store', 'woocommerce-for-japan' ), array( $this, 'wc4jp_options_atstore' ), 'wc4jp_payment', 'wc4jp_payments' );
		add_settings_field( 'wc4jp_options_cod2', __( 'COD for Subscriptions', 'woocommerce-for-japan' ), array( $this, 'wc4jp_options_cod2' ), 'wc4jp_payment', 'wc4jp_payments' );

		register_setting( 'wc4jp_informations', 'wc4jp_informations_name');

		// Plugins Informations
		add_settings_section( 'wc4jp_plugins', __( 'Plugins Information', 'woocommerce-for-japan' ), '', 'wc4jp_informations' );
		add_settings_field( 'wc4jp_informations_plugins', __( 'Featured Plugins', 'woocommerce-for-japan' ), array( $this, 'wc4jp_informations_plugins' ), 'wc4jp_informations', 'wc4jp_plugins' );

		// Professional services Informations
		add_settings_section( 'wc4jp_services', __( 'Professional services Information', 'woocommerce-for-japan' ), '', 'wc4jp_informations' );
		add_settings_field( 'wc4jp_informations_services', __( 'Featured Services', 'woocommerce-for-japan' ), array( $this, 'wc4jp_informations_services' ), 'wc4jp_informations', 'wc4jp_services' );

		if( isset( $_POST['_wpnonce']) and isset($_GET['page']) and $_GET['page'] == 'wc4jp-options' ){
			if(isset($_GET['tab']) and $_GET['tab'] == 'setting'){
				//Save general setting
				$add_methods = array('yomigana', 'honorific-suffix', 'company-name', 'free-shipping', 'zip2address', 'yahoo-app-id');
				$this->wc4jp_save_methods( $add_methods );
				self::add_message( __( 'Your settings have been saved.', 'woocommerce' ) );
			}
			if(isset($_GET['tab']) and $_GET['tab'] == 'payment'){
				//Save payment setting
				$payment_methods = array('bankjp','postofficebank','atstore','cod2');
				foreach($payment_methods as $payment_method){
					$woocommerce_settings = get_option('woocommerce_'.$payment_method.'_settings');
					if(isset($_POST[$payment_method]) && $_POST[$payment_method]){
						update_option( $this->prefix.$payment_method, $_POST[$payment_method]);
						if(isset($woocommerce_settings)){
							$woocommerce_settings['enabled'] = 'yes';
							update_option( 'woocommerce_'.$payment_method.'_settings', $woocommerce_settings);
						}
					}else{
						update_option( $this->prefix.$payment_method, '');
						if(isset($woocommerce_settings)){
							$woocommerce_settings['enabled'] = 'no';
							update_option( 'woocommerce_'.$payment_method.'_settings', $woocommerce_settings);
						}
					}
				}
				self::add_message( __( 'Your settings have been saved.', 'woocommerce' ) );
			}
			if(isset($_GET['tab']) and $_GET['tab'] == 'shipment'){
				//Save shipment setting
				$add_methods = array('delivery-date','start-date','reception-period','unspecified-date','delivery-deadline','no-mon','no-tue','no-wed','no-thu','no-fri','no-sat','no-sun','holiday-start-date','holiday-end-date','delivery-time-zone','unspecified-time','date-format');
				$this->wc4jp_save_methods( $add_methods );
				//Save timezones setting
				$time_zones = array();
				if ( isset( $_POST['start_time'] ) ) {

					$start_times   = array_map( 'wc_clean', $_POST['start_time'] );
					$end_times = array_map( 'wc_clean', $_POST['end_time'] );

					foreach ( $start_times as $i => $start_time ) {
						if ( ! isset( $start_times[ $i ] ) ) {
							continue;
						}
						$time_zones[] = array(
							'start_time'      => $start_times[ $i ],
							'end_time'      => $end_times[ $i ],
						);
					}
				}
				update_option( 'wc4jp_time_zone_details', $time_zones);
				$this->add_message( __( 'Your settings have been saved.', 'woocommerce' ) );
			}
		}
	}
	/**
	 * Add a message.
	 * @param string $text
	 */
	public function add_message( $text ) {
		$this->messages[] = $text;
	}

	/**
	 * Add an error.
	 * @param string $text
	 */
	public function add_error( $text ) {
		$this->errors[] = $text;
	}

	/**
	 * Output messages + errors.
	 */
	public function show_messages() {
		if ( sizeof( $this->errors ) > 0 ) {
			foreach ( $this->errors as $error ) {
				echo '<div id="message" class="error inline"><p><strong>' . esc_html( $error ) . '</strong></p></div>';
			}
		} elseif ( sizeof( $this->messages ) > 0 ) {
			foreach ( $this->messages as $message ) {
				echo '<div id="message" class="updated inline"><p><strong>' . esc_html( $message ) . '</strong></p></div>';
			}
		}
	}
	/**
	 * Yomigana option.
	 * 
	 * @return mixed
	 */
	public function wc4jp_save_methods( $add_methods ) {
		foreach($add_methods as $add_method){
			if(isset($_POST[$add_method]) && $_POST[$add_method]){
				update_option( $this->prefix.$add_method, $_POST[$add_method]);
			}else{
				update_option( $this->prefix.$add_method, '');
			}
		}
	}

	/**
	 * Yomigana option.
	 * 
	 * @return mixed
	 */
	public function wc4jp_options_yomigana() {
		$title = __( 'Name Yomigana', 'woocommerce-for-japan' );
		$descritpion = $this->wc4jp_description_address_pattern( $title );
		$this->wc4jp_setting->wc4jp_input_checkbox('yomigana', $descritpion, $this->prefix);
	}
	/**
	 * Honorific Suffix option.
	 * 
	 * @return mixed
	 */
	public function wc4jp_options_honorific_suffix() {
		$title = __( 'Honorific Suffix(Sama)', 'woocommerce-for-japan' );
		$descritpion = $this->wc4jp_description_address_pattern( $title );
		$this->wc4jp_setting->wc4jp_input_checkbox('honorific-suffix', $descritpion, $this->prefix);
	}
	/**
	 * Company Name option.
	 * 
	 * @return mixed
	 */
	public function wc4jp_options_company_name() {
		$title = __( 'Company Name', 'woocommerce-for-japan' );
		$descritpion = $this->wc4jp_description_address_pattern( $title );
		$this->wc4jp_setting->wc4jp_input_checkbox('company-name', $descritpion, $this->prefix);
	}
	/**
	 * Free Shipping Display option.
	 * 
	 * @return mixed
	 */
	public function wc4jp_options_free_shipping() {
		$title = __( 'Free Shipping Display', 'woocommerce-for-japan' );
		$descritpion = $this->wc4jp_description_address_pattern( $title );
		$this->wc4jp_setting->wc4jp_input_checkbox('free-shipping', $descritpion, $this->prefix);
	}
	/**
	 * Free Shipping Display option.
	 * 
	 * @return mixed
	 */
	public function wc4jp_options_zip2address() {
		$title = __( 'Automatic zip code entry', 'woocommerce-for-japan' );
		$descritpion = $this->wc4jp_setting->wc4jp_description_check_pattern( $title );
		$this->wc4jp_setting->wc4jp_input_checkbox('zip2address', $descritpion, $this->prefix);
	}
	/**
	 * Free Shipping Display option.
	 * 
	 * @return mixed
	 */
	public function wc4jp_options_yahoo_app_id() {
		$title = __( 'Yahoo! APP ID', 'woocommerce-for-japan' );
		$descritpion = sprintf(__( 'If you use it a bit for testing, you do not need to enter it here. But if you want to use Automatic zip code entry, you must get and input %s here. Please get it from <a href="https://e.developer.yahoo.co.jp/dashboard/" target="_blank">here</a>. If you need our support, please access <a href="https://wc.artws.info/shop/setting-support/yahoo-app-id/" target="_blank">here</a> and pay for an aid to development costs ', 'woocommerce-for-japan' ), $title);
		$this->wc4jp_setting->wc4jp_input_text('yahoo-app-id', $descritpion, 60, '',$this->prefix);
	}
	/**
	 * Delivery date designation enable.
	 * 
	 * @return mixed
	 */
	public function wc4jp_delivery_date_designation(){
		$title = __( 'Delivery date designation', 'woocommerce-for-japan' );
		$descritpion = $this->wc4jp_setting->wc4jp_description_check_pattern( $title );
		$this->wc4jp_setting->wc4jp_input_checkbox('delivery-date', $descritpion, $this->prefix);
	}
	/**
	 * Start date for delivery date.
	 * 
	 * @return mixed
	 */
	public function wc4jp_start_date(){
		$title = __( 'Start Date', 'woocommerce-for-japan' );
		$descritpion = __( 'Please enter the number of days until the first day you can receive the delivery date / time. If you enter 0 you can specify delivery from today.', 'woocommerce-for-japan' );
		$this->wc4jp_setting->wc4jp_input_number('start-date', $descritpion, 2,$this->prefix);
	}
	/**
	 * Term for delivery date.
	 * 
	 * @return mixed
	 */
	public function wc4jp_reception_period(){
		$title = __( 'Reception period', 'woocommerce-for-japan' );
		$descritpion = __( 'Please enter the number of days for which you can accept the delivery reservation. Please enter 1 or more.', 'woocommerce-for-japan' );
		$this->wc4jp_setting->wc4jp_input_number('reception-period', $descritpion, 7,$this->prefix);
	}
	/**
	 * Unspecified for delivery date.
	 * 
	 * @return mixed
	 */
	public function wc4jp_unspecified_date(){
		$title = __( 'Unspecified Date', 'woocommerce-for-japan' );
		$descritpion = __( 'Please enter the sentence when you do not need to specify the delivery date.', 'woocommerce-for-japan' );
		$this->wc4jp_setting->wc4jp_input_text('unspecified-date', $descritpion, 60, __( 'No specified date', 'woocommerce-for-japan' ), $this->prefix);
	}
	/**
	 * Unspecified for delivery date.
	 * 
	 * @return mixed
	 */
	public function wc4jp_date_format(){
		$title = __( 'Date format', 'woocommerce-for-japan' );
		$descritpion = __( 'Please enter the format when you want to save the data by format. Use PHP format type.', 'woocommerce-for-japan' );
		$descritpion .= '<a href="http://php.net/manual/ja/function.date.php" target="_blank">'.' '.__('Check from here.', 'woocommerce-for-japan' ).'</a>';
		$this->wc4jp_setting->wc4jp_input_text('date-format', $descritpion, 10, 'Y/m/d', $this->prefix);
	}
	/**
	 * Delivery deadline setting.
	 * 
	 * @return mixed
	 */
	public function wc4jp_delivery_deadline(){
		$title = __( 'Delivery deadline', 'woocommerce-for-japan' );
		$descritpion = __( 'Please enter the time delivery deadline of your store.', 'woocommerce-for-japan' );
		$this->wc4jp_setting->wc4jp_input_time('delivery-deadline', $descritpion, '15:00', $this->prefix);
	}
	/**
	 * Delivery No delivery weekday setting.
	 * 
	 * @return mixed
	 */
	public function wc4jp_no_delivery_weekday(){
		$weekdays = array(
			array(
				'label' => __( 'Sunday', 'woocommerce-for-japan' ),
				'name'=> 'no-sun',
				'bold'=> true,
			),
			array(
				'label' => __( 'Moday', 'woocommerce-for-japan' ),
				'name'=> 'no-mon',
			),
			array(
				'label' => __( 'Tuesday', 'woocommerce-for-japan' ),
				'name'=> 'no-tue',
			),
			array(
				'label' => __( 'Wednesday', 'woocommerce-for-japan' ),
				'name'=> 'no-wed',
			),
			array(
				'label' => __( 'Thursday', 'woocommerce-for-japan' ),
				'name'=> 'no-thu',
			),
			array(
				'label' => __( 'Friday', 'woocommerce-for-japan' ),
				'name'=> 'no-fri',
			),
			array(
				'label' => __( 'Saturday', 'woocommerce-for-japan' ),
				'name'=> 'no-sat',
				'bold'=> true,
			),
		);
		foreach($weekdays as $weekday){
			if(isset($weekday['bold']))echo '<strong>';
			echo $weekday['label'].$this->wc4jp_setting->wc4jp_input_checkbox($weekday['name'], null, $this->prefix).' ';
			if(isset($weekday['bold']))echo '</strong>';
		}
		echo '<br />'.__( 'Please check the days of the week that you do not ship. In case of continuous, we will correspond up to three days.', 'woocommerce-for-japan' );
	}
	/**
	 * Delivery holioday term setting.
	 * 
	 * @return mixed
	 */
	public function wc4jp_holiday_term(){
		$start_date = array(
			'label' => __( 'Holiday Start Date', 'woocommerce-for-japan' ),
			'id' => 'holiday_start_date',
			'name'=> 'holiday-start-date',
			'title'=> 'Start date:YYYY-MM-DD (Blank:Not specified)',
		);
		$end_date = array(
			'label' => __( 'Holiday End Date', 'woocommerce-for-japan' ),
			'id' => 'holiday_end_date',
			'name'=> 'holiday-end-date',
			'title'=> 'End date:YYYY-MM-DD (Blank:Not specified)',
		);
		$title = __( 'Holiday term', 'woocommerce-for-japan' );
		$descritpion = __( 'Please enter the date term long holiday of your store.', 'woocommerce-for-japan' );
		$this->wc4jp_setting->wc4jp_input_date_term($start_date, $end_date, $descritpion, $this->prefix);
	}

	/**
	 * Delivery time zone enable.
	 * 
	 * @return mixed
	 */
	public function wc4jp_delivery_time_zone(){
		$title = __( 'Delivery time zone', 'woocommerce-for-japan' );
		$descritpion = $this->wc4jp_setting->wc4jp_description_check_pattern( $title );
		$this->wc4jp_setting->wc4jp_input_checkbox('delivery-time-zone', $descritpion, $this->prefix);
	}
	/**
	 * Unspecified for delivery time zone.
	 * 
	 * @return mixed
	 */
	public function wc4jp_unspecified_time(){
		$title = __( 'Unspecified Time', 'woocommerce-for-japan' );
		$descritpion = __( 'Please enter the sentence when you do not need to specify the delivery time.', 'woocommerce-for-japan' );
		$this->wc4jp_setting->wc4jp_input_text('unspecified-time', $descritpion, 60, __( 'No designated time zone', 'woocommerce-for-japan' ), $this->prefix);
	}
	/**
	 * Delivery time zone Management.
	 * 
	 * @return mixed
	 */
	public function wc4jp_delivery_time_zone_mgn(){
		$title = __( 'Delivery time zone Management', 'woocommerce-for-japan' );
		$time_zone_details = array(
			array ( 'start_time' => '08:00', 'end_time' => '12:00' ), 
			array ( 'start_time' => '12:00', 'end_time' => '14:00' ),
			array ( 'start_time' => '14:00', 'end_time' => '16:00' ),
			array ( 'start_time' => '16:00', 'end_time' => '18:00' ),
			array ( 'start_time' => '18:00', 'end_time' => '20:00' ),
			array ( 'start_time' => '19:00', 'end_time' => '21:00' ),
			array ( 'start_time' => '20:00', 'end_time' => '21:00' ),
		);
		$this->wc4jp_input_time_zone_html($time_zone_details );
	}
	/**
	 * Delivery tracking enable.
	 * 
	 * @return mixed
	 */
	public function wc4jp_delivery_tracking(){
		$title = __( 'Delivery tracking', 'woocommerce-for-japan' );
		$descritpion = $this->wc4jp_setting->wc4jp_description_check_pattern( $title );
		$this->wc4jp_setting->wc4jp_input_checkbox('delivery-tracking', $descritpion, $this->prefix);
	}
	/**
	 * BANK PAYMENT IN JAPAN option.
	 * 
	 * @return mixed
	 */
	public function wc4jp_options_bankjp() {
		$title = __( 'BANK PAYMENT IN JAPAN', 'woocommerce-for-japan' );
		$descritpion = $this->wc4jp_setting->wc4jp_description_payment_pattern( $title );
		$this->wc4jp_setting->wc4jp_input_checkbox('bankjp', $descritpion, $this->prefix);
	}
	/**
	 * Postal transfer option.
	 * 
	 * @return mixed
	 */
	public function wc4jp_options_postofficebank() {
		$title = __( 'Postal transfer', 'woocommerce-for-japan' );
		$descritpion = $this->wc4jp_setting->wc4jp_description_payment_pattern( $title );
		$this->wc4jp_setting->wc4jp_input_checkbox('postofficebank', $descritpion, $this->prefix);
	}
	/**
	 * Pay at store option.
	 * 
	 * @return mixed
	 */
	public function wc4jp_options_atstore() {
		$title = __( 'Pay at store', 'woocommerce-for-japan' );
		$descritpion = $this->wc4jp_setting->wc4jp_description_payment_pattern( $title );
		$this->wc4jp_setting->wc4jp_input_checkbox('atstore', $descritpion, $this->prefix);
	}
	/**
	 * Postal transfer option.
	 * 
	 * @return mixed
	 */
	public function wc4jp_options_cod2() {
		$title = __( 'COD for Subscriptions', 'woocommerce-for-japan' );
		$descritpion = $this->wc4jp_setting->wc4jp_description_payment_pattern( $title );
		$this->wc4jp_setting->wc4jp_input_checkbox('cod2', $descritpion, $this->prefix);
	}
	/**
	 * create input time zone form.
	 * 
	 * @return mixed
	 */
	 public function wc4jp_input_time($slug, $descritpion, $default_value){
	    ?>
		<label for="woocommerce_input_<?php echo $slug;?>">
		<?php 
			$wc4jp_meta_name = 'wc4jp-'.$slug;
			if(get_option($wc4jp_meta_name)){
				$wc4jp_options_setting = get_option($wc4jp_meta_name) ;
			}else{
				$wc4jp_options_setting = $default_value;
			}
			?>
			<input type="time" name="<?php echo $slug;?>" value="<?php echo $wc4jp_options_setting; ?>" ><br />
			<?php echo $descritpion; ?>
		</label>
	<?php }
	/**
	 * create input time zone form.
	 * 
	 * @return mixed
	 */
	 public function wc4jp_input_time_zone_html( $default_value ){
		if(get_option( 'wc4jp_time_zone_details')){			
	    	$time_zone_details = get_option( 'wc4jp_time_zone_details',
				array(
					array(
						'start_time'      => $this->get_option( 'start_time' ),
						'end_time'   => $this->get_option( 'end_time' ),
					)
				)
			);
		}else{
			$time_zone_details = $default_value;
		}
?>
	 			    <table class="widefat wc_input_table sortable" id="delivery_time_zone" cellspacing="0">
		    		<thead>
		    			<tr>
		    				<th class="sort" style="width: 17px;">&nbsp;</th>
			            	<th><?php _e( 'Delivery time zone start time', 'woocommerce-for-japan' ); ?></th>
			            	<th></th>
			            	<th><?php _e( 'Delivery time zone end time', 'woocommerce-for-japan' ); ?></th>
		    			</tr>
		    		</thead>
		    		<tfoot>
		    			<tr>
		    				<th colspan="7"><a href="#" class="add button"><?php _e( '+ Add Time Zone', 'woocommerce-for-japan' ); ?></a> <a href="#" class="remove_rows button"><?php _e( 'Remove selected Time Zone', 'woocommerce-for-japan' ); ?></a></th>
		    			</tr>
		    		</tfoot>
		    		<tbody class="time_zone">
		            	<?php
		            	$i = -1;
		            	if ( $time_zone_details ) {
		            		foreach ( $time_zone_details as $time_zone ) {
		                		$i++;

		                		echo '<tr class="time_zone">
		                			<td class="sort"></td>
		                			<td><input type="time" value="' . esc_attr( $time_zone['start_time'] ) . '" name="start_time[' . $i . ']" /></td>
		                			<td>~</td>
		                			<td><input type="time" value="' . esc_attr( $time_zone['end_time'] ) . '" name="end_time[' . $i . ']" /></td>
			                    </tr>';
		            		}
		            	}
		            	?>
		        	</tbody>
		        </table>
		       	<script type="text/javascript">
					jQuery(function() {
						jQuery('#delivery_time_zone').on( 'click', 'a.add', function(){

							var size = jQuery('#delivery_time_zone').find('tbody .time_zone').length;

							jQuery('<tr class="time_zone">\
		                			<td class="sort"></td>\
		                			<td><input type="time" name="start_time[' + size + ']" /></td>\
		                			<td>~</td>\
		                			<td><input type="time" name="end_time[' + size + ']" /></td>\
			                    </tr>').appendTo('table#delivery_time_zone tbody');

							return false;
						});
					});
				</script>
       <?php
	 }
	/**
	 * create description for address pattern.
	 * 
	 * @return mixed
	 */
	 public function wc4jp_description_address_pattern($title){
			$descritpion = sprintf(__( 'Please check it if you want to use input field for %s', 'woocommerce-for-japan' ), $title);
			return $descritpion;
	 }
	/**
	 * Validate options.
	 * 
	 * @param array $input
	 * @return array
	 */
	public function validate_options( $input ) {
//		if ( ! current_user_can( 'administrator' ) )
//			return $input;
		if ( isset( $_POST['save_wc4jp_options'] ) ) {
			add_settings_error( 'wc4jp_settings_errors', 'wc4jp_settings_saved', __( 'Settings saved.', 'woocommerce-for-japan' ), 'updated' );
		}
		return $input;
	}
	/**
	 * Plguins information display.
	 * 
	 * @return mixed
	 */
	public function wc4jp_informations_plugins() {
		echo sprintf(__('<a href="%s" target="_blank" title="Paygent Payment">Paygent Payment</a> :  You can handle Credit Card payment and Convini payment, etc<br >', 'woocommerce-for-japan'),'https://wc.artws.info/shop/wordpress-official/paygent-for-woocommerce/?utm_source=wc4jp-settings&utm_medium=link&utm_campaign=plugins-information');
		echo sprintf(__('<a href="%s" target="_blank" title="WooCommerce Subscriptions">WooCommerce Subscriptions</a> : You can handle Subscriptions.<br >', 'woocommerce-for-japan'),'https://wc.artws.info/shop/woothemes-official/woocommerce-subscriptions/?utm_source=wc4jp-settings&utm_medium=link&utm_campaign=plugins-information');
	}
	/**
	 * Services information display.
	 * 
	 * @return mixed
	 */
	public function wc4jp_informations_services() {
		echo sprintf(__('<a href="%s" target="_blank" title="Payment Setting Support">Payment Setting Support</a> :  We support Payment Plugins Setting.<br >', 'woocommerce-for-japan'),'https://wc.artws.info/shop/setting-support/payment-support/?utm_source=wc4jp-settings&utm_medium=link&utm_campaign=services-information');
		echo sprintf(__('<a href="%s" target="_blank" title="Support Tickets">Support Tickets</a> : We support your WordPress and WooCommmerce questions.<br >', 'woocommerce-for-japan'),'https://wc.artws.info/shop/maintenance-support/freshdesk-support-tickets/?utm_source=wc4jp-settings&utm_medium=link&utm_campaign=services-information');
		echo sprintf(__('<a href="%s" target="_blank" title="Maintenance Support">Maintenance Support</a> : We support your WordPress and WooCommmerce site, update or somethings.<br >', 'woocommerce-for-japan'),'https://wc.artws.info/shop/maintenance-support/woocommerce-professional-support-subscription/?utm_source=wc4jp-settings&utm_medium=link&utm_campaign=services-information');
	}

	/**
	 * This function is similar to the function in the Settings API, only the output HTML is changed.
	 * Print out the settings fields for a particular settings section
	 *
	 * @global $wp_settings_fields Storage array of settings fields and their pages/sections
	 *
	 * @since 0.1
	 *
	 * @param string $page Slug title of the admin page who's settings fields you want to show.
	 * @param string $section Slug title of the settings section who's fields you want to show.
	 */
	public function do_settings_sections( $page ) {
		global $wp_settings_sections, $wp_settings_fields;
	 
		if ( ! isset( $wp_settings_sections[$page] ) )
			return;
	 
		foreach ( (array) $wp_settings_sections[$page] as $section ) {
			echo '<div id="" class="stuffbox postbox '.$section['id'].'">';
			echo '<button type="button" class="handlediv button-link" aria-expanded="true"><span class="screen-reader-text">' . __('Toggle panel', 'woocommerce-for-japan') . '</span><span class="toggle-indicator" aria-hidden="true"></span></button>';
			if ( $section['title'] )
				echo "<h3 class=\"hndle\"><span>{$section['title']}</span></h3>\n";
	 
			if ( $section['callback'] )
				call_user_func( $section['callback'], $section );

			if ( ! isset( $wp_settings_fields ) || !isset( $wp_settings_fields[$page] ) || !isset( $wp_settings_fields[$page][$section['id']] ) )
				continue;
			echo '<div class="inside"><table class="form-table">';
			do_settings_fields( $page, $section['id'] );
			echo '</table></div>';
			echo '</div>';
		}
	}
	/**
	 * Enqueue admin scripts and styles.
	 * 
	 * @global $pagenow
	 */
	public function admin_enqueue_scripts( $page ) {
//		global $pagenow;
		if ( $page === 'woocommerce_page_wc4jp-options' ) {
			wp_enqueue_script( 'wc4jp-admin-script', plugins_url( 'views/js/admin-settings.js', __FILE__ ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable', 'jquery-ui-slider', 'jquery-ui-button' ), WC4JP_VERSION );
			wp_enqueue_script( 'postbox' );
		}
	}
	/**
	 * Get a setting from the settings API.
	 *
	 * @param mixed $option_name
	 * @return string
	 */
	public function get_option( $option_name, $default = '' ) {
		// Array value
		if ( strstr( $option_name, '[' ) ) {

			parse_str( $option_name, $option_array );

			// Option name is first key
			$option_name = current( array_keys( $option_array ) );

			// Get value
			$option_values = get_option( $option_name, '' );

			$key = key( $option_array[ $option_name ] );

			if ( isset( $option_values[ $key ] ) ) {
				$option_value = $option_values[ $key ];
			} else {
				$option_value = null;
			}

		// Single value
		} else {
			$option_value = get_option( $option_name, null );
		}

		if ( is_array( $option_value ) ) {
			$option_value = array_map( 'stripslashes', $option_value );
		} elseif ( ! is_null( $option_value ) ) {
			$option_value = stripslashes( $option_value );
		}

		return $option_value === null ? $default : $option_value;
	}

}

new WC_4JP_Admin_Screen();