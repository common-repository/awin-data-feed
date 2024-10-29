<?php

namespace Datafeed\Models;

use Datafeed\Models\WidgetDisplayProcessor as Processor;
use Datafeed\Models\DBAdapter;

class AjaxHandler
{
    /** @var Processor */
    private $processor;

    /** @var DBAdapter */
    private $adapter;

    /**
     * @param \Datafeed\Processor $processor
     * @param \Datafeed\DBAdapter $adapter
     */
    public function __construct(Processor $processor, DBAdapter $adapter)
    {
        $this->processor = $processor;
        $this->adapter = $adapter;
    }

    public function run()
    {
        /** get_sw_product */
        add_action('wp_ajax_get_sw_product', array($this, 'get_sw_product'));
        add_action('wp_ajax_nopriv_get_sw_product', array($this, 'get_sw_product'));

        /** track_user_click */
        add_action('wp_ajax_track_user_click', array($this, 'track_user_click'));
        add_action('wp_ajax_nopriv_track_user_click', array($this, 'track_user_click'));
    }

    public function get_sw_product()
    {
        $title = sanitize_text_field($_REQUEST['title']);
        $count = sanitize_text_field($_REQUEST['displayCount']);
        $layout = sanitize_text_field($_REQUEST['layout']);
        $keywords = sanitize_text_field($_REQUEST['keywords']);

        if (!empty($title)) {
            $this->processor->setTitle($title);
        }
        if (!empty($count)) {
            $this->processor->setProductCount($count);
        }
        $this->processor->setLayout($layout);

        if (!empty($keywords)) {
            $this->processor->setKeywords($keywords);
        }

        echo wp_kses($this->processor->displayWidget(), 'post');
        wp_die(); // this is required to terminate immediately and return a proper response
    }

    public function track_user_click()
    {
        $row = array(
            'feedId' => sanitize_text_field($_REQUEST['feedId']),
            'clickIp' => $this->getUserIp(),
            'clickDateTime' => current_time('mysql')
        );

        $this->adapter->saveAnalytics($row);
        wp_die();
    }

    private function getUserIp()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = sanitize_text_field($_SERVER['HTTP_CLIENT_IP']);
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = sanitize_text_field($_SERVER['HTTP_X_FORWARDED_FOR']);
        } else {
            $ip = sanitize_text_field($_SERVER['REMOTE_ADDR']);
        }

        return apply_filters('wpb_get_ip', $ip);
    }
}
