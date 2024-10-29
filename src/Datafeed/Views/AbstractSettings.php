<?php

namespace Datafeed\Views;

use Datafeed\Models\WidgetDisplayProcessor as Processor;
use Datafeed\Models\Csv\UploadErrorHandler;
use Datafeed\Models\Csv\Importer;
use Datafeed\Models\OptionHandler;
use Datafeed\Models\DBAdapter;

abstract class AbstractSettings
{
    /** @var  array */
    protected $settings_page_properties;

    /** @var  Processor */
    protected $processor;

    /** @var  UploadErrorHandler */
    protected $errorHandler;

    /** @var  Importer */
    protected $importer;

    /** @var  OptionHandler */
    protected $optionHandler;

    /** * @var DBAdapter */
    protected $adapter;


    /**
     * @param \Datafeed\DBAdapter $adapter
     * @param OptionHandler $optionHandler
     * @param Importer $importer
     * @param UploadErrorHandler $handler
     * @param Processor $processor
     * @param array $settings_page_properties
     */
    public function __construct(
        DBAdapter $adapter,
        OptionHandler $optionHandler,
        Importer $importer,
        UploadErrorHandler $handler,
        Processor $processor,
        array $settings_page_properties
    ) {
        $this->adapter = $adapter;
        $this->optionHandler = $optionHandler;
        $this->importer = $importer;
        $this->errorHandler = $handler;
        $this->processor = $processor;
        $this->settings_page_properties = $settings_page_properties;
    }

    public function run()
    {
        add_action('admin_menu', array($this, 'add_menu_and_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_notices', array($this, 'datafeed_admin_notice'));
    }

    public function add_menu_and_page()
    {
        add_menu_page(
            __($this->settings_page_properties['page_title'], 'awin-data-feed'),
            __($this->settings_page_properties['menu_title'], 'awin-data-feed'),
            $this->settings_page_properties['capability'],
            $this->settings_page_properties['menu_slug'],
            array($this, 'render_settings_page'),
            $this->settings_page_properties['icon']
        );
        add_submenu_page(
            $this->settings_page_properties['parent_slug'],
            __($this->settings_page_properties['sub_menu_title'], 'awin-data-feed'),
            __($this->settings_page_properties['sub_menu_title'], 'awin-data-feed'),
            $this->settings_page_properties['capability'],
            $this->settings_page_properties['menu_slug']
        );
        add_submenu_page(
            $this->settings_page_properties['parent_slug'],
            __($this->settings_page_properties['help_menu_title'], 'awin-data-feed'),
            __($this->settings_page_properties['help_menu_title'], 'awin-data-feed'),
            $this->settings_page_properties['capability'],
            $this->settings_page_properties['help_menu_slug'],
            array($this, 'render_guide')
        );
    }

    public function register_settings()
    {
        register_setting(
            $this->settings_page_properties['option_group'],
            $this->settings_page_properties['option_name']
        );
    }

    public function datafeed_admin_notice()
    {
        if (!$this->processor->hasFeedInDb()) {
?>
            <div class="update-nag">
                <p>
                    <a href="<?php echo admin_url('admin.php?page=datafeed-settings') ?>">
                        <?php _e('Import  your affiliate window data feed to display in widget!', 'awin-data-feed') ?>
                    </a>
                </p>
            </div>
<?php
        }
    }

    public function get_settings_data()
    {
        return get_option($this->settings_page_properties['option_name'], $this->get_default_settings_data());
    }

    public function render_settings_page()
    {
    }

    public function render_guide()
    {
    }
}
