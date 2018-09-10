<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

// Check stock status.
$out_of_stock = get_post_meta( $post->ID, '_stock_status', true ) == 'outofstock';

// Extra post classes.variations_form cart
$classes   = array();
$classes[] = 'product-small';
$classes[] = 'col';
$classes[] = 'has-hover';

if ( $out_of_stock ) $classes[] = 'out-of-stock';

?>

<div <?php fl_woocommerce_version_check( '3.4.0' ) ? wc_product_class( $classes ) : post_class( $classes ); ?>>
	<div class="col-inner">
	<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>
	<div class="product-small box <?php echo flatsome_product_box_class(); ?>">
		<div class="box-image">
			<div class="<?php echo flatsome_product_box_image_class(); ?>">
				<a href="<?php echo get_the_permalink(); ?>">
					<?php
						/**
						 *
						 * @hooked woocommerce_get_alt_product_thumbnail - 11
						 * @hooked woocommerce_template_loop_product_thumbnail - 10
						 */
						do_action( 'flatsome_woocommerce_shop_loop_images' );
					?>
				</a>
			</div>
			<div class="image-tools is-small top right show-on-hover">
				<?php do_action( 'flatsome_product_box_tools_top' ); ?>
			</div>
			<div class="image-tools is-small hide-for-small bottom left show-on-hover">
				<?php do_action( 'flatsome_product_box_tools_bottom' ); ?>
			</div>
			<div class="image-tools <?php echo flatsome_product_box_actions_class(); ?>">
				<?php do_action( 'flatsome_product_box_actions' ); ?>
			</div>
			<?php if ( $out_of_stock ) { ?><div class="out-of-stock-label"><?php _e( 'Out of stock', 'woocommerce' ); ?></div><?php } ?>
		</div><!-- box-image -->

		<div class="box-text <?php echo flatsome_product_box_text_class(); ?>">

            <?php

            $color = $product->get_attribute('color');
            if(strpos($color,'|'))      $array=explode("|", $color);
            elseif(strpos($color,','))  $array=explode(",", $color);
            else $array[]=$color;

            $color_count=count($array);
            if($color!=""){
                echo "<ul class='color-chip'>";
                for($i=0;$i<=($color_count-1);$i++){
                    if(strpos($array[$i],"BEIGE"))$array[$i]="#ddc5ac";
                    elseif(strpos($array[$i],"BLACK"))$array[$i]="#000000";
                    elseif(strpos($array[$i],"WHITE"))$array[$i]="#FFFFFF";
                    elseif(strpos($array[$i],"BLUE"))$array[$i]="#0f4ad0";
                    elseif(strpos($array[$i],"BROWN"))$array[$i]="#832b13";
                    elseif(strpos($array[$i],"CAMEL"))$array[$i]="#d27028";
                    elseif(strpos($array[$i],"DARKBLUE"))$array[$i]="#021b76";
                    elseif(strpos($array[$i],"DARKGRAY"))$array[$i]="#393939";
                    elseif(strpos($array[$i],"GRAY"))$array[$i]="#a8a8a8";
                    elseif(strpos($array[$i],"GREEN"))$array[$i]="#056e16";
                    elseif(strpos($array[$i],"IVORY"))$array[$i]="#fbfaf7";
                    elseif(strpos($array[$i],"KHAKI"))$array[$i]="#2b3f1e";
                    elseif(strpos($array[$i],"LIME"))$array[$i]="#e5ffcc";
                    elseif(strpos($array[$i],"MINT"))$array[$i]="#a3e09e";
                    elseif(strpos($array[$i],"MUSTARD"))$array[$i]="#ffbe0e";
                    elseif(strpos($array[$i],"ORABGE"))$array[$i]="#ff7e15";
                    elseif(strpos($array[$i],"PINK"))$array[$i]="#ff81a5";
                    elseif(strpos($array[$i],"PURPLE"))$array[$i]="#eba1f8";
                    elseif(strpos($array[$i],"RED"))$array[$i]="#FF0000";
                    elseif(strpos($array[$i],"WINE"))$array[$i]="#bb0f38";
                    elseif(strpos($array[$i],"YELLOW"))$array[$i]="#ffd200";

                    ?>
                    <li style="background:<?=$array[$i]?>; border:1px solid;"></li>
                    <?php
                }
                echo "</ul>";
            }

				echo '<div class="title-wrapper">';
				do_action( 'woocommerce_shop_loop_item_title' );
				echo '</div>';


				echo '<div class="price-wrapper">';
				do_action( 'woocommerce_after_shop_loop_item_title' );
				echo '</div>';

				do_action( 'flatsome_product_box_after' );

			?>
		</div><!-- box-text -->
	</div><!-- box -->
	<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
	</div><!-- .col-inner -->
</div><!-- col -->
