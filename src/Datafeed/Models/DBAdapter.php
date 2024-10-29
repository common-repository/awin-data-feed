<?php

namespace Datafeed\Models;

use Datafeed\Models\OptionHandler;

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
require_once('OptionHandler.php');

class DBAdapter
{
    /** @var string */
    private $dbTable = "";

    /** @var string */
    private $analyticsTable = "";

    /** @var string */
    private $tableName = "";

    /** @var  OptionHandler */
    private $handler;

    /**
     * @param OptionHandler|null $handler
     */
    public function __construct(OptionHandler $handler)
    {
        global $wpdb;

        $this->handler = $handler;
        $this->tableName = $wpdb->prefix . "datafeed";
        $this->dbTable = "`" . DB_NAME . "`.`" . $this->tableName . "`";
        $this->analyticsTable = "`" . DB_NAME . "`.`" . $wpdb->prefix . "datafeed_analytics" . "`";

        $this->createTableIfNotExist();
    }

    public function truncateTable()
    {
        $this->handler->delete_sw_options();

        global $wpdb;
        $wpdb->query("TRUNCATE TABLE " . $this->dbTable);
        $wpdb->query("TRUNCATE TABLE " . $this->analyticsTable);
    }

    /**
     * @param array $row
     *
     * @return false|int
     */
    public function insertRow($row)
    {
        global $wpdb;

        $query = "
        INSERT INTO " . $this->dbTable . "
            (
            categoryName,
            awDeepLink,
            merchantDeepLink,
            awImageUrl,
            description,
            productName,
            deliveryCost,
            currency,
            price
            )
        VALUES
            (
            '" . esc_sql($row['category_name']) . "','" .
            esc_sql($row['aw_deep_link']) . "&plugin=datafeed','" .
            esc_sql($row['merchant_deep_link']) . "','" .
            esc_sql($row['aw_image_url']) . "','" .
            esc_sql($row['description']) . "','" .
            esc_sql($row['product_name']) . "','" .
            esc_sql($row['delivery_cost']) . "','" .
            esc_sql($row['currency']) . "','" .
            esc_sql($row['search_price']) . "'
            )";

        return $wpdb->query($query);
    }

    /**
     * @param integer $limit
     * @param null $keywords
     *
     * @return array
     */
    public function getLimitedRows($limit, $keywords = null)
    {
        global $wpdb;

        $extraWhere = $this->getWhere($keywords);

        $sql = "SELECT * FROM  $this->dbTable" .
            " WHERE description !=''";
        $sql .= $extraWhere;
        $sql .= " ORDER BY RAND() LIMIT " . $limit;
        $result = $wpdb->get_results($sql, ARRAY_A);

        return $result;
    }

    /**
     * @return integer
     */
    public function countFeedInDb()
    {
        global $wpdb;
        $extraWhere = $this->getWhere();
        $sql = "SELECT COUNT(*) FROM " .  $this->dbTable . " WHERE price >= 0 " . $extraWhere;
        $result = $wpdb->get_var($sql);

        return $result;
    }

    /**
     * @return integer
     */
    public function getProductCountByFreeDeliveryCost()
    {
        global $wpdb;

        $sql = "
        SELECT SUM( amount)
        FROM
          (SELECT deliveryCost, COUNT(*) AS amount
           FROM $this->dbTable
           GROUP BY deliveryCost
           HAVING deliveryCost<1) getCount;
        ";
        $result = $wpdb->get_var($sql);

        return $result;
    }

    /**
     * @return integer
     */
    public function getProductCountByCategory()
    {
        global $wpdb;

        $sql = "SELECT
                  categoryName,
                COUNT(*) as count
                FROM  $this->dbTable
                GROUP BY categoryName
                HAVING count > 20
                ORDER BY count DESC ;";
        $result = $wpdb->get_results($sql, ARRAY_A);

        return $result;
    }

    /**
     * @param integer $price
     *
     * @return integer
     */
    public function getProductCountByPrice($price)
    {
        global $wpdb;

        $sql = "SELECT COUNT(*) as amount FROM $this->dbTable where price < $price;";
        $result = $wpdb->get_var($sql);

        return $result;
    }

    /**
     * @param array $row
     */
    public function saveAnalytics(array $row)
    {
        global $wpdb;

        $insertOrUpdate = "
        INSERT INTO " . $this->analyticsTable . "
            (clickIp, clickDateTime, feed)
        VALUES
            (
            '"  . esc_sql($row['clickIp']) . "','"
            . esc_sql($row['clickDateTime']) . "','"
            . esc_sql($row['feedId']) . "'
            )
        ON DUPLICATE KEY UPDATE
            clickIp = '"            . esc_sql($row['clickIp']) . "',
            clickDateTime = '"      . esc_sql($row['clickDateTime']) . "',
            feed = '"               . esc_sql($row['feedId']) . "';";

        $wpdb->query($insertOrUpdate);
    }

    /**
     * @return array
     */
    public function getClickAnalytics()
    {
        global $wpdb;

        $sql = "
            SELECT DISTINCT clickDateTime, clickIp, df.awImageUrl, df.merchantDeepLink
            FROM " . $this->analyticsTable . " da
            JOIN " . $this->tableName . " df
            ON df.id = da.feed
            WHERE DATE(clickDateTime) = CURDATE() ORDER BY clickDateTime DESC LIMIT 20;
        ";
        $result = $wpdb->get_results($sql, ARRAY_A);

        return $result;
    }

    /**
     * @return array
     */
    public function getPopularAnalytics()
    {
        global $wpdb;

        $sql = "
            SELECT feed AS product, COUNT(*) AS count, df.awImageUrl, df.merchantDeepLink
            FROM " . $this->analyticsTable . " da
            JOIN " . $this->tableName . " df
            ON df.id = da.feed
            GROUP BY product ORDER BY count DESC LIMIT 20;
        ";
        $result = $wpdb->get_results($sql, ARRAY_A);

        return $result;
    }

    /**
     * @param null $keywords
     *
     * @return string
     */
    private function getWhere($keywords = null)
    {
        $where = "";
        if (get_option('sw_deliveryMethod') == 'free') {
            $where .= "AND (deliveryCost=0 OR deliveryCost='') ";
        }

        $categories = get_option('sw_categories');
        if ($categories) {
            $where .= "AND categoryName in (\"" . implode('","', esc_sql($categories)) . "\") ";
        }

        $maxPriceRadio = get_option('sw_maxPriceRadio');
        if ($maxPriceRadio == "range") {
            $min = $this->getMinPrice();
            $max = $this->getMaxPrice();
            $where .= "AND price between $min AND $max ";
        } else {
            $max = (int)$maxPriceRadio;
            if ($max > 0) {
                $where .= "AND price < $max";
            }
        }

        if (!empty($keywords)) {
            $array_name = explode(',', $keywords);
            foreach ($array_name as $val) {
                $query_parts[] = "'%" . esc_sql($val) . "%'";
            }
            $stringLike = " AND productName LIKE";
            $stringLike .= implode(' OR productName LIKE ', $query_parts);
            $where .= $stringLike;
        }

        return $where;
    }

    /**
     * create necessary db tables
     */
    private function createTableIfNotExist()
    {
        $this->createFeedTable();
        $this->createAnalyticsTable();
    }

    private function createFeedTable()
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE " . $this->dbTable . "(
                  id int(11) NOT NULL AUTO_INCREMENT,
                  categoryName varchar(45) DEFAULT NULL,
                  awDeepLink varchar(500) DEFAULT NULL,
                  merchantDeepLink varchar(500) DEFAULT NULL,
                  awImageUrl varchar(500) DEFAULT NULL,
                  description text CHARACTER SET utf8mb4,
                  productName varchar(255) DEFAULT NULL,
                  deliveryCost varchar(255) DEFAULT NULL,
                  currency varchar(11) DEFAULT NULL,
                  price varchar(15) DEFAULT NULL,
                  PRIMARY KEY (id),
                  UNIQUE KEY id_UNIQUE (id)
                ) $charset_collate;";

        $wpdb->get_var("SHOW TABLES LIKE '" . $this->tableName . "'");
        if ($wpdb->num_rows != 1) {
            dbDelta($sql);
        }
    }

    private function createAnalyticsTable()
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "
          CREATE TABLE IF NOT EXISTS " . $this->analyticsTable . " (
          id int(11) NOT NULL AUTO_INCREMENT,
          clickIp varchar(45) NOT NULL,
          clickDateTime datetime DEFAULT NULL,
          feed int(11) DEFAULT NULL,
          PRIMARY KEY (id)
        ) $charset_collate;";

        $wpdb->get_var("SHOW TABLES LIKE '" . $this->analyticsTable . "'");
        if ($wpdb->num_rows != 1) {
            dbDelta($sql);
        }
    }

    private function getMinPrice()
    {
        $minPrice = get_option('sw_minPrice');

        if (empty($minPrice)) {
            global $wpdb;

            $sql = "SELECT min(price) FROM $this->dbTable";
            $minPrice = $wpdb->get_var($sql);
        }

        delete_option('sw_minPrice');
        add_option('sw_minPrice', $minPrice);

        return $minPrice;
    }

    private function getMaxPrice()
    {
        $maxPrice = get_option('sw_maxPrice');

        if (empty($maxPrice)) {
            global $wpdb;

            $sql = "SELECT max(price) FROM $this->dbTable";
            $maxPrice = $wpdb->get_var($sql);
        }

        delete_option('sw_maxPrice');
        add_option('sw_maxPrice', $maxPrice);

        return $maxPrice;
    }
}
