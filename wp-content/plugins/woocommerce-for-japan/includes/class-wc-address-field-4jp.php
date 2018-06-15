<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * WooCommerce For Japan

 * @package woocommerce-for-japan
 * @version     1.2.20
 * @category Address for Japan
 * @author Artisan Workshop
 */

class AddressField4jp{
	
	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {
		// MyPage Edit And Checkout fields.
		add_filter( 'woocommerce_default_address_fields', array( $this, 'address_fields'));
		add_filter( 'woocommerce_billing_fields', array( $this, 'billing_address_fields'));
		add_filter( 'woocommerce_shipping_fields', array( $this, 'shipping_address_fields'), 20 );
		add_filter( 'woocommerce_formatted_address_replacements', array( $this, 'address_replacements'),20,2);
		add_filter( 'woocommerce_localisation_address_formats', array( $this, 'address_formats'),20);
		//My Account Display for address
		add_filter( 'woocommerce_my_account_my_address_formatted_address', array( $this, 'formatted_address'),20,3);//template/myaccount/my-address.php
		//Check Out Display for address
		add_filter( 'woocommerce_order_formatted_billing_address', array( $this, 'wc4jp_billing_address'),10,2);
		add_filter( 'woocommerce_order_formatted_shipping_address', array( $this, 'wc4jp_shipping_address'),20,2);
		add_action( 'woocommerce_admin_order_data_after_shipping_address', array( $this, 'admin_order_data_after_shipping_address'), 10 );
		//include get_order function
		add_filter( 'woocommerce_get_order_address', array( $this, 'wc4jp_get_order_address'),20,3);//includes/abstract/abstract-wc-order.php
		//Admin CSS file 
		add_action( 'admin_enqueue_scripts', array( $this, 'load_custom_wc4jp_admin_style') ,20);
		//FrontEnd CSS file 
		add_action( 'wp_enqueue_scripts', array( $this, 'checkout_enqueue_style'), 10 );

		//
		add_action( 'woocommerce_after_checkout_billing_form', array( $this, 'auto_zip2address_billing'), 10 );
		add_action( 'woocommerce_after_checkout_shipping_form', array( $this, 'auto_zip2address_shipping'), 10 );
		add_action( 'woocommerce_after_edit_address_form_billing', array( $this, 'auto_zip2address_billing'), 10 );
		add_action( 'woocommerce_after_edit_address_form_shipping', array( $this, 'auto_zip2address_myaccount_shipping'), 10 );
		
		// Display delivery date and time
		add_action( 'woocommerce_before_order_notes', array( $this, 'delivery_date_designation'), 10  );
		// Save delivery date and time values to order
		add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'update_order_meta' ) );
		// Show on order detail page (frontend)
		add_action( 'woocommerce_order_details_after_order_table', array( $this, 'frontend_order_timedate' ) );
		// Show on order detail email (frontend)
		add_filter( 'woocommerce_email_order_meta', array( $this, 'email_order_delivery_details' ), 10, 4 );
		// Shop Order functions
		add_filter( 'manage_edit-shop_order_columns', array( $this, 'shop_order_columns' ) );
		add_action( 'manage_shop_order_posts_custom_column', array( $this, 'render_shop_order_columns' ), 2 );
		// Shop Order functions
//		add_action( 'woocommerce_admin_order_data_after_billing_address', array( $this, 'display_admin_order_meta' ), 10, 1 );

		// Admin Edit Address
		add_filter( 'woocommerce_admin_billing_fields', array( $this, 'admin_billing_address_fields'));
		add_filter( 'woocommerce_admin_shipping_fields', array( $this, 'admin_shipping_address_fields'));
		add_filter( 'woocommerce_customer_meta_fields', array( $this, 'admin_customer_meta_fields'));
		// Change current_user->display_name to Japanese style when update user data
		add_filter( 'woocommerce_update_customer_args', array( $this, 'wc4jp_update_customer_args'), 10, 2 );
		// display in Order meta box ship date and time.
		add_action('add_meta_boxes', array($this, 'add_meta_box'));
		add_action('woocommerce_process_shop_order_meta', array($this, 'save_meta_box'), 0, 2);
		// State() add Ken and Fu.
		add_filter( 'woocommerce_states', array($this, 'wc4jp_states'), 99 );
	}
	//Default address fields
	public function address_fields( $fields ) {
		$fields = array(
			'country' => array(
				'type'     => 'country',
				'label'    => __( 'Country', 'woocommerce-for-japan' ),
				'required' => true,
				'class'    => array( 'form-row-wide', 'address-field', 'update_totals_on_change' ),
			),
			'last_name'    => array(
				'label'    => __( 'Last Name', 'woocommerce-for-japan' ),
				'required' => true,
				'class'    => array( 'form-row-first' ),
			),
			'first_name' => array(
				'label'    => __( 'First Name', 'woocommerce-for-japan' ),
				'required' => true,
				'class'    => array( 'form-row-last' ),
				'clear'    => true
			),
			'yomigana_last_name' => array(
				'label'    => __( 'Last Name (Yomigana)', 'woocommerce-for-japan' ),
				'required' => true,
				'class'    => array( 'form-row-first' ),
			),
			'yomigana_first_name' => array(
				'label'    => __( 'First Name (Yomigana)', 'woocommerce-for-japan' ),
				'required' => true,
				'class'    => array( 'form-row-last' ),
				'clear'    => true
			),
			'company' => array(
				'label' => __( 'Company Name', 'woocommerce-for-japan' ),
				'class' => array( 'form-row-wide' ),
			),
			'postcode' => array(
				'label'       => __( 'Postcode / Zip', 'woocommerce-for-japan' ),
				'placeholder' => _x( '123-4567', 'placeholder', 'woocommerce-for-japan' ),
				'required'    => true,
				'class'       => array( 'form-row-first', 'address-field' ),
				'validate'    => array( 'postcode' ),
				'priority' => '',
				'clear'       => false
			),
			'state' => array(
				'type'        => 'state',
				'label'       => __( 'Prefecture', 'woocommerce-for-japan' ),
				'required'    => true,
				'class'       => array( 'form-row-last', 'address-field' ),
				'validate'    => array( 'state' ),
				'priority' => '',
				'clear'       => true
			),
			'city' => array(
				'label'       => __( 'Town / City', 'woocommerce-for-japan' ),
				'placeholder' => __( 'Town / City', 'woocommerce-for-japan' ),
				'required'    => true,
				'class'       => array( 'form-row-wide', 'address-field' )
			),
			'address_1' => array(
				'label'       => __( 'Address', 'woocommerce-for-japan' ),
				'placeholder' => _x( 'Street address', 'placeholder', 'woocommerce-for-japan' ),
				'required'    => true,
				'class'       => array( 'form-row-wide', 'address-field' )
			),
			'address_2' => array(
				'placeholder' => _x( 'Apartment, suite, unit etc. (optional)', 'placeholder', 'woocommerce-for-japan' ),
				'class'       => array( 'form-row-wide', 'address-field' ),
				'required'    => false
			),
			'country' => array(
				'type'         => 'country',
				'label'        => __( 'Country', 'woocommerce' ),
				'required'     => true,
				'class'        => array( 'form-row-wide', 'address-field', 'update_totals_on_change' ),
				'autocomplete' => 'country',
				'priority'     => 40,
			)
		);
		if(!get_option( 'wc4jp-yomigana'))unset($fields['yomigana_last_name'],$fields['yomigana_first_name']);
		return $fields;
	}
	// Billing/Shipping Specific
	public function billing_address_fields( $fields ) {
		$address_fields = $fields;
		$address_fields['billing_state']['class'] = array( 'form-row-last', 'address-field' );
		$address_fields['billing_state']['clear'] = true;
		$address_fields['billing_phone']['label'] = __( 'Billing Phone', 'woocommerce-for-japan' );
		$address_fields['billing_phone']['clear'] = true;
		if(!get_option( 'wc4jp-company-name'))unset($address_fields['billing_company']);
		return $address_fields;
	}
	public function shipping_address_fields( $fields ) {
		$address_fields = $fields;

		$address_fields['shipping_state']['class'] = array( 'form-row-last', 'address-field' );
		$address_fields['shipping_state']['clear'] = true;
		$address_fields['shipping_phone'] = array(
			'label' 		=> __( 'Shipping Phone', 'woocommerce-for-japan' ),
			'required' 		=> true,
			'class' 		=> array( 'form-row-wide' ),
			'clear'			=> true,
			'validate'		=> array( 'phone' ),
		);
		if(!get_option( 'wc4jp-company-name'))unset($address_fields['shipping_company']);
		return $address_fields;
	}

	public function address_replacements( $fields, $args ) {
		$fields['{name}'] = $args['last_name'] . ' ' . $args['first_name'];
		$fields['{name_upper}'] = strtoupper( $args['last_name'] . ' ' . $args['first_name'] );
		if(get_option( 'wc4jp-yomigana')){
			$fields['{yomigana_last_name}'] = $args['yomigana_last_name'];
			$fields['{yomigana_first_name}'] = $args['yomigana_first_name'];
		}
		if(version_compare( WC_VERSION, '3.1', '<=' )){
			$fields['{phone}'] = $args['phone'];
		}

		return $fields;
	}
	public function address_formats( $fields ) {
		//honorific suffix
		$honorific_suffix = '';
		if(get_option('wc4jp-honorific-suffix'))$honorific_suffix = '様';
		
		if(version_compare( WC_VERSION, '3.1', '>=' )){
			if(get_option( 'wc4jp-company-name') and get_option( 'wc4jp-yomigana')){
				$fields['JP'] = "〒{postcode}\n{state}{city}{address_1}\n{address_2}\n{company}\n{yomigana_last_name} {yomigana_first_name}\n{last_name} {first_name}".$honorific_suffix."\n {country}";
			}
			if(!get_option( 'wc4jp-company-name') and get_option( 'wc4jp-yomigana')){
				$fields['JP'] = "〒{postcode}\n{state}{city}{address_1}\n{address_2}\n{yomigana_last_name} {yomigana_first_name}\n{last_name} {first_name}".$honorific_suffix."\n {country}";
			}
			if(!get_option( 'wc4jp-company-name') and !get_option( 'wc4jp-yomigana')){
				$fields['JP'] = "〒{postcode}\n{state}{city}{address_1}\n{address_2}\n{last_name} {first_name}".$honorific_suffix."\n {country}";
			}
		}else{
			if(get_option( 'wc4jp-company-name') and get_option( 'wc4jp-yomigana')){
				$fields['JP'] = "〒{postcode}\n{state}{city}{address_1}\n{address_2}\n{company}\n{yomigana_last_name} {yomigana_first_name}\n{last_name} {first_name}".$honorific_suffix."\n {phone}\n {country}";
			}
			if(!get_option( 'wc4jp-company-name') and get_option( 'wc4jp-yomigana')){
				$fields['JP'] = "〒{postcode}\n{state}{city}{address_1}\n{address_2}\n{yomigana_last_name} {yomigana_first_name}\n{last_name} {first_name}".$honorific_suffix."\n {phone}\n {country}";
			}
			if(!get_option( 'wc4jp-company-name') and !get_option( 'wc4jp-yomigana')){
				$fields['JP'] = "〒{postcode}\n{state}{city}{address_1}\n{address_2}\n{last_name} {first_name}".$honorific_suffix."\n {phone}\n {country}";
			}			
		}
		return $fields;
	}
	public function formatted_address( $fields, $customer_id, $name) {
		if(version_compare( WC_VERSION, '2.7', '>' )){
			$fields['yomigana_first_name']  = get_user_meta( $customer_id, $name . '_yomigana_first_name', true );
			$fields['yomigana_last_name']  = get_user_meta( $customer_id, $name . '_yomigana_last_name', true );
			$fields['phone']  = get_user_meta( $customer_id, $name . '_phone', true );
		}else{
			$fields['yomigana_first_name']  = get_user_meta( $customer_id, $name . '_yomigana_first_name', true );
			$fields['yomigana_last_name']  = get_user_meta( $customer_id, $name . '_yomigana_last_name', true );
			$fields['phone']  = get_user_meta( $customer_id, $name . '_phone', true );
		}

		return $fields;
	}
	public function wc4jp_billing_address( $fields, $args) {
		if(version_compare( WC_VERSION, '2.7', '>=' )){
			$order_id = $args->get_id();
			$fields['yomigana_first_name'] = get_post_meta( $order_id, '_billing_yomigana_first_name', true );
			$fields['yomigana_last_name'] = get_post_meta( $order_id, '_billing_yomigana_last_name', true );
			$fields['phone'] = get_post_meta( $order_id, '_billing_phone', true );
		}else{
			$fields['yomigana_first_name'] = $args->billing_yomigana_first_name;
			$fields['yomigana_last_name'] = $args->billing_yomigana_last_name;
			$fields['phone'] = $args->billing_phone;
		}
		if($fields['country'] == '')$fields['country'] = 'JP';

		return $fields;
	}
	public function wc4jp_shipping_address( $fields, $args) {
		if(version_compare( WC_VERSION, '2.7', '>=' )){
			$order_id = $args->get_id();
			$fields['yomigana_first_name'] = get_post_meta( $order_id, '_shipping_yomigana_first_name', true );
			$fields['yomigana_last_name'] = get_post_meta( $order_id, '_shipping_yomigana_last_name', true );
			$fields['phone'] = get_post_meta( $order_id, '_shipping_phone', true );
		}else{
			$fields['yomigana_first_name'] = $args->shipping_yomigana_first_name;
			$fields['yomigana_last_name'] = $args->shipping_yomigana_last_name;
			$fields['phone'] = $args->shipping_phone;
		}
		if($fields['country'] == '')$fields['country'] = 'JP';

		return $fields;
	}
	public function admin_order_data_after_shipping_address( $order ){
		$order_id = version_compare( WC_VERSION, '2.7', '<' ) ? $order->id : $order->get_id();
		if(version_compare( WC_VERSION, '2.7', '>' )){
			$field['label'] = __( 'Shipping Phone', 'woocommerce-for-japan' );
			$field_value = get_post_meta( $order_id, '_shipping_phone', true );
			$field_value = wc_make_phone_clickable( $field_value );
			echo '<div style="display:block;clear:both;"><p><strong>' . esc_html( $field['label'] ) . ':</strong> ' . wp_kses_post( $field_value ) . '</p></div>';
		}
	}

	public function wc4jp_get_order_address( $address, $type, $args ){
		if(version_compare( WC_VERSION, '2.7', '>=' )){
			$order_id = $args->get_id();
			if ( 'billing' === $type ) {
				$address['yomigana_first_name'] = get_post_meta( $order_id, '_billing_yomigana_first_name', true );
				$address['yomigana_last_name'] = get_post_meta( $order_id, '_billing_yomigana_last_name', true );
			}else{
				$address['yomigana_first_name'] = get_post_meta( $order_id, '_shipping_yomigana_first_name', true );
				$address['yomigana_last_name'] = get_post_meta( $order_id, '_shipping_yomigana_last_name', true );
				$address['phone'] = get_post_meta( $order_id, '_shipping_phone', true );
			}
		}else{
			if ( 'billing' === $type ) {
				$address['yomigana_first_name'] =$args->billing_yomigana_first_name;
				$address['yomigana_last_name'] =$args->billing_yomigana_last_name;
			} else {
				$address['yomigana_first_name'] =$args->shipping_yomigana_first_name;
				$address['yomigana_last_name'] =$args->shipping_yomigana_last_name;
				$address['phone'] = $args->shipping_phone;
			}
		}
		return $address;
	}

	//Admin CSS file function
	public function load_custom_wc4jp_admin_style() {
		wp_register_style( 'custom_wc4jp_admin_css', plugins_url() . '/woocommerce-for-japan/includes/views/css/admin-wc4jp.css', false, WC4JP_VERSION );
		wp_enqueue_style( 'custom_wc4jp_admin_css' );
		$suffix       = '.min';

		// Register scripts
		wp_register_script( 'woocommerce_admin', WC()->plugin_url() . '/woocommerce/assets/js/admin/woocommerce_admin' . $suffix . '.js', array( 'jquery', 'jquery-blockui', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip' ), WC_VERSION );
		wp_enqueue_script( 'woocommerce_admin' );
	}

	//FrontEnd CSS file function
	public function checkout_enqueue_style() {
		$current_theme = wp_get_theme();
		if((is_checkout() or is_account_page() )and $current_theme->get( 'Name' ) == 'Storefront'){
			wp_register_style( 'custom_checkout_wc4jp_css', plugins_url() . '/woocommerce-for-japan/includes/views/css/checkout-wc4jp.css', false, WC4JP_VERSION );
			wp_enqueue_style( 'custom_checkout_wc4jp_css' );
		}
	}

	public function admin_billing_address_fields( $fields ) {
		foreach($fields as $key => $value){
			$new_fields[$key] = $value;
		}
		$fields = array(
			'last_name' => $new_fields['last_name'],
			'first_name' => $new_fields['first_name'],
			'country' => $new_fields['country'],
			'postcode' => $new_fields['postcode'],
			'state' => $new_fields['state'],
			'city' => $new_fields['city'],
			'company' => $new_fields['company'],
			'address_1' => $new_fields['address_1'],
			'address_2' => $new_fields['address_2'],
			'yomigana_last_name' => array(
				'label' => __( 'Last Name Yomigana', 'woocommerce-for-japan' ),
				'show'	=> false
			),
			'yomigana_first_name' => array(
				'label' => __( 'First Name Yomigana', 'woocommerce-for-japan' ),
				'show'	=> false
			),
			'email' => $new_fields['email'],
			'phone' => $new_fields['phone'],
		);
		if(!get_option( 'wc4jp-company-name'))unset($fields['company']);
		if(!get_option( 'wc4jp-yomigana')){
			unset($fields['yomigana_last_name']);
			unset($fields['yomigana_first_name']);
		};

		return $fields;
	}
	public function admin_shipping_address_fields( $fields ) {
		foreach($fields as $key => $value){
			$new_fields[$key] = $value;
		}
		$fields = array(
			'last_name' => $new_fields['last_name'],
			'first_name' => $new_fields['first_name'],
			'country' => $new_fields['country'],
			'postcode' => $new_fields['postcode'],
			'state' => $new_fields['state'],
			'city' => $new_fields['city'],
			'company' => $new_fields['company'],
			'address_1' => $new_fields['address_1'],
			'address_2' => $new_fields['address_2'],
			'yomigana_last_name' => array(
				'label' => __( 'Last Name Yomigana', 'woocommerce-for-japan' ),
				'show'	=> false
			),
			'yomigana_first_name' => array(
				'label' => __( 'First Name Yomigana', 'woocommerce-for-japan' ),
				'show'	=> false
			),
			'phone' => array(
				'label' => __( 'Phone', 'woocommerce-for-japan' ),
				'show'	=> false
			)
		);

		if(!get_option( 'wc4jp-company-name'))unset($fields['company']);
		if(!get_option( 'wc4jp-yomigana'))unset($fields['yomigana_last_name'],$fields['yomigana_first_name']);

		return $fields;
	}
	public function admin_customer_meta_fields( $fields ){
		$customer_meta_fields = $fields;
		//Billing fields
		$billing_fields = $fields['billing']['fields'];
		$customer_meta_fields['billing']['fields'] = array(
			'billing_last_name' => $billing_fields['billing_last_name'],
			'billing_first_name' => $billing_fields['billing_first_name'],
			'billing_yomigana_last_name' => array(
				'label' => __( 'Last Name Yomigana', 'woocommerce-for-japan' ),
				'description' => '',
			),
			'billing_yomigana_first_name' => array(
				'label' => __( 'First Name Yomigana', 'woocommerce-for-japan' ),
				'description' => '',
			),
			'billing_company'  => $billing_fields['billing_company'],
			'billing_country'  => $billing_fields['billing_country'],
			'billing_postcode' => $billing_fields['billing_postcode'],
			'billing_state'  => $billing_fields['billing_state'],
			'billing_city'  => $billing_fields['billing_city'],
			'billing_address_1'  => $billing_fields['billing_address_1'],
			'billing_address_2'  => $billing_fields['billing_address_2'],
			'billing_phone'  => $billing_fields['billing_phone'],
			'billing_email'  => $billing_fields['billing_email'],
		);
		//Shipping fields
		$shipping_fields = $fields['shipping']['fields'];
		$customer_meta_fields['shipping']['fields'] = array(
			'shipping_last_name' => $shipping_fields['shipping_last_name'],
			'shipping_first_name' => $shipping_fields['shipping_first_name'],
			'shipping_yomigana_last_name' => array(
				'label' => __( 'Last Name Yomigana', 'woocommerce-for-japan' ),
				'description' => '',
			),
			'shipping_yomigana_first_name' => array(
				'label' => __( 'First Name Yomigana', 'woocommerce-for-japan' ),
				'description' => '',
			),
			'shipping_company'  => $shipping_fields['shipping_company'],
			'shipping_country'  => $shipping_fields['shipping_country'],
			'shipping_postcode' => $shipping_fields['shipping_postcode'],
			'shipping_state'  => $shipping_fields['shipping_state'],
			'shipping_city'  => $shipping_fields['shipping_city'],
			'shipping_address_1'  => $shipping_fields['shipping_address_1'],
			'shipping_address_2'  => $shipping_fields['shipping_address_2'],
			'shipping_phone'  => array(
				'label' => __( 'Phone', 'woocommerce-for-japan' ),
				'description' => '',
			),
		);
		if(!get_option( 'wc4jp-company-name'))unset($customer_meta_fields['billing']['fields']['billing_company'], $customer_meta_fields['shipping']['fields']['shipping_company']);
		if(!get_option( 'wc4jp-yomigana'))unset($customer_meta_fields['billing']['fields']['billing_yomigana_last_name'], $customer_meta_fields['billing']['fields']['billing_yomigana_first_name'], $customer_meta_fields['shipping']['fields']['shipping_yomigana_last_name'], $customer_meta_fields['shipping']['fields']['shipping_yomigana_first_name']);
		return $customer_meta_fields;
	}

	// Automatic input from postal code to Address for billing
	public function auto_zip2address_billing(){
		$this->auto_zip2address( 'billing', apply_filters('billing_select_num',2) );
	}

	// Automatic input from postal code to Address for shipping
	public function auto_zip2address_shipping(){
		$this->auto_zip2address( 'shipping', apply_filters('shipping_select_num',2) );
	}

	// Automatic input from postal code to Address for shipping
	public function auto_zip2address_myaccount_shipping(){
		$this->auto_zip2address( 'shipping', 2 );
	}
	// Automatic input from postal code to Address
	function auto_zip2address( $method, $num ){
		global $states;
		$states = $this->wc4jp_states( $states );
		add_action( 'wp_enqueue_scripts', 'yahoo_api_scripts' );
		if(get_option( 'wc4jp-yahoo-app-id' )){
			$yahoo_app_id = get_option( 'wc4jp-yahoo-app-id' );
		}else{
			$yahoo_app_id = 'dj0zaiZpPWZ3VWp4elJ2MXRYUSZzPWNvbnN1bWVyc2VjcmV0Jng9MmY-';
		}
		if(version_compare( WC_VERSION, '2.7', '>=' )){
			$state_id = 'select2-'.$method.'_state-container';
		}else{
			$state_id = 'select2-chosen-'.$num;
		}
		if(get_option( 'wc4jp-zip2address' )){
			wp_enqueue_script( 'yahoo-app','https://map.yahooapis.jp/js/V1/jsapi?appid='.$yahoo_app_id,array('jquery'),WC4JP_VERSION);
			echo '
<script type="text/javascript">
// Search Japanese Postal number
jQuery(function($) {
$(document).ready(function(){
	$("#'.$method.'_postcode").keyup(function(){
	    var zip = $("#'.$method.'_postcode").val(),
	    zipCount = zip.length;
	    if(zipCount == 4 && zip.charAt(zipCount -1) != "-") {
		    alert("'.__('Please enter a hyphen [-] when entering a zip code.','woocommerce-for-japan').'");
	    }else if(zipCount > 7) {
    var url = "https://map.yahooapis.jp/search/zip/V1/zipCodeSearch";
    var param = {
        appid: "'.$yahoo_app_id.'",
        output: "json",
        query: $("#'.$method.'_postcode").val()
    };
    $.ajax({
        url: url,
        data: param,
        dataType: "jsonp",
        success: function(result) {
            var ydf = new Y.YDF(result);
            // Display Address from Zip
            dispZipToAddress'.$method.'(ydf);
        },
        error: function() {
            // Error handling
        }
    });
    }
    });
});
});
// Display Address from Zipcode
function dispZipToAddress'.$method.'(ydf) {
	var address = ydf.features[0].property.Address;
	var state = address.substr( 0, 3 );
	var states = new Array();';
	foreach($states['JP'] as $key => $value){
		$key = substr($key, 2);
		if($key == '14' || $key == "30" || $key == "46"){
			echo 'states['.$key.'] = "'.mb_substr($value, 0, 3).'";';
		}else{
			echo 'states['.$key.'] = "'.$value.'";';
		}
	}
		echo '
	var state_id = jQuery.inArray(state, states);
	jQuery("#'.$method.'_state").val(state_id);	
	var text_num = 3;
	if(state_id == "14" || state_id == "30" || state_id == "46"){
		text_num = 4;
	}
	var city = address.substr( text_num );
	jQuery("#'.$method.'_city").val(city);
	states[14] = "'.$states['JP']['JP14'].'";
	states[30] = "'.$states['JP']['JP30'].'";
	states[46] = "'.$states['JP']['JP46'].'";
	if(state_id > 9){
	document.getElementById("'.$method.'_state").value = "JP" + state_id;
	}else{
	document.getElementById("'.$method.'_state").value = "JP0" + state_id;
	}
	document.getElementById("'.$state_id.'").innerHTML = states[state_id];
}
</script>
		';
		}
	}
	// Delivery date designation
	public function delivery_date_designation(){
		$setting_methods = array( 'delivery-date','start-date','reception-period','unspecified-date','delivery-deadline','no-mon','no-tue','no-wed','no-thu','no-fri','no-sat','no-sun','holiday-start-date','holiday-end-date','delivery-time-zone','unspecified-time');
		foreach($setting_methods as $setting_method){
			$setting[$setting_method] = get_option( 'wc4jp-'.$setting_method );
		}
		if($setting['delivery-date'] or $setting['delivery-time-zone']){
			echo '<h3>'.__('Delivery request date and time', 'woocommerce-for-japan' ).'</h3>';
		}
		$this->delivery_date_display($setting);
		$this->delivery_time_display($setting);
	}
	//Display Delivery date select
	function delivery_date_display($setting){
		if(get_option( 'wc4jp-delivery-date' )){
			$time = new DateTime();
			$time = $time->format('H:i');
			$now = get_date_from_gmt($time);
			if (strtotime($now) > strtotime($setting['delivery-deadline'])){
				$setting['start-date'] = $setting['start-date'] + 1;
				$today = date_i18n('Y/m/d', strtotime('+1 day'));
			}else{
				$today = date_i18n('Y/m/d');
			}
			//The day of week check
			$weekday_options = array('0'=>'no-sun','1'=>'no-mon','2'=>'no-tue','3'=>'no-wed','4'=>'no-thu','5'=>'no-fri','6'=>'no-sat');
			$no_ship_weekdays = array();
			foreach($weekday_options as $key => $value){
				if(get_option( 'wc4jp-'.$value )){
					$no_ship_weekdays[$value] = $key;
				}
			}
			$datetime = new DateTime();
			$w = $datetime->format('w');
			if($w == 6){
				$tomorrow = 0;				
				$after_tomorrow = 1;
				$after_tomorrow2 = 2;
			}elseif($w == 5){
				$tomorrow = 6;
				$after_tomorrow = 0;
				$after_tomorrow2 = 1;
			}elseif($w == 4){
				$tomorrow = 5;
				$after_tomorrow = 6;
				$after_tomorrow2 = 0;
			}else{
				$tomorrow = $w + 1;
				$after_tomorrow = $w + 2;
				$after_tomorrow2 = $w + 3;
			}
			$no_ship_term = 0;
			if(array_search($tomorrow,$no_ship_weekdays)){
				if(array_search($after_tomorrow,$no_ship_weekdays)){
					if(array_search($after_tomorrow2,$no_ship_weekdays)){
						$no_ship_term = 3;
					}else{
						$no_ship_term = 2;
					}
				}else{
					$no_ship_term = 1;
				}
			}
			echo '<p class="form-row delivery-date" id="order_delivery_date_field">';
			echo '<label for="delivery_date" class="">'.__('Preferred delivery date', 'woocommerce-for-japan' ).'</label>';
			echo '<select name="wc4jp_delivery_date" class="input-select" id="wc4jp_delivery_date">';
			echo '<option value="0">'.$setting['unspecified-date'].'</option>';

			for($i = $setting['start-date']; $i <= $setting['start-date']+$setting['reception-period']; $i++){
				if(isset($setting['holiday-start-date']) and isset($setting['holiday-end-date']) and strtotime($today) >= strtotime($setting['holiday-start-date']) and strtotime($today) <= strtotime($setting['holiday-end-date']) ){
					$set_disp_date = new DateTime();
					$set_disp_date->setDate(substr($setting['holiday-end-date'],0,4), substr($setting['holiday-end-date'],5,2), substr($setting['holiday-end-date'],8,2));
					$set_disp_date->modify('+1 day');
				}else{
					$set_disp_date = new DateTime();
				}
				$add_days = $i + $no_ship_term;
				$set_disp_date->modify('+'.$add_days.' day');
				$set_disp_date = $set_disp_date->format('Y-m-d h:i:s');
				$valuedate[$i] = get_date_from_gmt($set_disp_date, 'Y-m-d');
				$dispdate[$i] = get_date_from_gmt($set_disp_date, __('Y/m/d', 'woocommerce-for-japan' ));
				echo '<option value="'.$valuedate[$i].'">'.$dispdate[$i].'</option>';
				if($i == $setting['start-date']){
					$setting['first-value-date'] = get_date_from_gmt($set_disp_date, 'Y-m-d');
					$setting['first-disp-date'] = get_date_from_gmt($set_disp_date, __('Y/m/d', 'woocommerce-for-japan' ));
				}
			}
			echo '</select>';
			echo '</p>';
		}
		do_action( 'after_wc4jp_delivery_date', $setting );
	}
	//Display Delivery time select
	function delivery_time_display($setting){
		$time_zone_setting = get_option( 'wc4jp_time_zone_details' );
		if(get_option( 'wc4jp-delivery-time-zone' )){
			echo '<p class="form-row delivery-time" id="order_delivery_time_field">';
			echo '<label for="delivery_time_zone" class="">'.__('Delivery Time Zone', 'woocommerce-for-japan' ).'</label>';
			echo '<select name="wc4jp_delivery_time_zone" class="input-select" id="wc4jp_delivery_time_zone">';
			echo '<option value="0">'.$setting['unspecified-time'].'</option>';
			$count_time_zone = count($time_zone_setting);
			for($i = 0; $i <= $count_time_zone - 1; $i++){
				echo '<option value="'.$time_zone_setting[$i]['start_time'].'-'.$time_zone_setting[$i]['end_time'].'">'.$time_zone_setting[$i]['start_time'].__('-', 'woocommerce-for-japan' ).$time_zone_setting[$i]['end_time'].'</option>';
			}
			echo '</select>';
			echo '</p>';
		}
	}
	/**
	 * Helper: Update order meta on successful checkout submission
	 *
	 * @param str $order_id
	 */
	function update_order_meta( $order_id ) {

		$date = false;
		$time = false;

		$date = apply_filters('wc4jp_delivery_date', $_POST['wc4jp_delivery_date'], $order_id );
		if( isset($date) && $date != 0 ){
			if(get_option( 'wc4jp-date-format' )){
				$date = strtotime($date);
				$date = date(get_option( 'wc4jp-date-format' ),$date);
			}
			update_post_meta( $order_id, 'wc4jp-delivery-date', esc_attr(htmlspecialchars($date)));
		}
		$time = apply_filters('wc4jp_time_zone', $_POST['wc4jp_delivery_time_zone'], $order_id );
		if( isset($time) && $time != 0 ){
			update_post_meta( $order_id, 'wc4jp-delivery-time-zone', esc_attr(htmlspecialchars($time)));
		}
	}
	/**
	 * Frontend: Add date and timeslot to frontend order overview
	 *
	 * @param obj $order
	 */
	function frontend_order_timedate( $order ){

		if( !$this->has_date_or_time( $order ) )
			return;

		$this->display_date_and_time_zone( $order, true );

	}
	/**
	 * Helper: Display Date and Timeslot
	 *
	 * @param obj $order
	 * @param bool $plain_text
	 */
	public function display_date_and_time_zone( $order, $show_title = false, $plain_text = false ) {

		$date_time = $this->has_date_or_time( $order );

		if( !$date_time )
			return;
		if($date_time['date'] === 0 ){$date_time['date'] = get_option( 'wc4jp-unspecified-date' );}
		if($date_time['time'] === 0 ){$date_time['time'] = get_option( 'wc4jp-unspecified-time' );}
		$date_time['date'] = apply_filters('wc4jp-unspecified-date', $date_time['date'], $order);
		$date_time['time'] = apply_filters('wc4jp-unspecified-time', $date_time['time'], $order);
		$show_title = apply_filters('wc4jp-show-title', $show_title, $date_time['date'], $date_time['time'], $order);

		if( $plain_text ) {

			echo "\n\n==========\n\n";

			if( $show_title ) {
				printf( "%s \n", strtoupper( apply_filters( 'wc4jp_delivery_details_text', __('Scheduled Delivery date and time', 'woocommerce-for-japan') ) ) );
			}

			if( $date_time['date'] ){
				printf( "\n%s: %s", apply_filters( 'wc4jp_delivery_date_text', __('Scheduled Delivery Date', 'woocommerce-for-japan') ), $date_time['date'] );
			}

			if( $date_time['time'] ){
				printf( "\n%s: %s", apply_filters( 'wc4jp_time_zone_text', __('Scheduled Time Zone', 'woocommerce-for-japan') ), $date_time['time'] );
			}

			echo "\n\n==========\n\n";

		} else {

			if( $show_title ) {
				printf( '<h2>%s</h2>', apply_filters( 'wc4jp_delivery_details_text', __('Scheduled Delivery date and time', 'woocommerce-for-japan') ) );
			}

			if( $date_time['date'] ){
				printf( "<p><strong>%s</strong> <br>%s</p>", apply_filters( 'wc4jp_delivery_date_text', __('Scheduled Delivery Date', 'woocommerce-for-japan') ), $date_time['date'] );
			}

			if( $date_time['time'] ){
				printf( "<p><strong>%s</strong> <br>%s</p>", apply_filters( 'wc4jp_time_zone_text', __('Scheduled Time Zone', 'woocommerce-for-japan') ), $date_time['time'] );
			}
		}
	}
	/**
	 * Frontend: Add date and timeslot to order email
	 *
	 * @param obj $order
	 * @param bool $sent_to_admin
	 * @param bool $plain_text
	 * @param obj $email
	 */
	function email_order_delivery_details( $order, $sent_to_admin, $plain_text, $email ) {

		if( !$this->has_date_or_time( $order ) )
			return;

		if( $plain_text ) {
			$this->display_date_and_time_zone( $order, true, true );
		} else {
			$this->display_date_and_time_zone( $order, true );
		}

	}
	/**
	 * Helper: Check if order has date or time
	 *
	 * @param obj $order
	 * @return bool
	 */
	function has_date_or_time( $order ) {

		$meta = array(
			'date' => false,
			'time' => false
		);
		$has_meta = false;
		$order_id = version_compare( WC_VERSION, '2.7', '<' ) ? $order->id : $order->get_id();

		$date = get_post_meta( $order_id, 'wc4jp-delivery-date', true);
		$time = get_post_meta( $order_id, 'wc4jp-delivery-time-zone', true);

		if( ( $date && $date != "" ) ) {
			$meta['date'] = $date;
			$has_meta = true;
		}

		if( ( $time && $time != "" ) ) {
			$meta['time'] = $time;
			$has_meta = true;
		}

		if( $has_meta ) {
			return $meta;
		}

		return false;

	}
	/**
	 * Admin: Add Columns to orders tab
	 *
	 * @param arr $columns
	 * @return arr
	 */
	public function shop_order_columns( $columns ) {

		if(get_option( 'wc4jp-delivery-date' ) or get_option( 'wc4jp-delivery-time-zone' )){
			$columns['wc4jp_delivery'] = __( 'Delivery', 'woocommerce-for-japan' );
		}

		return $columns;

	}

	/**
	 * Admin: Output date and timeslot columns on orders tab
	 *
	 * @param str $column
	 */
	public function render_shop_order_columns( $column ) {

		global $post, $woocommerce, $the_order;
		if(version_compare( WC_VERSION, '2.7', '>=' )){
			if ( empty( $the_order ) || $the_order->get_id() != $post->ID ) {
				$the_order = wc_get_order( $post->ID );
			}
		}else{
			if ( empty( $the_order ) || $the_order->ID != $post->ID ) {
				$the_order = wc_get_order( $post->ID );
			}
		}

		switch ( $column ) {
			case 'wc4jp_delivery' :

				$this->display_date_and_time_zone( $the_order );

				break;
		}
	}
	/**
	 * Admin: Display date and timeslot on the admin order page
	 *
	 * @param obj $order
	 */
	function display_admin_order_meta( $order ) {

		$this->display_date_and_time_zone( $order );

	}
	/**
	 * Update customer data when create and update
	 *
	 * @param obj $customer_array and $customer
	 */
	public function wc4jp_update_customer_args($customer_array, $customer) {
		$customer_array = array(
			'ID' => $customer->get_id(),
			'display_name' => $customer->get_last_name() . ' ' . $customer->get_first_name(),
		);
		if(isset($customer_array['role'])){
			$customer_array['role'] = $customer->get_role();
		}elseif(isset($customer_array['user_email'])){
			$customer_array['user_email'] = $customer->get_email();
		}
		return $customer_array;
	}
	/**
	 * Add the meta box for shipment info on the order page
	 *
	 * @access public
	 */
	public function add_meta_box(){
		if(get_option( 'wc4jp-delivery-date' ) or get_option( 'wc4jp-delivery-time-zone' )){
			add_meta_box('woocommerce-shipping-date-and-time', __('Shipping Detail', 'woocommerce-for-japan'), array(&$this, 'meta_box'), 'shop_order', 'side', 'high');
		}
	}

	/**
	 * Show the meta box for shipment info on the order page
 	 *
	 * @access public
	 */
	public function meta_box(){
		global $post;
		$shipping_fields = $this->shipping_fields($post);
		echo '<div id="aftership_wrapper">';
		foreach($shipping_fields as $key =>$value){
			if( $value['type'] == 'text' ){
				woocommerce_wp_text_input( $value );
			}
		}
		echo '</div>';
	}
	/**
	* Order Downloads Save
	*
	* Function for processing and storing all order downloads.
	 */
	 public function save_meta_box($post_id, $post){
		$shipping_fields = $this->shipping_fields($post);
		foreach ($shipping_fields as $field) {
			if(isset($_POST[$field['id']]) && $_POST[$field['id']] != 0){
				update_post_meta($post_id, $field['id'], woocommerce_clean($_POST[$field['id']]));
			}
		}
	}
	/**
	 * Show the meta box for shipment info on the order page
 	 *
	 * @access public
	 */
	public function shipping_fields($post){
		$date = get_post_meta( $post->ID, 'wc4jp-delivery-date', true);
		$date_timestamp = strtotime($date);
		$time = get_post_meta( $post->ID, 'wc4jp-delivery-time-zone', true);
		$date_format = get_option( 'wc4jp-date-format', true );
		$shipping_fields = array(
			'wc4jp-delivery-date' => array(
				'type' => 'text',
				'id' => 'wc4jp-delivery-date',
				'label' => __('Delivery Date', 'woocommerce-for-japan'),
				'placeholder' => date($date_format, $date_timestamp),
				'description' => __('Date on which the customer wished delivery.', 'woocommerce-for-japan'),
				'class' => 'wc4jp-delivery-date',
				'value' => ($date) ? $date : ''
			),
			'wc4jp-delivery-time-zone' => array(
				'type' => 'text',
				'id' => 'wc4jp-delivery-time-zone',
				'label' => __('Time Zone', 'woocommerce-for-japan'),
				'description' => __('Time Zone on which the customer wished delivery.', 'woocommerce-for-japan'),
				'class' => 'wc4jp-delivery-time-zone',
				'value' => ($time) ? $time : ''
			),
			'wc4jp_tracking_ship_date' => array(
				'type' => 'text',
				'id' => 'wc4jp_tracking_ship_date',
				'label' => __('Tracking Ship Date', 'woocommerce-for-japan'),
				'placeholder' => date($date_format, $date_timestamp),
				'description' => __('Actually shipped to date', 'woocommerce-for-japan'),
				'class' => 'wc4jp-tracking-ship-date',
				'value' => ($delivery_date = get_post_meta($post->ID, '_' . 'wc4jp_tracking_ship_date', true)) ? date($date_format, $delivery_date) : ''
			),
		);
		return apply_filters( 'wc4jp_shipping_fields', $shipping_fields, $post );
	}
	//Add Ken and Fu to prefectures in Japan.
	public function wc4jp_states( $states ){
		if(get_locale() == 'ja'){
			$states['JP']['JP02'] = __( 'Aomori-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP03'] = __( 'Iwate-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP04'] = __( 'Miyagi-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP05'] = __( 'Akita-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP06'] = __( 'Yamagata-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP07'] = __( 'Fukushima-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP08'] = __( 'Ibaraki-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP09'] = __( 'Tochigi-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP10'] = __( 'Gunma-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP11'] = __( 'Saitama-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP12'] = __( 'Chiba-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP14'] = __( 'Kanagawa-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP15'] = __( 'Niigata-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP16'] = __( 'Toyama-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP17'] = __( 'Ishikawa-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP18'] = __( 'Fukui-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP19'] = __( 'Yamanashi-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP20'] = __( 'Nagano-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP21'] = __( 'Gifu-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP22'] = __( 'Shizuoka-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP23'] = __( 'Aichi-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP24'] = __( 'Mie-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP25'] = __( 'Shiga-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP26'] = __( 'Kyoto-Fu', 'woocommerce-for-japan' );
			$states['JP']['JP27'] = __( 'Osaka-Fu', 'woocommerce-for-japan' );
			$states['JP']['JP28'] = __( 'Hyogo-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP29'] = __( 'Nara-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP30'] = __( 'Wakayama-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP31'] = __( 'Tottori-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP32'] = __( 'Shimane-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP33'] = __( 'Okayama-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP34'] = __( 'Hiroshima-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP35'] = __( 'Yamaguchi-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP36'] = __( 'Tokushima-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP37'] = __( 'Kagawa-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP38'] = __( 'Ehime-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP39'] = __( 'Kochi-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP40'] = __( 'Fukuoka-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP41'] = __( 'Saga-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP42'] = __( 'Nagasaki-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP43'] = __( 'Kumamoto-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP44'] = __( 'Oita-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP45'] = __( 'Miyazaki-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP46'] = __( 'Kagoshima-Ken', 'woocommerce-for-japan' );
			$states['JP']['JP47'] = __( 'Okinawa-Ken', 'woocommerce-for-japan' );
		}

		return $states;

	}
}
