<?php
/**
 * Plugin Name: WooCommerce For Japan
 * Framework Version : 1.0.3
 * Author: Artisan Workshop
 * Author URI: https://wc.artws.info/
 *
 * @category View
 * @author Artisan Workshop
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if (!class_exists('WC4JP_Admin_Setting')):
class WC4JP_Admin_Setting {

	/**
	 * create checkbox input form.
	 * 
	 * @return mixed
	 */
	public function wc4jp_input_checkbox($slug, $descritpion = null, $prefix, $array_name = null){
		?>
		<?php if(isset($descritpion))echo '<label for="woocommerce_input_'.$slug.'">';
			$wc4jp_options_setting = null;
			$wc4jp_meta_name = $prefix.$slug;
			if(get_option($wc4jp_meta_name)){
				$wc4jp_options_setting = get_option($wc4jp_meta_name);
			}elseif(get_option($array_name)){
				$setting = get_option($array_name);
				$wc4jp_options_setting = $setting[$slug];
			}
			?>
			<input type="checkbox" name="<?php echo $slug;?>" value="1" <?php checked( $wc4jp_options_setting, 1 ); ?>>
			<?php if(isset($descritpion))echo $descritpion;
		if(isset($descritpion))echo '</label>';
	}
	/**
	 * create input text form.
	 * 
	 * @return mixed
	 */
	 public function wc4jp_input_text($slug, $descritpion, $num, $default_value = null, $prefix, $array_name = null){
		 ?>
		<label for="woocommerce_input_<?php echo $slug;?>">
		<?php 
			$wc4jp_meta_name = $prefix.$slug;
			if(get_option($wc4jp_meta_name)){
				$wc4jp_options_setting = get_option($wc4jp_meta_name) ;
			}elseif(get_option($array_name)){
				$setting = get_option($array_name);
				$wc4jp_options_setting = $setting[$slug];
			}else{
				$wc4jp_options_setting = $default_value ;
			}
			?>
			<input type="text" name="<?php echo $slug;?>"  size="<?php echo $num;?>" value="<?php echo $wc4jp_options_setting; ?>" ><br />
			<?php echo $descritpion; ?>
		</label>
		<?php
	}
	/**
	 * create input number form.
	 * 
	 * @return mixed
	 */
	 public function wc4jp_input_number($slug, $descritpion, $default_value, $prefix, $array_name = null){
		 ?>
		<label for="woocommerce_input_<?php echo $slug;?>">
		<?php 
			$wc4jp_meta_name = $prefix.$slug;
			if(get_option($wc4jp_meta_name)){
				$wc4jp_options_setting = get_option($wc4jp_meta_name);
			}elseif(get_option($array_name)){
				$setting = get_option($array_name);
				$wc4jp_options_setting = $setting[$slug];
			}else{
				$wc4jp_options_setting = $default_value;
			}
			?>
			<input type="number" name="<?php echo $slug;?>" value="<?php echo $wc4jp_options_setting; ?>" ><br />
			<?php echo $descritpion; ?>
		</label>
		<?php
	}
	/**
	 * create input select form.
	 * 
	 * @return mixed
	 */
	 public function wc4jp_input_select($slug, $descritpion, $select_options, $prefix, $array_name = null){
		 ?>
		<label for="woocommerce_input_<?php echo $slug;?>">
		<?php 
			$wc4jp_meta_name = $prefix.$slug;
			$wc4jp_options_setting = '';
			if(get_option($wc4jp_meta_name)){
				$wc4jp_options_setting = get_option($wc4jp_meta_name);
			}elseif(get_option($array_name)){
				$setting = get_option($array_name);
				$wc4jp_options_setting = $setting[$slug];
			}
			echo '<select name="'.$slug.'">';
			foreach($select_options as $key => $value){
				$checked = '';
				if($wc4jp_options_setting == $key){
					$checked = ' selected="selected"';
				}
				echo '<option value="'.$key.'"'.$checked.'>'.$value.'</option>';
			}
			echo '</select><br />';
			echo $descritpion; ?>
			</select>
		</label>
		<?php
	}
	/**
	 * create input time.
	 * 
	 * @return mixed
	 */
	 public function wc4jp_input_time($slug, $descritpion, $default_value, $prefix){
	    ?>
		<label for="woocommerce_input_<?php echo $slug;?>">
		<?php 
			$meta_name = $prefix.$slug;
			if(get_option($meta_name)){
				$options_setting = get_option($meta_name) ;
			}else{
				$options_setting = $default_value;
			}
			?>
			<input type="time" name="<?php echo $slug;?>" value="<?php echo $options_setting; ?>" ><br />
			<?php echo $descritpion; ?>
		</label>
	<?php }
	/**
	 * create date time.
	 * 
	 * @return mixed
	 */
	 public function wc4jp_input_date_term($start_date, $end_date, $descritpion, $prefix){
	    ?>
		<label for="woocommerce_input_date_term">
		<?php 
			$start_meta_name = $prefix.$start_date['name'];
			$start_date_value = '';
			if(get_option($start_meta_name)){
				$start_date_value = get_option($start_meta_name) ;
			}
			$end_date_value = '';
			$end_meta_name = $prefix.$end_date['name'];
			if(get_option($end_meta_name)){
				$end_date_value = get_option($end_meta_name) ;
			}
			?>
			<?php echo $start_date['label']; ?><input id="<?php echo $start_date['id'];?>" name="<?php echo $start_date['name'];?>" type="date" value="<?php echo $start_date_value; ?>" > - <?php echo $end_date['label']; ?><input id="<?php echo $end_date['id'];?>" name="<?php echo $end_date['name'];?>" type="date" value="<?php echo $end_date_value; ?>" ><br />
			<?php echo $descritpion; ?>
		</label>
	<?php }
	/**
	 * create description for check pattern.
	 * 
	 * @return mixed
	 */
	public function wc4jp_description_check_pattern($title){
		$descritpion = sprintf(__( 'Please check it if you want to use %s.', 'woocommerce-for-japan' ), $title);
		return $descritpion;
	}
	/**
	 * create description for payment pattern.
	 * 
	 * @return mixed
	 */
	public function wc4jp_description_payment_pattern($title){
		$descritpion = sprintf(__( 'Please check it if you want to use the payment method of %s.', 'woocommerce-for-japan' ), $title);
		return $descritpion;
	}
	/**
	 * create description for input pattern.
	 * 
	 * @return mixed
	 */
	public function wc4jp_description_input_pattern($title){
		$descritpion = sprintf(__( 'Please input %s.', 'woocommerce-for-japan' ), $title);
		return $descritpion;
	}
	/**
	 * create description for input pattern.
	 * 
	 * @return mixed
	 */
	public function wc4jp_description_select_pattern($title){
		$descritpion = sprintf(__( 'Please select one from these as %s.', 'woocommerce-for-japan' ), $title);
		return $descritpion;
	}
	/**
	 * Sidebar Support notice html
	 * 
	 * @return mixed
	 */
	public function wc4jp_support_notice($support_form_url){?>
		<h4 class="inner"><?php echo __( 'Need support?', 'woocommerce-for-japan' );?></h4>
		<p class="inner"><?php echo sprintf(__( 'If you are having problems with this plugin, talk about them in the <a href="%s" target="_blank" title="Support forum">Support forum</a>.', 'woocommerce-for-japan' ),$support_form_url.'?utm_source=wc4jp-settings&utm_medium=link&utm_campaign=top-support');?></p>
		<p class="inner"><?php echo sprintf(__( 'If you need professional support, please consider about <a href="%1$s" target="_blank" title="Site Construction Support service">Site Construction Support service</a> or <a href="%2$s" target="_blank" title="Maintenance Support service">Maintenance Support service</a>.', 'woocommerce-for-japan' ),'https://wc.artws.info/product-category/setting-support/?utm_source=wc4jp-settings&utm_medium=link&utm_campaign=setting-support','https://wc.artws.info/product-category/maintenance-support/?utm_source=wc4jp-settings&utm_medium=link&utm_campaign=maintenance-support');?></p>
     <?php
	}
	/**
	 * Sidebar Update notice html
	 * 
	 * @return mixed
	 */
	public function wc4jp_update_notice(){?>
		<h4 class="inner"><?php echo __( 'Finished Latest Update, WordPress and WooCommerce?', 'woocommerce-for-japan' );?></h4>
		<p class="inner"><?php echo sprintf(__( 'One the security, latest update is the most important thing. If you need site maintenance support, please consider about <a href="%s" target="_blank" title="Support forum">Site Maintenance Support service</a>.', 'woocommerce-for-japan' ),'https://wc.artws.info/shop/maintenance-support/woocommerce-professional-support-subscription/?utm_source=wc4jp-settings&utm_medium=link&utm_campaign=maintenance-support');?>
		</p>
     <?php
	}
	/**
	 * Sidebar Community information html
	 * 
	 * @return mixed
	 */
	public function wc4jp_community_info(){?>
		<h4 class="inner"><?php echo __( 'Where is the study group of Woocommerce in Japan?', 'woocommerce-for-japan' );?></h4>
		<p class="inner"><?php echo sprintf(__( '<a href="%s" target="_blank" title="Tokyo WooCommerce Meetup">Tokyo WooCommerce Meetup</a>.', 'woocommerce-for-japan' ),'http://meetup.com/ja-JP/Tokyo-WooCommerce-Meetup/?');?><br />
		<?php echo sprintf(__( '<a href="%s" target="_blank" title="Kansai WooCommerce Meetup">Kansai WooCommerce Meetup</a>.', 'woocommerce-for-japan' ),'http://meetup.com/ja-JP/Kansai-WooCommerce-Meetup/');?><br />
		<?php echo __('Join Us!', 'woocommerce-for-japan' );?>
		</p>
     <?php
	}
	/**
	 * Sidebar Footer Author infomation html
	 * 
	 * @return mixed
	 */
	public function wc4jp_author_info(){?>
					<p class="wc4jp-link inner"><?php echo __( 'Created by', 'woocommerce-for-japan' );?> <a href="https://wc.artws.info/?utm_source=wc4jp-settings&utm_medium=link&utm_campaign=created-by" target="_blank" title="Artisan Workshop"><img src="https://wc.artws.info/wp-content/uploads/2016/08/woo-logo.png" title="Artsain Workshop" alt="Artsain Workshop" class="wc4jp-logo" /></a><br />
					<a href="https://docs.artws.info/?utm_source=wc4jp-settings&utm_medium=link&utm_campaign=created-by" target="_blank"><?php echo __( 'WooCommerce Doc in Japanese', 'woocommerce-for-japan' );?></a>
					</p>
     <?php
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
			echo '<button type="button" class="handlediv button-link" aria-expanded="true"><span class="screen-reader-text">' . __('Toggle panel', 'woocommerce-for-paygent-payment-main') . '</span><span class="toggle-indicator" aria-hidden="true"></span></button>';
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
}
endif;