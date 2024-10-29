<?php

namespace Datafeed;

class Widget extends \WP_Widget
{
    public function __construct()
    {
        $widget_details = array(
            'className' => 'Widget',
            'description' => __('Sell your affiliate product from Affiliate Window product data feed', 'awin-data-feed')
        );

        parent::__construct('Widget', 'Affiliate Window data feed', $widget_details);
    }

    public function run()
    {
        add_action( 'wp_enqueue_scripts', array($this, 'add_stylesheet') );
        add_action( 'wp_enqueue_scripts', array($this, 'setScript') );
        add_action( 'admin_enqueue_scripts', array($this, 'add_stylesheet') );

        add_action('widgets_init', array($this, 'init_datafeed_widget'));
    }

    public function init_datafeed_widget()
    {
        register_widget('Datafeed\Widget');
        $plugin_dir = plugin_dir_path(plugin_basename(__FILE__));
        load_plugin_textdomain('awin-data-feed', null, $plugin_dir . '/../languages');
    }

    public function form($instance)
    {
        include __DIR__ . '/Views/Widget/form.html';
?>
        <div class='mfc-text'></div>
<?php
        echo $args['after_widget'];
    }

    public function update($new_instance, $old_instance)
    {
        return $new_instance;
    }

    public function widget($args, $instance)
    {
        echo $args['before_widget'];

        $instance = array_map('sanitize_text_field', $instance);

        $layout = $instance['layout'];
        $layout = empty($layout) ? 'vertical' : $layout;
        $layout = ucfirst($layout);

        include __DIR__ . '/Views/Widget/widget.php';

        echo $args['after_widget'];
    }

    public function add_stylesheet()
    {
        wp_register_style('awindatafeed-style', plugins_url('../../assets/aw-styles.css', __FILE__));
        wp_enqueue_style('awindatafeed-style');
    }

    public function setScript()
    {
        // Get the Path to this plugin's folder
        $path = plugin_dir_url(__FILE__);

        // Enqueue our script
        wp_enqueue_script(
            'awindatafeed',
            $path . '../../assets/awindatafeed.js',
            array('jquery'),
            '1.0.0',
            true
        );

        // Get the protocol of the current page
        $protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';

        // Set the ajaxurl Parameter which will be output right before
        // our ajax-delete-posts.js file so we can use ajaxurl
        $params = array(
            // Get the url to the admin-ajax.php file using admin_url()
            'ajaxurl' => admin_url('admin-ajax.php', $protocol),
        );
        // Print the script to our page
        wp_localize_script('awindatafeed', 'awindatafeed_params', $params);
    }
}
