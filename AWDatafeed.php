<?php
defined('ABSPATH') or exit;
/*
Plugin Name: Awin Data Feed
Version: 1.8.6
Plugin URI: https://wordpress.org/plugins/awin-data-feed
Description: Sell your affiliate product from Awin product datafeed
Author: awinglobal
Author URI: https://profiles.wordpress.org/awinglobal/#content-plugins
Text Domain: awin-data-feed
Domain Path: /languages
*/

use Datafeed\DI\PluginContainer;

spl_autoload_register('datafeed_autoloader');
function datafeed_autoloader($class_name)
{
    if (false !== strpos($class_name, 'Datafeed')) {
        $classes_dir = realpath(plugin_dir_path(__FILE__)) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;
        $class_file = str_replace('\\', DIRECTORY_SEPARATOR, $class_name) . '.php';
        require_once $classes_dir . $class_file;
    }
}

add_action('plugins_loaded', 'datafeed_init'); // Hook initialization function
function datafeed_init()
{
    $container = new PluginContainer(); // Create container
    $container['path'] = realpath(plugin_dir_path(__FILE__)) . DIRECTORY_SEPARATOR;
    $container['url'] = plugin_dir_url(__FILE__);
    $container['version'] = '1.0.0';
    $container['settings_page_properties'] = array(
        'parent_slug'       => 'datafeed-settings',
        'page_title'        => __('Datafeed', 'awin-data-feed'),
        'menu_title'        => __('Datafeed', 'awin-data-feed'),
        'sub_menu_title'    => __('Settings', 'awin-data-feed'),
        'help_menu_title'   => __('Datafeed Guide', 'awin-data-feed'),
        'help_menu_slug'    => 'data-feed-guide',
        'capability'        => 'manage_options',
        'menu_slug'         => 'datafeed-settings',
        'option_group'      => 'datafeed_option_group',
        'option_name'       => 'datafeed_option_name',
        'icon'              => plugins_url('assets/icon.png', __FILE__),
    );
    $container['settings_page'] = function ($container) {
        return new Datafeed\Views\SettingsMenu(
            $container['db_adapter'],
            $container['option_handler'],
            $container['importer'],
            $container['upload_error_handler'],
            $container['processor'],
            $container['settings_page_properties']
        );
    };

    $container['option_handler'] = function ($container) {
        return new \Datafeed\Models\OptionHandler();
    };

    $container['db_adapter'] = function ($container) {
        return new \Datafeed\Models\DBAdapter($container['option_handler']);
    };

    $container['widget_printer'] = function ($container) {
        return new \Datafeed\Views\WidgetPrinter();
    };

    $container['processor'] = function ($container) {
        return new \Datafeed\Models\WidgetDisplayProcessor($container['db_adapter'], $container['widget_printer']);
    };

    $container['upload_error_handler'] = function ($container) {
        return new \Datafeed\Models\Csv\UploadErrorHandler(array());
    };

    $container['importer'] = function ($container) {
        return new \Datafeed\Models\Csv\Importer($container['db_adapter']);
    };

    $container['shortcode_handler'] = function ($container) {
        return new \Datafeed\Models\ShortcodeHandler();
    };

    $container['widget'] = function ($container) {
        return new \Datafeed\Widget();
    };

    $container['ajax_handler'] = function ($container) {
        return new \Datafeed\Models\AjaxHandler($container['processor'], $container['db_adapter']);
    };

    $container->run();

}
