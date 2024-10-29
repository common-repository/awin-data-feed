<?php

namespace Datafeed\Models;

class OptionHandler
{
    public function updateOptions(array $data)
    {
        $deliveryMethod = sanitize_text_field($data['deliveryMethod']);
        delete_option('sw_deliveryMethod');
        add_option('sw_deliveryMethod', $deliveryMethod);

        $categories = filter_var($data['categories'], FILTER_SANITIZE_NUMBER_INT);
        delete_option('sw_categories');
        add_option('sw_categories', $categories);

        $maxPriceRadio = sanitize_text_field($data['maxPriceRadio']);
        delete_option('sw_maxPriceRadio');
        add_option('sw_maxPriceRadio', $maxPriceRadio);

        if ($maxPriceRadio == 'range') {
            $minPrice = sanitize_text_field($data['minPrice']);
            $minPrice = intval($minPrice);
            delete_option('sw_minPrice');
            add_option('sw_minPrice', $minPrice);

            $maxPrice = sanitize_text_field($data['maxPrice']);
            $maxPrice = intval($maxPrice);
            delete_option('sw_maxPrice');
            add_option('sw_maxPrice', $maxPrice);
        } else {
            delete_option('sw_minPrice');
            delete_option('sw_maxPrice');
        }
    }

    public function delete_sw_options()
    {
        delete_option('sw_deliveryMethod');
        delete_option('sw_categories');
        delete_option('sw_maxPriceRadio');
        delete_option('sw_minPrice');
        delete_option('sw_maxPrice');
    }
}
