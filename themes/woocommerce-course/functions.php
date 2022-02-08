<?php

/* подключение стилей и скриптов */
add_action('wp_enqueue_scripts', function () {

    wp_enqueue_style('open-sans-font', 'https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,500,600,700,800');
    wp_enqueue_style('playfair-font', 'https://fonts.googleapis.com/css?family=Playfair+Display');

    wp_enqueue_style('bootstrap', get_stylesheet_directory_uri() . '/assets/css/vendor/bootstrap.min.css');
    wp_enqueue_style('dl-icon', get_stylesheet_directory_uri() . '/assets/css/vendor/dl-icon.css');
    wp_enqueue_style('fa', get_stylesheet_directory_uri() . '/assets/css/vendor/font-awesome.css');
    wp_enqueue_style('helper', get_stylesheet_directory_uri() . '/assets/css/helper.min.css');
    wp_enqueue_style('plugins', get_stylesheet_directory_uri() . '/assets/css/plugins.css');
    wp_enqueue_style('main', get_stylesheet_directory_uri() . '/assets/css/style.css', array(), time());

    wp_enqueue_script('jquery');
    wp_enqueue_script('bootstrap', get_stylesheet_directory_uri() . '/assets/js/bootstrap.min.js', 'jquery', null, true);
    wp_enqueue_script('plugins', get_stylesheet_directory_uri() . '/assets/js/plugins.js', 'jquery', null, true);
    wp_enqueue_script('scripts', get_stylesheet_directory_uri() . '/assets/js/scripts.js', array(), time(), true);


    wp_deregister_style('woocommerce-general');
    wp_deregister_style('woocommerce-layout');
});


/* регистрация меню */

register_nav_menus(
    array(
        'head_menu' => 'Меню в шапке',
        'foot_1' => 'Футер 1: Каталог',
        'foot_2' => 'Футер 2: Страницы',
        'foot_3' => 'Футер 3: Товары',
    )
);

/*add_filter('woocommerce_loop_add_to_cart_link', function ($html, $product, $args) {
    $html = '<a href="' . $product->add_to_cart_url() . '" class="btn btn-primary btn-block">Купить</a>';
    return $html;
}, 10, 3);*/

add_theme_support('woocommerce');

add_filter('woocommerce_breadcrumb_defaults', function () {
    return array(
        'delimiter' => '',
        'wrap_before' => '<nav class="page-breadcrumb"><ul class="d-flex justify-content-center">',
        'wrap_after' => '</ul></nav>',
        'before' => '<li>',
        'after' => '</li>',
        'home' => _x('Главная', 'breadcrumb', 'woocommerce'),
    );
});

/*
register_sidebar(array(
    'id' => 'filter',
    'name' => 'Сайдбар фильтров товаров',
    'before_widget' => '<div class="single-sidebar-wrap">',
    'after_widget' => '</div>',
    'before_title' => '<h3 class="sidebar-title">',
    'after_title' => '</h3>',
));
*/

//отключаем вывод хлебных крошек
remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
//отключаем вывод сайдбара
remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);

add_action('wp_footer', function () { ?>
	<script>
      jQuery(function ($) {
          $('.change_razmer').on('click', function () {
              var el = $(this),
                  name = el.text(),
                  val = el.data('value');
              $('.change_razmer').removeClass('active');
              el.addClass('active');

              $('#pa_razmer').val(val);
              $('#pa_razmer').change();
              $('#configurable-name').html('Размер: <b>' + name + '</b>');
          });
          $('.single_variation_wrap').on('show_variation', function (event, variation) {
              $('#prices-group').html(variation.price_html);
              if (!variation.is_in_stock) {
                  $('.stock-status').text('Нет в наличии');
              }
          });
          // добавление рейтинга к отзыву через звёзды в карточке товара
          $('.change-star').on('click', function () {
              var el = $(this),
                  val = el.data('value');

              el.addClass('active');
              el.nextAll().removeClass('active');
              el.prevAll().addClass('active');

              $('select[name="rating"]').val(val);
          });
      });
	</script>
<?php });

// удаление/добавление из/в мини-корзины динамически
add_filter('woocommerce_add_to_cart_fragments', function ($fragments) {
    ob_start(); ?>
	<span class="cart-count"><?php echo count(WC()->cart->get_cart()); ?></span>
    <?php
    $fragments['.cart-count'] = ob_get_clean();
    return $fragments;
});

add_filter('woocommerce_checkout_fields', function ($checkout_fields) {
    $checkout_fields['billing']['billing_phone']['placeholder'] = 'Телефон';
    $checkout_fields['billing']['billing_first_name']['placeholder'] = 'Как вас зовут?';
    $checkout_fields['billing']['billing_last_name']['placeholder'] = 'Ваша фамилия';
    $checkout_fields['billing']['billing_email']['placeholder'] = 'E-mail';
    $checkout_fields['billing']['billing_address_1']['placeholder'] = 'Адрес';
    $checkout_fields['billing']['billing_city']['placeholder'] = 'Город';
    $checkout_fields['billing']['billing_postcode']['placeholder'] = 'Индекс';
    $checkout_fields['billing']['billing_country']['placeholder'] = 'Страна';
    $checkout_fields['billing']['billing_state']['placeholder'] = 'Регион';
    return $checkout_fields;
});

// заменяем стили у кнопки через хук
add_filter('woocommerce_order_button_html', function ($html) {
    return str_replace('button alt', 'btn btn-full btn-black mt-26', $html);
});
// выводить телефон в Orders
add_action( 'manage_shop_order_posts_custom_column' , 'custom_orders_list_column_content', 50, 2 );
function custom_orders_list_column_content( $column, $post_id ) {
    if ( $column == 'order_number' )
    {
        global $the_order;

        if( $phone = $the_order->get_billing_phone() ){
            $phone_wp_dashicon = '<span class="dashicons dashicons-phone"></span> ';
            echo '<br><a href="tel:'.$phone.'">' . $phone_wp_dashicon . $phone.'</a></strong>';
        }

        if( $email = $the_order->get_billing_email() ){
            echo '<br><strong><a href="mailto:'.$email.'">' . $email . '</a></strong>';
        }
    }
}
