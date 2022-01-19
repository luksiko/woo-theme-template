<?php
/**
 * Single variation cart button
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.0
 */

defined('ABSPATH') || exit;

global $product;
?>
<div class="woocommerce-variation-add-to-cart variations_button">
    <?php do_action('woocommerce_before_add_to_cart_button'); ?>

	<div class="quantity-btn-group d-flex">
      <?php woocommerce_quantity_input(
          array(
              'min_value' => apply_filters('woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product),
              'max_value' => apply_filters('woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product),
              'input_value' => isset($_POST['quantity']) ? wc_stock_amount(wp_unslash($_POST['quantity'])) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
          )
      ); ?>

		<div class="list-btn-group">
        <?php echo sprintf(
            '<a href="%s" data-product_id="%s" class="%s">%s</a>',
            $product->add_to_cart_url(),
            $product->get_id(),
            'btn btn-black product_type_' . ($product->get_type() . $product->is_purchasable() && $product->is_in_stock() && $product->supports('ajax_add_to_cart') ? ' single_add_to_cart_button add_to_cart_button ajax_add_to_cart' : ''),
            $product->add_to_cart_text()
        ); ?>
		</div>
	</div>
    <?php do_action('woocommerce_after_add_to_cart_button'); ?>

	<input type="hidden" name="add-to-cart" value="<?php echo absint($product->get_id()); ?>"/>
	<input type="hidden" name="product_id" value="<?php echo absint($product->get_id()); ?>"/>
	<input type="hidden" name="variation_id" class="variation_id" value="0"/>
</div>
