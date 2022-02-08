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
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined('ABSPATH') || exit;

global $product;

// Ensure visibility.
if (empty($product) || !$product->is_visible()) {
    return;
}

$product_published = $product->get_date_created()?$product->get_date_created():'';// $product_published->date
?>

<!-- Start Single Product -->
<div <?php $class = is_product() ? 'col-lg-3 col-sm-6' : 'col-lg-4 col-sm-6';
wc_product_class($class, $product); ?>>
	<div class="single-product-wrap">
		<!-- Product Thumbnail -->
		<figure class="product-thumbnail">
			<a href="<?php echo $product->get_permalink(); ?>" class="d-block">
          <?php echo $product->get_image(); ?>
			</a>
			<figcaption class="product-hvr-content">
          <?php echo sprintf(
              '<a href="%s" data-product_id="%s" class="%s">%s</a>',
              $product->add_to_cart_url(),
              $product->get_id(),
              'btn btn-black btn-addToCart product_type_' . ($product->get_type() . $product->is_purchasable() && $product->is_in_stock() && $product->supports('ajax_add_to_cart') ? ' add_to_cart_button ajax_add_to_cart' : ''),
              $product->add_to_cart_text()
          ); ?>
          <?php
          if ($product->is_on_sale()) {
              echo '<span class="product-badge sale">' . esc_html__('Sale!', 'woocommerce') . '</span>';
          } elseif ($product->is_featured()) {
              echo '<span class="product-badge hot">' . esc_html__('Hot!', 'woocommerce') . '</span>';
          } elseif (strtotime($product_published) < (time() - 86400 * 5)) {
              echo '<span class="product-badge">' . esc_html__('New!', 'woocommerce') . '</span>';
          }
          ?>
			</figcaption>
		</figure>

		<!-- Product Details -->
		<div class="product-details">
			<h2 class="product-name"><a href="<?php the_permalink(); ?>"><?php echo $product->get_title(); ?></a></h2>
			<div class="product-prices"><?php echo $product->get_price_html(); ?></div>
			<div class="list-view-content">
				<p class="product-desc"><?php echo $product->get_short_description(); ?></p>
				<div class="list-btn-group mt-30 mt-sm-14">
            <?php echo sprintf(
                '<a href="%s" data-product_id="%s" class="%s">%s</a>',
                $product->add_to_cart_url(),
                $product->get_id(),
                'btn btn-black product_type_' . ($product->get_type() . $product->is_purchasable() && $product->is_in_stock() && $product->supports('ajax_add_to_cart') ? ' add_to_cart_button ajax_add_to_cart' : ''),
                $product->add_to_cart_text()
            ); ?>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- End Single Product -->
