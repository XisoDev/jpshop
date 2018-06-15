<?php

/**
 * Plugin Name: WooCommerce Cash On Delivery Plus Fee
 * Plugin URI: http://www.nigamo.com/woocommerce-fsn/
 * Description: Add fee to the Cash On Delivery payments
 * Version: 1.0.1
 * Author: Nigamo
 * Author URI: http://nigamo.com
 * Requires at least: 3.8
 * Tested up to: 3.9
 *
 * Text Domain: woocommerce
 * Domain Path: /i18n/languages/
 *
 */
function init_codpf_gateway_class() {

    class WC_Gateway_CODPF extends WC_Payment_Gateway {

        /**
         * Constructor for the gateway.
         */
        public function __construct() {
            $this->id = 'codpf';
            $this->icon = apply_filters('woocommerce_cod_icon', '');
            $this->method_title = __('Cash on Delivery Plus Fee', 'woocommerce');
            $this->method_description = __('Have your customers pay with cash (or by other means) upon delivery, including the COD Fee. <span style="float: right; color: #d54e21; font-weight: bold;">Find more themes and plugings at <a href="https://nigamo.com" target="_new">http://nigamo.com</a></span>', 'woocommerce');
            $this->has_fields = false;
            $this->codpf_rate_option = 'codpf_rates';
            $this->codpf_rates = $this->get_codpf_rates();

            // Load the settings
            $this->init_form_fields();
            $this->init_settings();

            // Get settings
            $this->title = $this->get_option('title');
            $this->description = $this->get_option('description');
            $this->instructions = $this->get_option('instructions', $this->description);
            $this->enable_for_methods = $this->get_option('enable_for_methods', array());
            $this->enable_for_virtual = $this->get_option('enable_for_virtual', 'yes') === 'yes' ? true : false;
            $this->codpf_label = $this->get_option('codpf_label');
            $this->default_fee_type = $this->get_option('default_fee_type');
            $this->include_shipping = $this->get_option('include_shipping');

            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
            add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_codpf_rates'));

            // Customer Emails
            add_action('woocommerce_email_before_order_table', array($this, 'email_instructions'), 10, 3);

            wp_enqueue_script('ngm-wc-codpf', plugins_url() . '/woo-cod-plus-fee/assets/js/ngm-wc-codpf.js', array('wc-checkout'), false, true);
        }

        /**
         * Add the fee on checkout
         *
         * @access public
         * @return void
         */
        function codpf_add_surcharge() {
            global $woocommerce;

            $cart_total = $woocommerce->cart->cart_contents_total;
            $cart_total += $this->include_shipping == 'yes' ? $woocommerce->cart->shipping_total : 0;
            $fee_amount = $this->get_copdf_fee($cart_total);

            // Show the fee only if available and more than 0
            if ($fee_amount && $fee_amount > 0) {
                $woocommerce->cart->add_fee($this->codpf_label, $fee_amount, true, 'standard');
            }
        }

        /**
         * Get the fee based on the cart total
         *
         * @access public
         * @param double $cart_total The current amount in the customer's cart
         * @return double
         */
        public function get_copdf_fee($cart_total) {
            foreach ($this->get_codpf_rates() as $rate) {
                if ($cart_total >= $rate['total_from'] && $cart_total < $rate['total_to']) {
                    return $this->calculate_codpf_fee($rate, $cart_total);
                } else if ($cart_total >= $rate['total_from'] && ($rate['total_to'] == '' || $rate['total_to'] == 0)) {
                    return $this->calculate_codpf_fee($rate, $cart_total);
                }
            }

            return false;
        }

        /**
         * Get the fee based on the cart total
         *
         * @access public
         * @param array $rate The information for the gateway rate, that is set for the current cart amount
         * @param double $cart_total The current amount in the customer's cart
         * @return double
         */
        public function calculate_codpf_fee($rate, $cart_total) {
            $fee_type = $rate['fee_type'] != 'default' ? $rate['fee_type'] : $this->default_fee_type;

            if ($fee_type == 'amount') {
                return $rate['fee'];
            } else if ($fee_type == 'percent') {
                return ($cart_total * $rate['fee']) / 100;
            }
        }

        /**
         * Initialise Gateway Settings Form Fields
         */
        public function init_form_fields() {
            $shipping_methods = array();

            if (is_admin()) {
                $shipping_zones = WC_Shipping_Zones::get_zones();
                                
                foreach ((array)$shipping_zones as $zone) {
                    foreach ($zone['shipping_methods'] as $method) {
                        $shipping_methods[$method->instance_id] = $zone['zone_name'] . ': ' . $method->title;
                    }
                }
            }

            $this->form_fields = array(
                'enabled' => array(
                    'title' => __('Enable COD Plus Fee', 'woocommerce'),
                    'label' => __('Enable Cash on Delivery Plus Fee', 'woocommerce'),
                    'type' => 'checkbox',
                    'description' => '',
                    'default' => 'no'
                ),
                'title' => array(
                    'title' => __('Title', 'woocommerce'),
                    'type' => 'text',
                    'description' => __('Payment method description that the customer will see on your checkout.', 'woocommerce'),
                    'default' => __('Cash on Delivery Plus Fee', 'woocommerce'),
                    'desc_tip' => true,
                ),
                'description' => array(
                    'title' => __('Description', 'woocommerce'),
                    'type' => 'textarea',
                    'description' => __('Payment method description that the customer will see on your website.', 'woocommerce'),
                    'default' => __('Pay with cash upon delivery.', 'woocommerce'),
                    'desc_tip' => true,
                ),
                'instructions' => array(
                    'title' => __('Instructions', 'woocommerce'),
                    'type' => 'textarea',
                    'description' => __('Instructions that will be added to the thank you page.', 'woocommerce'),
                    'default' => __('Pay with cash upon delivery.', 'woocommerce'),
                    'desc_tip' => true,
                ),
                'enable_for_methods' => array(
                    'title' => __('Enable for shipping methods', 'woocommerce'),
                    'type' => 'multiselect',
                    'class' => 'chosen_select',
                    'css' => 'width: 450px;',
                    'default' => '',
                    'description' => __('If COD is only available for certain methods, set it up here. Leave blank to enable for all methods.', 'woocommerce'),
                    'options' => $shipping_methods,
                    'desc_tip' => true,
                    'custom_attributes' => array(
                        'data-placeholder' => __('Select shipping methods', 'woocommerce')
                    )
                ),
                'enable_for_virtual' => array(
                    'title' => __('Enable for virtual orders', 'woocommerce'),
                    'label' => __('Enable COD if the order is virtual', 'woocommerce'),
                    'type' => 'checkbox',
                    'default' => 'yes'
                ),
                'codpf_label' => array(
                    'title' => __('Label', 'woocommerce'),
                    'type' => 'text',
                    'description' => __('Label for the Fee in the Order Totals.', 'woocommerce'),
                    'default' => __('Cash on Delivery Fee', 'woocommerce'),
                    'desc_tip' => true,
                ),
                'default_fee_type' => array(
                    'title' => __('Default Fee Type', 'woocommerce'),
                    'type' => 'select',
                    'description' => __('Default fee type, which is used if none is specified in the rate table.', 'woocommerce'),
                    'desc_tip' => true,
                    'options' => array(
                        'amount' => __('Amount', 'woocommerce'),
                        'percent' => __('Percent', 'woocommerce')
                    )
                ),
                'include_shipping' => array(
                    'title' => __('Include Shipping Fee', 'woocommerce'),
                    'label' => __('Include the shipping fee in the Cart Total used for calculations of the COD fee', 'woocommerce'),
                    'type' => 'checkbox',
                    'default' => 'no'
                ),
                'codpf_rates' => array(
                    'title' => __('COD Plus Fee rates', 'woocommerce'),
                    'type' => 'title'
                ),
                'codpf_rates_table' => array(
                    'type' => 'codpf_rates_table'
                )
            );
        }

        /**
         * Generate_additional_costs_html function.
         *
         * @access public
         * @return void
         */
        function generate_codpf_rates_table_html() {
            global $woocommerce;
            ob_start();
            ?>
            <tr valign="top">
                <th scope="row" class="titledesc"><?php _e('Costs', 'woocommerce'); ?>:</th>
                <td class="forminp" id="<?php echo $this->id; ?>_codpf_rates">
                    <table class="shippingrows widefat" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="check-column"><input type="checkbox"></th>
                                <th><?php _e('Total from', 'woocommerce'); ?></th>
                                <th><?php _e('Total to', 'woocommerce'); ?></th>
                                <th><?php _e('Fee', 'woocommerce'); ?></th>
                                <th><?php _e('Fee Type', 'woocommerce'); ?></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th colspan="5"><a href="#" class="add button"><?php _e('+ Add row', 'woocommerce'); ?></a> <a href="#" class="remove button"><?php _e('Delete selected rows', 'woocommerce'); ?></a></th>
                            </tr>
                        </tfoot>
                        <tbody class="codpf_rates">
                            <?php
                            $i = -1;
                            if ($this->codpf_rates) {
                                foreach ($this->codpf_rates as $codpf_rate) {
                                    $i++;
                                    $amount = ($codpf_rate['fee_type'] == 'amount') ? 'selected="selected"' : '';
                                    $percent = ($codpf_rate['fee_type'] == 'percent') ? 'selected="selected"' : '';
                                    echo '<tr class="codpf_rate">
                                            <th class="check-column"><input type="checkbox" name="select" /></th>
                                            <td><input type="number" step="any" min="0" value="' . esc_attr($codpf_rate['total_from']) . '" name="' . esc_attr($this->id . '_rates[' . $i . '][total_from]') . '" placeholder="' . __('0.00', 'woocommerce') . '" size="4" /></td>
                		            <td><input type="number" step="any" min="0" value="' . esc_attr($codpf_rate['total_to']) . '" name="' . esc_attr($this->id . '_rates[' . $i . '][total_to]') . '" placeholder="' . __('0.00', 'woocommerce') . '" size="4" /></td>
				            <td><input type="text" value="' . esc_attr($codpf_rate['fee']) . '" name="' . esc_attr($this->id . '_rates[' . $i . '][fee]') . '" placeholder="' . __('0.00', 'woocommerce') . '" size="4" /></td>
				            <td><select name="' . esc_attr($this->id . '_rates[' . $i . '][fee_type]') . '"><option value="default">Default</option><option value="amount"' . $amount . '>Amount</option><option value="percent"' . $percent . '>Percent</option></select></td>
			                  </tr>';
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                    <script type="text/javascript">
                        jQuery(function() {

                        jQuery('#<?php echo $this->id; ?>_codpf_rates').on('click', 'a.add', function() {

                        var size = jQuery('#<?php echo $this->id; ?>_codpf_rates tbody .codpf_rate').size();
                        jQuery('<tr class="codpf_rate">\
                                                            <th class="check-column"><input type="checkbox" name="select" /></th>\
                                        <td><input type="number" step="any" min="0" name="<?php echo $this->id; ?>_rates[' + size + '][total_from]" placeholder="0.00" size="4" /></td>\
                                        <td><input type="number" step="any" min="0" name="<?php echo $this->id; ?>_rates[' + size + '][total_to]" placeholder="0.00" size="4" /></td>\
                                        <td><input type="text" name="<?php echo $this->id; ?>_rates[' + size + '][fee]" placeholder="0.00" size="4" /></td>\
                                        <td><select name="<?php echo $this->id; ?>_rates[' + size + '][fee_type]"><option value="default">Default</option><option value="amount">Amount</option><option value="percent">Percent</option></select></td>\
                                </tr>').appendTo('#<?php echo $this->id; ?>_codpf_rates table tbody');
                        return false;
                        });
                        // Remove row
                        jQuery('#<?php echo $this->id; ?>_codpf_rates').on('click', 'a.remove', function() {
                        var answer = confirm("<?php _e('Delete the selected rates?', 'woocommerce'); ?>")
                                if (answer) {
                        jQuery('#<?php echo $this->id; ?>_codpf_rates table tbody tr th.check-column input:checked').each(function(i, el) {
                        jQuery(el).closest('tr').remove();
                        });
                        }
                        return false;
                        });
                        });
                    </script>
                </td>
            </tr>
            <?php
            return ob_get_clean();
        }

        /**
         * Process_codpf_rates function.
         *
         * @access public
         * @return void
         */
        function process_codpf_rates() {
            // Save the rates

            $codpf_rates_records = array();
            $codpf_rates = array();

            if (!empty($_POST[$this->codpf_rate_option])) {
                foreach ($_POST[$this->codpf_rate_option] as $codpf_rate) {
                    $total_from = !empty($codpf_rate['total_from']) ? $codpf_rate['total_from'] : 0;
                    $codpf_rates[] = array(
                        'total_from' => number_format((double) $total_from, 2, '.', ''),
                        'total_to' => number_format((double) $codpf_rate['total_to'], 2, '.', ''),
                        'fee' => number_format((double) $codpf_rate['fee'], 2, '.', ''),
                        'fee_type' => esc_attr($codpf_rate['fee_type'])
                    );
                }
            }

            update_option($this->codpf_rate_option, $codpf_rates);

            $this->get_codpf_rates();
        }

        /**
         * Get COD Fee function.
         *
         * @access public
         * @return void
         */
        function get_codpf_rates() {

            return array_filter((array) get_option($this->codpf_rate_option));
        }

        /**
         * Check If The Gateway Is Available For Use
         *
         * @return bool
         */
        public function is_available() {
            $order = null;

            if (!$this->enable_for_virtual) {
                if (WC()->cart && !WC()->cart->needs_shipping()) {
                    return false;
                }

                if (is_page(wc_get_page_id('checkout')) && 0 < get_query_var('order-pay')) {
                    $order_id = absint(get_query_var('order-pay'));
                    $order = wc_get_order($order_id);

                    // Test if order needs shipping.
                    $needs_shipping = false;

                    if (0 < sizeof($order->get_items())) {
                        foreach ($order->get_items() as $item) {
                            $_product = $order->get_product_from_item($item);

                            if ($_product->needs_shipping()) {
                                $needs_shipping = true;
                                break;
                            }
                        }
                    }

                    $needs_shipping = apply_filters('woocommerce_cart_needs_shipping', $needs_shipping);

                    if ($needs_shipping) {
                        return false;
                    }
                }
            }

            if (!empty($this->enable_for_methods)) {

                // Only apply if all packages are being shipped via local pickup
                $chosen_shipping_methods_session = WC()->session->get('chosen_shipping_methods');

                if (isset($chosen_shipping_methods_session)) {
                    $chosen_shipping_methods = array_unique($chosen_shipping_methods_session);
                } else {
                    $chosen_shipping_methods = array();
                }

                $check_method = false;

                if (is_object($order)) {
                    if ($order->shipping_method) {
                        $check_method = $order->shipping_method;
                    }
                } elseif (empty($chosen_shipping_methods) || sizeof($chosen_shipping_methods) > 1) {
                    $check_method = false;
                } elseif (sizeof($chosen_shipping_methods) == 1) {
                    $check_method = explode(':',$chosen_shipping_methods[0]);
                }

                if (!$check_method) {
                    return false;
                }

                $found = false;
                
                if (in_array($check_method[1], $this->enable_for_methods)) {
                    $found = true;
                }

                if (!$found) {
                    return false;
                }
            }

            return parent::is_available();
        }

        /**
         * Process the payment and return the result
         *
         * @param int $order_id
         * @return array
         */
        public function process_payment($order_id) {

            $order = wc_get_order($order_id);

            // Mark as processing (payment won't be taken until delivery)
            $order->update_status('processing', __('Payment to be made upon delivery.', 'woocommerce'));

            // Reduce stock levels
            $order->reduce_order_stock();

            // Remove cart
            WC()->cart->empty_cart();

            // Return thankyou redirect
            return array(
                'result' => 'success',
                'redirect' => $this->get_return_url($order)
            );
        }

        /**
         * Output for the order received page.
         */
        public function thankyou_page() {
            if ($this->instructions) {
                echo wpautop(wptexturize($this->instructions));
            }
        }

        /**
         * Add content to the WC emails.
         *
         * @access public
         * @param WC_Order $order
         * @param bool $sent_to_admin
         * @param bool $plain_text
         */
        public function email_instructions($order, $sent_to_admin, $plain_text = false) {
            if ($this->instructions && !$sent_to_admin && 'cod' === $order->payment_method) {
                echo wpautop(wptexturize($this->instructions)) . PHP_EOL;
            }
        }

    }

}

function add_codpf_gateway_class($methods) {
    $methods[] = 'WC_Gateway_CODPF';
    return $methods;
}

add_filter('woocommerce_payment_gateways', 'add_codpf_gateway_class');
add_action('plugins_loaded', 'init_codpf_gateway_class');
add_action('woocommerce_cart_calculate_fees', 'ngm_codpf_fee', 20, 20);

function ngm_codpf_fee() {
    global $post;

    if (is_admin() && !defined('DOING_AJAX'))
        return;

    // Exclude the tax from the Cart preview as it is not yet clear if the customer will select this payment gateway
    if (WC()->session->chosen_payment_method === 'codpf' && !has_shortcode($post->post_content, 'woocommerce_cart')) {
        $codpf = new WC_Gateway_CODPF();
        $codpf->codpf_add_surcharge();
    }
}
