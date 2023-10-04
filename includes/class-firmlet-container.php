<?php
/**
 *  Copyright since 2007 PrestaShop SA and Contributors
 *  PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *  *
 *  NOTICE OF LICENSE
 *  *
 *  This source file is subject to the Academic Free License version 3.0
 *  that is bundled with this package in the file LICENSE.md.
 *  It is also available through the world-wide-web at this URL:
 *  https://opensource.org/licenses/AFL-3.0
 *  If you did not receive a copy of the license and are unable to
 *  obtain it through the world-wide-web, please send an email
 *  to license@prestashop.com so we can send you a copy immediately.
 *  *
 *  @author    PrestaShop SA and Contributors <contact@prestashop.com>
 *  @copyright Since 2007 PrestaShop SA and Contributors
 *  @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

define('VOSFACTURES_ERR_OK', '');
define('VOSFACTURES_ERR_EMPTY_CONFIGURATION', 'configuration_is_empty');
define('VOSFACTURES_ERR_OVERRIDING_DISABLED', 'overriding_is_disabled');
define('VOSFACTURES_ERR_INVOICES_DISABLED', 'invoices_are_disabled');
define('VOSFACTURES_ERR_ACCOUNT_NOT_PRO', 'account_not_pro');
define('VOSFACTURES_ERR_INVALID_API_TOKEN', 'api_token_incorrect');
define('VOSFACTURES_ERR_INVALID_CONFIGURATION', 'invalid_module_configuration');
define('VOSFACTURES_ERR_FIRMLET_IS_DOWN', 'firmlet_is_down');

class VosFacturesAppContainer
{
    /**
     * Contains all fields provided in settings
     * that will be saved in the database.
     * 'key'   -> option key in database
     * 'value' -> array(
     *               -> 'type' from [string, bool...]
     *               -> 'default' - default value provided for that key
     *
     * @since 2.2.5
     * @since 2.3.0 - show_my_invoices on client's side in FT/BF/IO
     * @since 2.3.2 - exported 'show_my_invoices', 'auto_issue' and 'issue_kind'
     *                to $fields_without_override
     * @since 2.3.5 - added 'use_carrier_name' and 'override_carrier_name'
     *                and 'category_id'
     * @var array
     */
    private static $fields = array(
        // Firmlet API Token
        'api_token'             => array('type' => 'string',  'default' => ''),
        // Issue invoices automatically: ['disabled', 'order_creation', 'order_paid', 'order_shipped']
        'auto_issue'         => array('type' => 'string',  'default' => 'order_paid'),
        // Are invoices sent automatically (from Firmlet)?
        'auto_send'             => array('type' => 'bool',    'default' => false),
        // Include free shipment on an invoice?
        'incl_free_shipment'    => array('type' => 'bool',    'default' => false),
        // Fill default descriptions on issued invoice?
        'fill_default_desc'     => array('type' => 'bool',    'default' => true),
        // Firmlet Department ID (0 -> default departament)
        'department_id'         => array('type' => 'int',     'default' => 0),
        // Firmlet Category ID (0 -> default category)
        'category_id'           => array('type' => 'int',     'default' => 0),
        // Additional fields in json - empty == none
        'additional_fields'     => array('type' => 'string',  'default' => ''),
        // The most recent error kept in database.
        'last_error'            => array('type' => 'string',  'default' => ''),
        // Use carrier name instead of 'Shipping', eg. 'UPC' or 'FedEx'
        'use_carrier_name'      => array('type' => 'bool',    'default' => false),
        // If something is present, will use exactly this for shipping position
        'override_carrier_name' => array('type' => 'string',  'default' => ''),
        // Display buyer name as ['company', 'full_name', 'company_and_full_name']
        'company_or_full_name'  => array('type' => 'string',  'default' => 'company'),
        // Include delivery date on issued invoice?
        'incl_delivery_date'    => array('type' => 'bool',    'default' => false),
        // Identify OSS invoices?
        'identify_oss'          => array('type' => 'bool',    'default' => false),
        // Show order messages?
        'show_order_messages'   => array('type' => 'bool',    'default' => false),
        // Force zero tax?
        'force_vat'             => array('type' => 'bool',    'default' => false),
        // Include private note on issued invoice?
        'incl_private_note'    => array('type' => 'bool',    'default' => false),
        // Show on invoice product description from VF
        'prod_desc_from_vf'     => array('type' => 'bool',    'default' => false),
        // Module Version
        'version'               => array('type' => 'string',  'default' => '2.4.14'),
    );

    /**
     * Fields available in modules, that use the override classes features
     * Used in VosFactures
     *
     * @since 2.3.2 - introduced
     * @var array
     */
    private static $fields_with_override = array();

    /**
     * Fields available in modules, that do not use the override classes features
     * Used in InvoiceOcean/Fakturownia/Bitfactura
     *
     * @since 2.3.2 - extracted 3 fields from $fields
     * @var array
     */
    private static $fields_without_override = array(
        // Issue invoices automatically: ['disabled', 'order_creation', 'order_paid', 'order_shipped']
        'auto_issue'         => array('type' => 'string',  'default' => 'disabled'),
        // What kind of invoices to issue automatically: ['vat_or_receipt', always_[vat|proforma|estimate|bill]]
        'issue_kind'         => array('type' => 'string',  'default' => 'vat_or_receipt'),
        // Show my invoices on client's side
        'show_my_invoices'   => array('type' => 'bool',    'default' => true),
    );


    /**
     * Prefix for all configuration keys.
     *
     * @since 2.2.5
     * @var string
     */
    private static $prefix = 'VOSFACTURES_';


    /**
     * Returns database key based on $key
     *
     * @since 2.2.5
     * @param string $key Key to be appended to prefix
     * @return string Correct database key for configuration
     */
    public function getDbKey($key)
    {
        return Tools::strtoupper(self::$prefix . $key);
    }

    /**
     * Returns all required keys with values for specific firmlet
     *
     * @since 2.3.2
     * @return array Associative array with firmlet specific keys
     */
    private function getFields()
    {
        $module = & VosFacturesApp::getInstance();
        if ($module->correctFirmlet('FT', 'BF', 'IO')) {
            return array_merge(self::$fields, self::$fields_without_override);
        } else {
            return array_merge(self::$fields, self::$fields_with_override);
        }
    }

    /**
     * Returns all container keys
     *
     * @since 2.3.5
     */
    public function getFieldKeys()
    {
        return array_keys($this->getFields());
    }

    /**
     * Constructor for container class.
     *
     * Afterwards will create public attributes based on self::$fields
     *
     * NOTE: When constructing, send $id_lang as null, unless a new field
     *       gets added to the settings, that depends on translation.
     *
     * @since 2.2.5
     * @param mixed $settings Array with Settings ().
     * @param integer $id_lang Language ID
     * @param integer $id_shop Shop ID
     * @param integer $id_shop_group ShopGroup ID
     */
    public function __construct($settings = null, $id_lang = null, $id_shop = null, $id_shop_group = null)
    {
        if (is_null($settings)) {
            $settings = array();
            foreach ($this->getFields() as $key => $value) {
                $value = Configuration::get($this->getDbKey($key), $id_lang, $id_shop_group, $id_shop);
                if ($value !== false) {
                    // Configuration::get() returns false if not found
                    $settings[$key] = $value;
                }
            }
        } elseif (gettype($settings) !== 'array') {
            error_log("Settings are incorrect!");
            exit();
        }

        $settings = $this->fillUnsetValues($settings);
        $settings = $this->unsetUndefinedValues($settings);
        $this->constructFromArray($settings);
    }


    // todo: jest odpalane 2 razy, dunno why
    public function isConfigured()
    {
        $success = !empty($this->api_token) && isset($this->auto_send);
        $module = & VosFacturesApp::getInstance();
        if ($module->correctFirmlet('FT', 'BF', 'IO')) {
            $success = $success && isset($this->auto_issue) && isset($this->issue_kind);
        }
        return $success;
    }


    /**
     * Run when the module gets installed - updates all Configuration values.
     * @since 2.2.5
     */
    public function install()
    {
        foreach ($this->getFields() as $key => $value) {
            Configuration::updateValue($this->getDbKey($key), $value['default']);
        }
    }


    /**
     * Run when the module gets uninstalled - removes all Configuration values.
     * @since 2.2.5
     */
    public function uninstall()
    {
        foreach (array_keys($this->getFields()) as $key) {
            Configuration::deleteByName($this->getDbKey($key));
        }
    }

    public function getUpdatedFields($container2)
    {
        $updated_fields = array();

        foreach ($this->getFieldKeys() as $key) {
            if ($this->{$key} != $container2->{$key}) {
                $updated_fields[$key] = $container2->{$key};
            }
        }

        return $updated_fields;
    }


    /**
     * Run when the module gets updated (in configuration) - updates Configurations values.
     * @since 2.2.5
     */
    public function updateConfiguration()
    {
        foreach (array_keys($this->getFields()) as $key) {
            Configuration::updateValue($this->getDbKey($key), $this->{$key});
        }
    }


    /**
     * Constructs public container attributes based on settings key
     *
     * Providing "$settings['example']" will be evaluated to
     * "$this->example = $settings['example']", which follows DRY principle.
     *
     * @param array $settings Contains keys and values
     * @since 2.2.5
     */
    private function constructFromArray($settings)
    {
        $fields = $this->getFields();
        foreach (array_keys($fields) as $key) {
            $this->{$key} = $settings[$key];

            if ($fields[$key]['type'] === 'int') {
                // cast to int. Anything without a digit will evaluate to 0
                $this->{ $key } = (int) $this->{ $key };
            } elseif ($fields[$key]['type'] === 'bool') {
                $this->{ $key } = (bool) $this->{ $key };
            }
        }
    }


    /**
     * Fills values that are defined in self::$fields, but
     * undefined in $settings
     *
     * @since 2.2.5
     * @param array $settings settings to fill unset values
     * @return array $settings settings with all values set
     */
    private function fillUnsetValues($settings)
    {
        foreach ($this->getFields() as $field => $value) {
            if (!array_key_exists($field, $settings)) {
                /**
                 * @since 2.3.0 - isset(null) -> false and we want to make sure
                 *                that variable is UNDEFINED
                 */
                $settings[$field] = $value['default'];
            }
        }
        return $settings;
    }


    /**
     * Unsets values that are undefined in self::$fields.
     *
     * @since 2.2.5
     * @param array $settings settings to unset if necessary
     * @return array $settings unset settings
     */
    private function unsetUndefinedValues($settings)
    {
        $field_keys = array_keys($this->getFields());
        $settings_keys = array_keys($settings);

        foreach ($settings_keys as $key) {
            if (!in_array($key, $field_keys)) {
                unset($settings[$key]);
            }
        }
        return $settings;
    }


    /**
     * Returns container as an array.
     *
     * @since 2.2.5
     * @return array Container as an array
     */
    public function asArray()
    {
        $field_keys = $this->getFieldKeys();

        $array_to_return = array();
        foreach ($field_keys as $key) {
            $array_to_return[$key] = $this->{$key};
        }

        return $array_to_return;
    }
}
