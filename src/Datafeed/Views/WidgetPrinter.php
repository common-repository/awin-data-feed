<?php

namespace Datafeed\Views;

class WidgetPrinter
{
    /**
     * @param $layout
     * @param $title
     * @param array $data
     *
     * @return string
     */
    public function getWidget($layout, $title, array $data)
    {
        if ($layout === 'horizontal') {
            return $this->horizontalWidget($title, $data);
        } elseif ($layout ===  'vertical') {
            return $this->verticalWidget($title, $data);
        } elseif ($layout === 'horizontalSc') {
            return $this->horizontalWidgetSc($title, $data);
        }
    }

    /**
     * @param $title
     * @param array $data
     *
     * @return string
     */
    private function horizontalWidget($title, array $data)
    {
        $productList = '
            <table class="horizontal aw-datafeed">
                <tr><th class="title" colspan="' . count($data) . ';">' . $title . '</th></tr>
                <tr class="image">';
        foreach ($data as $product) {
            $productList .= '
                <td class="hover image">
                    <a class="trackImage-' . $product['id'] . '" href="' . $product['awDeepLink'] . '" target="_blank" alt="' . $product['productName'] . '" title="' . $product['productName'] . '">
                        <img src="' . $product['awImageUrl'] . '" />
                    </a>
                </td>
            ';
        }

        $productList .= '
                </tr>
                <tr class="name">';
        foreach ($data as $product) {
            $productList .= '
                <td class="name">
                    ' . $this->wrapWords($product['productName']) . '
                </td>
            ';
        }

        $productList .= '
                </tr>
                <tr class="price">';
        foreach ($data as $product) {
            $productList .= '
                <td class="price">' . $this->getCurrencySymbol($product['currency']) . '' . number_format($product['price'], 2) . '</td>
            ';
        }
        $productList .= '
                </tr>';
        return $productList;
    }

    /**
     * @param $title
     * @param array $data
     *
     * @return string
     */
    private function verticalWidget($title, array $data)
    {
        $productList = '<table class="vertical aw-datafeed">
                            <tr><th class="title" colspan="2">' . $title . '</th></tr>';
        foreach ($data as $product) {
            $productList .= '
                    <tr class="priceBorder">
                        <td class="hover image" rowspan="2">
                            <a class="trackImage-' . $product['id'] . '" href="' . $product['awDeepLink'] . '" target="_blank" alt="' . $product['productName'] . '" title="' . $product['productName'] . '">
                                <img src="' . $product['awImageUrl'] . '" />
                            </a>
                        </td>
                        <td class="productName">
                            ' . $this->wrapWords($product['productName']) . '
                        </td>
                    </tr>
                    <tr>
                        <td class="price">' . $this->getCurrencySymbol($product['currency']) . '' . number_format($product['price'], 2) . '</td>

                    </tr>
            ';
        }
        $productList .= '</table>';

        return $productList;
    }

    /**
     * @param $title
     * @param array $data
     *
     * @return string
     */
    private function horizontalWidgetSc($title, array $data)
    {
        $productList = '
            <table class="horizontal">
                <tr><th class="title" colspan="' . count($data) . ';">' . $title . '</th></tr>
                <tr class="image">';
        foreach ($data as $product) {
            $productList .= '
                <td class="hover image">
                    <a class="trackImage-' . $product['id'] . '" href="' . $product['awDeepLink'] . '" target="_blank" alt="' . $product['productName'] . '" title="' . $product['productName'] . '">
                        <img src="' . $product['awImageUrl'] . '" />
                    </a>
                </td>
            ';
        }

        $productList .= '
                </tr>
                <tr class="name">';
        foreach ($data as $product) {
            $productList .= '
                <td class="name">
                    ' . $this->wrapWords($product['productName']) . '
                </td>
            ';
        }

        $productList .= '
                </tr>
                <tr class="price">';
        foreach ($data as $product) {
            $productList .= '
                <td class="price">' . $this->getCurrencySymbol($product['currency']) . '' . number_format($product['price'], 2) . '</td>
            ';
        }
        $productList .= '
                </tr>';
        return $productList;
    }

    /**
     * @param string $name
     * @return string
     */
    private function getCurrencySymbol($name)
    {
        if (strtoupper($name) === 'GBP') {
            return "£";
        } elseif (strtoupper($name) === 'USD') {
            return "$";
        }
    }

    /**
     * @param  string  $text
     * @param  integer $count
     * @return string
     */
    private function wrapWords($text, $count = 7)
    {
        $exploded = explode(' ', $text);
        if (count($exploded) <= $count) {
            return $text;
        }
        $slice = array_slice($exploded, 0, $count);
        $imploded = implode(' ', $slice);

        return $imploded;
    }
}
