<?php

namespace Datafeed\Views;

class SettingsMenu extends AbstractSettings
{
    public function render_settings_page()
    {
        $max_file_size = ini_get('upload_max_filesize');

        if (!empty($_POST) && check_admin_referer('sw_admin_option')) {

            if (isset($_POST['submit']) && !empty($_FILES["dataFeed"])) {
                $this->errorHandler->handleError($_FILES["dataFeed"]);
                if (!$this->errorHandler->isValid()) {
                    echo "<h3 class='error center'>Failed! </br>" . esc_html($this->errorHandler->getMessage()) . "</br></h3>";
                } else {
                    $this->importer->setFile($_FILES["dataFeed"]["tmp_name"]);
                    $total = $this->importer->importToTable();
                    echo "<h3 class='info center'>Success! </br>" . esc_html($total) . " Row(s) Inserted</h3>";
                }
            }
        }

        if (!empty($_POST) && check_admin_referer('sw_admin_option')) {

            if (!empty($_POST['filterOptions'])) {
                $this->optionHandler->updateOptions($_POST);
            }
        }

?>

        <section class="wrap">
            <h2><?php _e('Import your shopwindow data feed to display in widget', 'awin-data-feed') ?></h2>
            <h3 class="info"><?php printf(__('Maximum file size must be smaller than: %sB', 'awin-data-feed'),  $max_file_size); ?> </h3>
            <p>[<?php _e('Update \'upload_max_filesize\' directive in php.ini for larger import', 'awin-data-feed') ?>]</p>

            <form enctype="multipart/form-data" name="csvUpload" method="post" action="">
                <?php echo wp_nonce_field('sw_admin_option'); ?>
                <h3>
                    <span>
                        <input type="file" name="dataFeed" id="dataFeed">
                        <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo __('Import Data Feed', 'awin-data-feed') ?>">
                    </span>
                </h3>
            </form>
        </section>

        <?php
        if ($this->processor->hasFeedInDb()) {
        ?>
            <div class="productCount">
                <div class="count"> <?php printf(
                                        __('You have <span class="counter">%s</span> products in your data store to choose from', 'awin-data-feed'),
                                        $this->processor->getFeedCount()
                                    ); ?></h1>
                </div>
            </div>
            <section>
                <div class="options">
                    <div class="form">
                        <form name="swFilters" id="swFilters" method="post">
                            <?php echo wp_nonce_field('sw_admin_option'); ?>
                            <table class="aw-filter" cellspacing="0" cellpadding="0">
                                <tr>
                                    <th colspan="2" class="title">
                                        <h2><?php _e('Filter products*', 'awin-data-feed'); ?></h2>
                                    </th>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <?php _e('(DEFAULT) displays products in a random order.', 'awin-data-feed'); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th colspan="2" class="filterType"><?php _e('By Delivery Type', 'awin-data-feed'); ?></th>
                                </tr>
                                <tr>
                                    <td><input <?php if (get_option('sw_deliveryMethod') == 'free') {
                                                    echo 'checked="checked"';
                                                } ?> type="checkbox" name="deliveryMethod" value="free" id="deliveryMethod"><?php _e('Free Delivery', 'awin-data-feed'); ?></td>
                                    <td>
                                        (<?php echo esc_html($this->processor->getFreeDeliveryProducts()) ?>)
                                    </td>
                                </tr>

                                <?php
                                if (count($this->processor->getProductCountByCategory()) > 1) { ?>
                                    <tr>
                                        <th colspan="2" class="filterType"><?php _e('By Category', 'awin-data-feed'); ?></th>
                                    </tr>
                                    <?php
                                    foreach ($this->processor->getProductCountByCategory() as $category) {
                                    ?>
                                        <tr>
                                            <td>
                                                <input <?php if (is_array(get_option('sw_categories')) && in_array($category['categoryName'], get_option('sw_categories'))) {
                                                            echo 'checked="checked"';
                                                        } ?> type="checkbox" name="categories[]" value="<?php echo esc_html($category['categoryName']) ?>"><?php echo esc_html($category['categoryName']) ?>
                                            </td>
                                            <td>
                                                <?php echo esc_html($category['count']) ?>
                                            </td>
                                        </tr>
                                <?php
                                    }
                                }
                                ?>
                                <tr>
                                    <th colspan="2" class="filterType"><?php _e('By price', 'awin-data-feed'); ?></th>
                                </tr>
                                <tr>
                                    <td><input <?php if (get_option('sw_maxPriceRadio') == '10') {
                                                    echo 'checked="checked"';
                                                } ?> class="maxPriceRadio" type="radio" name="maxPriceRadio" value="10"><?php _e('Less than 10', 'awin-data-feed'); ?></td>
                                    <td>
                                        (<?php echo esc_html($this->processor->getProductCountForPrice(10)) ?>)
                                    </td>
                                </tr>
                                <tr>
                                    <td><input <?php if (get_option('sw_maxPriceRadio') == '50') {
                                                    echo 'checked="checked"';
                                                } ?> class="maxPriceRadio" type="radio" name="maxPriceRadio" value="50"><?php _e('Less than 50', 'awin-data-feed'); ?></td>
                                    <td>
                                        (<?php echo esc_html($this->processor->getProductCountForPrice(50)) ?>)
                                    </td>
                                </tr>
                                <tr>
                                    <td><input <?php if (get_option('sw_maxPriceRadio') == '100') {
                                                    echo 'checked="checked"';
                                                } ?> class="maxPriceRadio" type="radio" name="maxPriceRadio" value="100"><?php _e('Less than 100', 'awin-data-feed'); ?></td>
                                    <td>
                                        (<?php echo esc_html($this->processor->getProductCountForPrice(100)) ?>)
                                    </td>
                                </tr>
                                <tr>
                                    <th colspan="2" class="filterType"><?php _e('By price range', 'awin-data-feed'); ?></th>
                                </tr>
                                <tr>
                                    <td><input <?php if (get_option('sw_maxPriceRadio') == 'range') {
                                                    echo 'checked="checked"';
                                                } ?> type="radio" name="maxPriceRadio" value="range" id="maxPriceRange">
                                        <input value="<?php echo esc_html(get_option('sw_minPrice')) ?>" class="range" size="3" maxlength="3" type="number" name="minPrice" placeholder="<?php _e('min', 'awin-data-feed'); ?>" readonly>
                                    </td>
                                    <td><input value="<?php echo esc_html(get_option('sw_maxPrice')) ?>" class="range" size="3" maxlength="3" type="number" name="maxPrice" placeholder="<?php _e('max', 'awin-data-feed'); ?>" readonly></td>
                                </tr>
                                <tr>
                                    <td colspan="2"><i>*<?php _e('Product without valid image will not be displayed', 'awin-data-feed'); ?></i></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="buttons">
                                        <input type="submit" name="filterOptions" id="filterOptions" class="button button-primary" value="<?php _e('Save changes', 'awin-data-feed'); ?>">
                                        <input type="button" name="resetFilters" id="resetFilters" class="button" value="<?php _e('Reset filters', 'awin-data-feed'); ?>">
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </div>
                </div>
                <div class="display">
                    <input class="button reportButton" type="button" value="<?php _e('Display Analytics', 'awin-data-feed') ?>" id="reportButton" />
                </div>

                <section id="analytics" class="analytics" style="display: none;">

                    <div class="analyticsPopular">
                        <?php
                        $analytics = $this->adapter->getPopularAnalytics();
                        ?>
                        <table class="aw-filter analytics" cellspacing="0" cellpadding="0">
                            <tr>
                                <th colspan="2">
                                    <h1> <?php _e('Popular Products', 'awin-data-feed'); ?> </h1>
                                </th>
                            </tr>
                            <tr>
                                <th><?php _e('Product', 'awin-data-feed'); ?></th>
                                <th><?php _e('Click', 'awin-data-feed'); ?></th>
                            </tr>
                            <?php
                            foreach ($analytics as $row) {
                            ?>
                                <tr>
                                    <td class="image"><a href="<?php echo esc_html($row['merchantDeepLink']) ?>" target="_blank"><img src="<?php echo esc_html($row['awImageUrl']) ?>" /> </a></td>
                                    <td><?php echo esc_html($row['count']) ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </table>
                    </div>
                    <div class="analyticsDaily">
                        <?php
                        $analytics = $this->adapter->getClickAnalytics();
                        ?>
                        <table class="aw-filter analytics" cellspacing="0" cellpadding="0">
                            <tr>
                                <th colspan="3">
                                    <h1><?php _e('User daily click', 'awin-data-feed'); ?> </h1>
                                </th>
                            </tr>
                            <tr>
                                <th class="text-center"><?php _e('Click Time', 'awin-data-feed'); ?></th>
                                <th><?php _e('IP', 'awin-data-feed'); ?></th>
                                <th><?php _e('Product', 'awin-data-feed'); ?></th>
                            </tr>
                            <?php
                            foreach ($analytics as $row) {
                            ?>
                                <tr>
                                    <td><?php echo esc_html($row['clickDateTime']) ?></td>
                                    <td><?php echo esc_html($row['clickIp']) ?></td>
                                    <td class="image"> <a href="<?php echo esc_html($row['merchantDeepLink']) ?>" target="_blank"><img src="<?php echo esc_html($row['awImageUrl']) ?>" /></a></td>
                                </tr>
                            <?php
                            }
                            ?>
                        </table>
                    </div>
                </section>
            </section>
        <?php
        }
    }

    public function render_guide()
    { ?>

        <div class="wrap">
            <h3 class="info"> <?php _e('Please refer to', 'awin-data-feed') ?>
                <a href="https://wiki.awin.com/index.php/Downloading_feeds_using_Create-a-Feed" target="_blank">
                    AWIN Downloading feeds guide
                </a>
            </h3>
            <h1><?php _e('Shortcodes', 'awin-data-feed'); ?></h1>
            <ol>
                <li>[AWIN_DATA_FEED] - <?php _e('Default shortcode', 'awin-data-feed'); ?></li>
            </ol>
            <h2><?php _e('Shortcode Options', 'awin-data-feed'); ?></h2>
            <ol>
                <li>title='<?php _e('any title in quote', 'awin-data-feed'); ?>'</li>
                <li>no_of_product=<?php _e('any number', 'awin-data-feed'); ?></li>
                <li>keywords='<?php _e('comma separated word in quote', 'awin-data-feed'); ?>'</li>
                <li>layout='<?php _e('horizontal or vertical - to show the products horizontally or vertically', 'awin-data-feed'); ?>'</li>
            </ol>
            <h2><?php _e('Shortcode Examples', 'awin-data-feed'); ?></h2>
            <ol>
                <li>[AWIN_DATA_FEED title='<?php _e('hello world', 'awin-data-feed'); ?>' no_of_product=2]</li>
                <li>[AWIN_DATA_FEED no_of_product=3 title='<?php _e('Iron Man vs Captain America', 'awin-data-feed'); ?>'
                    keywords='<?php _e('Iron Man, Captain America', 'awin-data-feed'); ?>' ]
                </li>
                <li>[AWIN_DATA_FEED layout=horizontal no_of_product=10]</li>

            </ol>
        </div>

<?php
    }
}
