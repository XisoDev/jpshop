<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Zeus Payment Gateway
 *
 * Provides a Zeus Credit Card Payment Gateway.
 *
 * @class 		WC_Zeus
 * @extends		WC_Gateway_Zeus_CC
 * @version		0.9.0
 * @package		WooCommerce/Classes/Payment
 * @author		Artisan Workshop
 */
class WC_Gateway_Zeus_CC extends WC_Payment_Gateway {


	/**
	 * Constructor for the gateway.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
//		include_once( 'includes/class-wc-gateway-zeus-request.php' );
//		include_once( 'includes/class-wc-zeus-error-message.php' );

		$this->id                = 'zeus_cc';
		$this->has_fields        = false;
		$this->order_button_text = __( 'Proceed to Zeus Credit Card', 'woo-zeus' );
		$this->method_title      = __( 'Zeus Credit Card', 'woo-zeus' );
		
        // Create plugin fields and settings
		$this->init_form_fields();
		$this->init_settings();
		$this->method_title       = __( 'Zeus Credit Card Payment Gateway', 'woo-zeus' );
		$this->method_description = __( 'Allows payments by Zeus Credit Card in Japan.', 'woo-zeus' );

		// Get setting values
		foreach ( $this->settings as $key => $val ) $this->$key = $val;

		// Load plugin checkout credit Card icon
		if($this->setting_card_vm =='yes' and $this->setting_card_d =='yes' and $this->setting_card_aj =='yes'){
		$this->icon = plugins_url( 'assets/images/zeus-cards.png' , __FILE__ );
		}elseif($this->setting_card_vm =='yes' and $this->setting_card_d =='no' and $this->setting_card_aj =='no'){
		$this->icon = plugins_url( 'assets/images/zeus-cards-v-m.png' , __FILE__ );
		}elseif($this->setting_card_vm =='yes' and $this->setting_card_d =='yes' and $this->setting_card_aj =='no'){
		$this->icon = plugins_url( 'assets/images/zeus-cards-v-m-d.png' , __FILE__ );
		}elseif($this->setting_card_vm =='yes' and $this->setting_card_d =='no' and $this->setting_card_aj =='yes'){
		$this->icon = plugins_url( 'assets/images/zeus-cards-v-m-a-j.png' , __FILE__ );
		}elseif($this->setting_card_vm =='no' and $this->setting_card_d =='no' and $this->setting_card_aj =='yes'){
		$this->icon = plugins_url( 'assets/images/zeus-cards-a-j.png' , __FILE__ );
		}elseif($this->setting_card_vm =='no' and $this->setting_card_d =='yes' and $this->setting_card_aj =='no'){
		$this->icon = plugins_url( 'assets/images/zeus-cards-d.png' , __FILE__ );
		}elseif($this->setting_card_vm =='no' and $this->setting_card_d =='yes' and $this->setting_card_aj =='yes'){
		$this->icon = plugins_url( 'assets/images/zeus-cards-d-a-j.png' , __FILE__ );
		}

		// Actions
		add_action( 'woocommerce_receipt_zeus_cc',                              array( $this, 'receipt_page' ) );
		add_action( 'woocommerce_update_options_payment_gateways',              array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
	}

      /**
       * Initialize Gateway Settings Form Fields.
       */
	    function init_form_fields() {

	      $this->form_fields = array(
	      'enabled'     => array(
	        'title'       => __( 'Enable/Disable', 'woo-zeus' ),
	        'label'       => __( 'Enable Zeus Credit Card Payment', 'woo-zeus' ),
	        'type'        => 'checkbox',
	        'description' => '',
	        'default'     => 'no'
	        ),
	      'title'       => array(
	        'title'       => __( 'Title', 'woo-zeus' ),
	        'type'        => 'text',
	        'description' => __( 'This controls the title which the user sees during checkout.', 'woo-zeus' ),
	        'default'     => __( 'Credit Card (Zeus)', 'woo-zeus' )
	        ),
	      'description' => array(
	        'title'       => __( 'Description', 'woo-zeus' ),
	        'type'        => 'textarea',
	        'description' => __( 'This controls the description which the user sees during checkout.', 'woo-zeus' ),
	        'default'     => __( 'Pay with your credit card via Zeus.', 'woo-zeus' )
	        ),
			'authentication_clientip' => array(
				'title'       => __( 'Authentication Client IP', 'woo-zeus' ),
				'type'        => 'text',
				'description' => sprintf( __( 'Enter Authentication Client IP.', 'woo-zeus' )),
			),
			'authentication_key' => array(
				'title'       => __( 'Authentication Key', 'woo-zeus' ),
				'type'        => 'text',
				'description' => sprintf( __( 'Enter Authentication Key.', 'woo-zeus' )),
			),
			'setting_card_vm' => array(
				'title'       => __( 'Set Credit Card', 'woo-zeus' ),
				'id'              => 'wc-zeus-cc-vm',
				'type'        => 'checkbox',
				'label'       => __( 'VISA & MASTER', 'woo-zeus' ),
				'default'     => 'yes',
			),
			'setting_card_d' => array(
				'id'              => 'wc-zeus-cc-d',
				'type'        => 'checkbox',
				'label'       => __( 'DINNERS', 'woo-zeus' ),
				'default'     => 'yes',
			),
			'setting_card_aj' => array(
				'id'              => 'wc-zeus-cc-aj',
				'type'        => 'checkbox',
				'label'       => __( 'AMEX & JCB', 'woo-zeus' ),
				'default'     => 'yes',
				'description' => sprintf( __( 'Please check them you are able to use Credit Card', 'woo-zeus' )),
			),
			'payment_installments' => array(
				'title'       => __( 'Payment in installments', 'woo-zeus' ),
				'id'          => 'wc-zeus-payment-installments',
				'type'        => 'checkbox',
				'label'       => __( 'Enable Payment in installments', 'woo-zeus' ),
				'default'     => 'no',
			),
			'payment_counts' => array(
				'title'       => __( 'Payment counts', 'woo-zeus' ),
				'id'          => 'wc-zeus-payment-counts',
				'type'        => 'multiselect',
				'label'       => __( 'Enable Payment in installments', 'woo-zeus' ),
				'options'     => array(
					'03' => __('3 times', 'woocommerce' ),
					'05' => __('5 times', 'woocommerce' ),
					'06' => __('6 times', 'woocommerce' ),
					'10' => __('10 times', 'woocommerce' ),
					'12' => __('12 times', 'woocommerce' ),
					'15' => __('15 times', 'woocommerce' ),
					'18' => __('18 times', 'woocommerce' ),
					'20' => __('20 times', 'woocommerce' ),
					'24' => __('24 times', 'woocommerce' ),
				)
			),
			'revolving_repayment' => array(
				'title'       => __( 'Revolving repayment', 'woo-zeus' ),
				'id'          => 'wc-zeus-revolving-repayment',
				'type'        => 'checkbox',
				'label'       => __( 'Enable Revolving repayment', 'woo-zeus' ),
				'default'     => 'no',
			),
			'twice_payment' => array(
				'title'       => __( 'Twice payment', 'woo-zeus' ),
				'id'          => 'wc-zeus-twice-payment',
				'type'        => 'checkbox',
				'label'       => __( 'Enable Twice payment', 'woo-zeus' ),
				'default'     => 'no',
			),
			'bonus_pay' => array(
				'title'       => __( 'Bonus pay', 'woo-zeus' ),
				'id'          => 'wc-zeus-bonus-pay',
				'type'        => 'checkbox',
				'label'       => __( 'Enable Bonus pay', 'woo-zeus' ),
				'default'     => 'no',
				'description' => __( 'Split twice, bonus payments are it separately as an option. If the use of any of your choice, Please contact Zeus support.', 'woo-zeus' ),

			),
		);
		}


      /**
       * UI - Admin Panel Options
       */
		function admin_options() { ?>
			<h3><?php _e( 'Zeus Credit Card','woo-zeus' ); ?></h3>
		    <table class="form-table">
				<?php $this->generate_settings_html(); ?>
			</table>
		<?php }

      /**
       * UI - Payment page fields for Zeus Payment.
       */
		function payment_fields() {
		// Description of payment method from settings
			if ( $this->description ) {
            		echo $this->description;
			}
        }

	/**
	 * Process the payment and return the result.
	 */
	function process_payment( $order_id ) {

        global $woocommerce;
        global $wpdb;

        $order = new WC_Order( $order_id );

        $connect_url = WC_ZEUS_CC_URL;
        $post_data = array();
        $post_data['clientip'] = $this->authentication_clientip;
        $post_data['money'] = $order->order_total;
        $post_data['telno'] = str_replace('-','',$order->billing_phone);
        $post_data['email'] = $order->billing_email;

        $site_code = getSiteOrderCode();
        $post_data['sendid'] = $site_code["order_code"] . $order->get_id();
        $post_data['sendpoint'] = $site_code["order_code"] . $order->get_id();
        $post_data['success_url'] = $this->get_return_url( $order );
        $post_data['success_str'] = mb_convert_encoding("決済完了", "SJIS", "auto");
        $post_data['failure_url'] = esc_url( home_url( '/' ) );
        $post_data['failure_str'] = mb_convert_encoding("サイトに戻る", "SJIS", "auto");

        //Note for Message
        $order->update_status( 'pending', __( 'Proceed to Zeus Credit Card', 'woo-zeus' ) );

        // Reduce stock levels
        wc_reduce_stock_levels($order->get_id());

        // Remove cart
        WC()->cart->empty_cart();
        return array(
            'result'   => 'success',
            'redirect' => $this->get_zeus_url( $post_data ,$connect_url)
        );

	}

    private function get_zeus_url( $post_data , $connect_url) {
        $url = $connect_url;
        $url .= '?'.http_build_query($post_data);
        return $url;
    }


    /**
     * Check if the user has any billing records in the Customer Vault
     */
	function user_has_stored_data( $user_id ) {
		$tokens = WC_Payment_Tokens::get_customer_tokens( get_current_user_id(), $this->id );
		return $tokens;
	}

	/**
	* Notice Error in Request Invalid
	*/
	function error_notice($response, $order, $req){
		if($response->result->status == 'invalid'){
			$this->notice_invalid($response->result->code, $order, $req);
			return ;
		}elseif($response->result->status == 'failure'){
			$this->notice_failure($response->result->code, $order, $req);
			return ;
		}elseif($response->result->status == 'meintenance'){
			$this->notice_meintenance($response->result->code, $order, $req);
			return ;
		}
	}

	/**
	* Notice Error in Request Invalid
	*/
	function notice_invalid($code, $order, $req){
		$error_messages = new ErrorHandler();
		if(is_checkout()){
			wc_add_notice( $error_messages->getMessage( $code ).__( ' Invalid error code is' , 'woo-zeus' ).$code, $notice_type = 'error' );
		}
		$order->add_order_note($error_messages->getMessage( $code ).__( ' Invalid Error Code:' , 'woo-zeus' ).$code.'in '.$req);
	}

	/**
	* Notice Error in Request Failure
	*/
	function notice_failure($code, $order, $req) {
		$error_messages = new ErrorHandler();
		if(is_checkout()){
			wc_add_notice( $error_messages->getMessage( $code ).__( ' Failure error code is' , 'woo-zeus' ).$code, $notice_type = 'error' );
		}
		$order->add_order_note($error_messages->getMessage( $code ).__( ' Failure Error Code:' , 'woo-zeus' ).$code.'in '.$req);
	}
	/**
	* Notice Error in Request Meintenance
	*/
	function notice_meintenance($code, $order, $req) {
		$error_messages = new ErrorHandler();
		if(is_checkout()){
			wc_add_notice( $error_messages->getMessage( $code ).__( ' Mentenance error code is' , 'woo-zeus' ).$code, $notice_type = 'error' );
		}
		$order->add_order_note($error_messages->getMessage( $code ).__( ' Mentenance Error Code:' , 'woo-zeus' ).$code.'in '.$req);
	}

	function receipt_page( $order ) {
		echo '<p>' . __( 'Thank you for your order.', 'woo-zeus' ) . '</p>';
	}

	function zeus_payment_completed( $order_id ,$old_status , $new_status) {
		include_once( 'includes/class-wc-gateway-zeus-request.php' );
		global $woocommerce;
		$order = new WC_Order( $order_id );
		if($new_status == 'completed' || $old_status == 'processing'){
			$connect_url = WC_ZEUS_SECURE_API_URL;
			$post_data['clientip'] = $this->authentication_clientip;
			$post_data['king'] = $order->order_total;
			$post_data['date'] = date('Ymd');
			$post_data['ordd'] = get_post_meta( $order_id, '_transaction_id', true );
			$post_data['autype'] = 'sale';
			$zeus_request = new WC_Gateway_Zeus_Request();
//			$order->add_order_note('00');
			
			$zeus_response = $zeus_request->send_zeus_complete_request( $post_data, $connect_url );
			if($zeus_response == 'Success_order'){
				$order->add_order_note(__( 'Zeus Auto finished to sale.' , 'woo-zeus' ));
			}else{
				$order->add_order_note(__( 'Fail to authority for sale, please update sale in Zeus Admin site.' , 'woo-zeus' ));
			}
		}
	}

    /**
     * Include jQuery and our scripts
     */
/*    function add_zeus_cc_scripts() {

//      if ( ! $this->user_has_stored_data( wp_get_current_user()->ID ) ) return;

      wp_enqueue_script( 'jquery' );
      wp_enqueue_script( 'edit_billing_details', plugin_dir_path( __FILE__ ) . 'js/edit_billing_details.js', array( 'jquery' ), 1.0 );

      if ( $this->security_check == 'yes' ) wp_enqueue_script( 'check_cvv', plugin_dir_path( __FILE__ ) . 'js/check_cvv.js', array( 'jquery' ), 1.0 );

    }
*/
		/**
		 * Get post data if set
		 */
		private function get_post( $name ) {
			if ( isset( $_POST[ $name ] ) ) {
				return $_POST[ $name ];
			}
			return null;
		}

		/**
     * Check whether an order is a subscription
     */
		private function is_subscription( $order ) {
      return class_exists( 'WC_Subscriptions_Order' ) && WC_Subscriptions_Order::order_contains_subscription( $order );
		}

}
/**
 * Add the gateway to woocommerce
 */
function add_wc_zeus_cc_gateway( $methods ) {
	$methods[] = 'WC_Gateway_Zeus_CC';
	return $methods;
}

add_filter( 'woocommerce_payment_gateways', 'add_wc_zeus_cc_gateway' );

/**
 * Edit the available gateway to woocommerce
 */
function edit_available_gateways( $methods ) {
	if ( isset($currency) ) {
	}else{
	$currency = get_woocommerce_currency();
	}
	if($currency !='JPY'){
	unset($methods['zeus_cc']);
	}
	return $methods;
}

add_filter( 'woocommerce_available_payment_gateways', 'edit_available_gateways' );
