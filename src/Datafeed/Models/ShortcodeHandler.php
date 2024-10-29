<?php

namespace Datafeed\Models;

class ShortcodeHandler
{
    public function run()
    {
        add_shortcode('AWIN_DATA_FEED', array($this, 'renderShortCode'));
    }

    /**
     * @param array $attr
     *
     * @return string
     */
    public function renderShortCode($attr)
    {
        $ajaxLayout = "horizontalSc";

        $ajaxLayout = ucfirst($ajaxLayout);
        $title = '';
        $keywords = '';
        $noOfProducts = 0;
        $layout = 'vertical';

        if (isset($attr['title'])) {
            $title = esc_attr($attr['title']);
        }
        if (isset($attr['keywords'])) {
            $keywords = esc_attr($attr['keywords']);
        }
        if(isset($attr['no_of_product'])) {
            $noOfProducts = esc_attr($attr['no_of_product']);
        }
        if (isset($attr['layout'])) {
            $layout = esc_attr($attr['layout']);
        }

        return '
        <form name="swFeedSc" id="swFeed' . $ajaxLayout . '">
            <input name="title" type="hidden" value="' . $title . '"/>
            <input name="keywords" type="hidden" value="' . $keywords . '"/>
            <input name="displayCount" type="hidden" value="' . $noOfProducts . '"/>
            <input name="layout" type="hidden" value="' . $layout . '"/>
            <input name="action" type="hidden" value="get_sw_product"/>
        </form>
        <div class="widgetContentSc">
            <div class="ajaxResponse' . $ajaxLayout . '" id="ajaxResponse' . $ajaxLayout . '"></div>
            <div class="next' . $ajaxLayout . '">
                <button id="next' . $ajaxLayout . '" class="next" style="display:none"></button>
            </div>
        </div>';
    }
}
