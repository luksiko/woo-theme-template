<?php
/**
 * Plugin Name: LuxBooking Test Create Plugin
 * Description: Тестовый плагин бронирования для LuxBooking
 * Author: luksiko
 * Plugin URI: https://wordpress.org/plugins/luxbooking/
 * Version: 1.0.0
 * Description: Тестовый плагин бронирования для LuxBooking
 * Tested up to: 4.9.8
 * Text Domain: luxbooking
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * Copyright 2022 (email : luksiko@mail.ru)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA
 */

defined(('ABSPATH') or die());

class LuxBooking
{
// method
    function __construct() {
    }

    public function register() {

        // Регистрируем новый тип записи
        add_action('init', [$this, 'custom_post_type']);

        //enqueue admin
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_front']);

        // Load template
        add_filter('template_include', [$this, 'room_template']);

        // Add menu admin
        add_action('admin_menu', [$this, 'add_admin_menu']);

        // Add links to Plugins page
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), [$this, 'add_plugin_setting_links']);

        // Регистрируем поля в настройках плагина
        add_action('admin_init', [$this, 'settings_init']);
    }

    static function activation() {
        // update rewrite rules
        flush_rewrite_rules();
    }

    static function deactivation() {
        flush_rewrite_rules();
    }

    public function get_terns_hierarchical($tax_name) {
        $taxonomy_terms = get_terms($tax_name, ['hide_empty' => false, 'parent' => 0]);
        if (!empty($taxonomy_terms)) {
            foreach ($taxonomy_terms as $term) {
                echo '<option value="' . $term->term_id . '">' . $term->name . '</option>';

                $child_terms = get_terms($tax_name, ['hide_empty' => false, 'parent' => $term->term_id]);
                if (!empty($child_terms)) {
                    foreach ($child_terms as $child_term) {
                        echo '<option value="' . $child_term->term_id . '">&nbsp;&nbsp;-&nbsp;' . $child_term->name . '</option>';
                    }
                }
            }
        }
    }

    public function settings_init() {
        // Регистрируем настройки в таблице wp_options
        register_setting('booking_settings', 'booking_settings_options');

        add_settings_section('booking_settings_section', esc_html__('Settings', 'luxbooking'), [$this, 'settings_section_html'], 'luxbooking_settings');

        add_settings_field('posts_per_page', esc_html__('Posts per page', 'luxbooking'), [$this, 'posts_per_page_html'], 'luxbooking_settings', 'booking_settings_section');
        add_settings_field('title_for_rooms', esc_html__('Archive page title', 'luxbooking'), [$this, 'title_for_rooms_html'], 'luxbooking_settings', 'booking_settings_section');
    }

    public function settings_section_html() {
        echo esc_html__('Settings LuxBooking', 'luxbooking');
    }

    public function posts_per_page_html() {
        $options = get_option('booking_settings_options'); ?>

			<input type="text" name="booking_settings_options[posts_per_page]"
			       value="<?php echo isset($options['posts_per_page']) ? $options['posts_per_page'] : ''; ?>"/>
    <?php }

    public function title_for_rooms_html() {
        $options = get_option('booking_settings_options'); ?>
			<input type="text" name="booking_settings_options[title_for_rooms]"
			       value="<?php echo isset($options['title_for_rooms']) ? $options['title_for_rooms'] : ''; ?>"/>

    <?php }

    // Add Settings link to plugin page
    public function add_plugin_setting_links($links) {
        $custom_link = '<a href="admin.php?page=luxbooking_settings">' . esc_html__('Settings', 'luxbooking') . '</a>';
        array_push($links, $custom_link);
        return $links;
    }

    // Добавляем страницу опций плагина
    // Custom template for room
    public function add_admin_menu() {
        add_menu_page(
            esc_html__('LuxBooking Settings Page', 'luxbooking'),
            esc_html__('LuxBooking', 'luxbooking'),
            'manage_options',
            'luxbooking_settings',
            [$this, 'luxbooking_settings_page'],
            'dashicons-admin-multisite',
            100
        );
    }

    // luxbooking Page подгружаем с файла
    public function luxbooking_settings_page() {
        require_once plugin_dir_path(__FILE__) . 'admin/admin.php';
    }

    // Даем пользователю возможность добавлять шаблоны страниц в тему
    public function room_template($template) {
        if (is_post_type_archive('room')) {
            $theme_files = ['archive-room.php', 'luxbooking/archive-room.php'];
            $exists_in_theme = locate_template($theme_files, false);
            if ($exists_in_theme != '') {
                return $exists_in_theme;
            } else {
                return plugin_dir_path(__FILE__) . 'templates/archive-room.php';
            }
        }
        return $template;
    }

    // Шрифты и стили в админке
    public function enqueue_admin() {
        wp_enqueue_style('luxbookingStyle', plugins_url('assets/admin/styles.css', __FILE__));
        wp_enqueue_script('luxbookingScript', plugins_url('assets/admin/scripts.js', __FILE__));
    }

    // Шрифты и стили на сайте
    public function enqueue_front() {
        wp_enqueue_style('luxbookingStyle', plugins_url('assets/front/styles.css', __FILE__), [], time());
        wp_enqueue_script('luxbookingScript', plugins_url('assets/front/scripts.js', __FILE__));
    }

    function custom_post_type() {
        register_post_type('room', [
            'public' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'rooms'],
            'label' => esc_html__('Room', 'luxbooking'),
            'menu_icon' => 'dashicons-book',
            'supports' => ['title', 'editor', 'thumbnail'],
        ]);
        /*
                register_post_type('booking', [
                    'public' => true,
                    'has_archive' => true,
                    'rewrite' => ['slug' => 'booking'],
                    'label' => esc_html__('Booking', 'luxbooking'),
                    'menu_icon' => 'dashicons-book',
                    'supports' => ['title', 'editor', 'thumbnail'],
                ]);
                        */

        // Add new taxonomy, make it hierarchical (like categories)
        $labels = array(
            'name' => _x('Location', 'taxonomy general name', 'luxbooking'),
            'singular_name' => _x('Location', 'taxonomy singular name', 'luxbooking'),
            'search_items' => __('Search Locations', 'luxbooking'),
            'all_items' => __('All Locations', 'luxbooking'),
            'parent_item' => __('Parent Location', 'luxbooking'),
            'parent_item_colon' => __('Parent Location:', 'luxbooking'),
            'edit_item' => __('Edit Location', 'luxbooking'),
            'update_item' => __('Update Location', 'luxbooking'),
            'add_new_item' => __('Add New Location', 'luxbooking'),
            'new_item_name' => __('New Location Name', 'luxbooking'),
            'menu_name' => __('Location', 'luxbooking'),
        );
        $args = array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'room/location'),
        );
        register_taxonomy('location', 'room', $args);

        // Add new taxonomy, make it hierarchical (like categories)
        $labels_type = array(
            'name' => _x('Type', 'taxonomy general name', 'luxbooking'),
            'singular_name' => _x('Type', 'taxonomy singular name', 'luxbooking'),
            'search_items' => __('Search Types', 'luxbooking'),
            'all_items' => __('All Types', 'luxbooking'),
            'parent_item' => __('Parent Type', 'luxbooking'),
            'parent_item_colon' => __('Parent Type:', 'luxbooking'),
            'edit_item' => __('Edit Type', 'luxbooking'),
            'update_item' => __('Update Type', 'luxbooking'),
            'add_new_item' => __('Add New Type', 'luxbooking'),
            'new_item_name' => __('New Type Name', 'luxbooking'),
            'menu_name' => __('Type', 'luxbooking'),
        );
        $args_type = array(
            'hierarchical' => true,
            'labels' => $labels_type,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'room/type'),
        );
        register_taxonomy('type', 'room', $args_type);

    }
}

if (class_exists('LuxBooking')) {
    $luxBooking = new LuxBooking();
    $luxBooking->register();
}

register_activation_hook(__FILE__, array($luxBooking, 'activation'));
register_deactivation_hook(__FILE__, array($luxBooking, 'deactivation'));
//register_uninstall_hook(__FILE__, array($luxBooking, 'uninstall'));
