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

if (!defined( '_PS_VERSION_')) {
    exit;
}


define('VOSFACTURES_DEBUG', 0);

require 'vendor/autoload.php';

class VosFacturesApp extends Module
{
    const API_URL         = 'vosfactures.fr';
    const NEW_ACCOUNT_URL = 'http://app.vosfactures.fr/account/new';
    const HELP_URL        =
        'http://aide.vosfactures.fr/929756-Module-Prestashop-Int-grer-votre-compte-Prestashop-avec-VosFacture...';

    /**
     * Container for all required options for this module to work correctly.
     *
     * @var Firmlet Container
     * @since 2.2.5
     */
    public $c;

    /**
     * Instance to be returned in self::get_instance
     * @since 2.2.5
     */
    private static $instance;

    /**
     * @var PrestaShop\ModuleLibServiceContainer\DependencyInjection\ServiceContainer
     */
    private $container;

    /**
     * Returns a reference to static module in order not to copy
     * this class whenever this is called.
     *
     * @note Calling this function should be done by " & self::get_instance() "
     * @since 2.2.5
     * @return object This class
     */
    public static function & getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct()
    {
        self::$instance = $this;
        $this->name       = 'vosfacturesapp';
        $this->tab        = 'billing_invoicing';
        $this->author     = 'VosFactures';
        $this->version    = '2.4.14';
        $this->module_key = '5bbaca810d0f3274b91718909512d431';
        $this->firmlet    = 'VF';
        $this->bootstrap  = true;
        // Presta compares differently in different versions
        // <  1.6: min <= _PS_VERSION_ <  max
        // >= 1.6: min <= _PS_VERSION_ <= max
        $this->ps_versions_compliancy = array(
            'min' => '1.6.1',
            'max' => _PS_VERSION_
        );

        if ($this->correctFirmlet('BF', 'FT')) {
            // Front controllers are available only in bitfactura/fakturownia
            $this->controllers = array('invoices');
        }

        parent::__construct();

	    if ($this->container === null) {
		    $this->container = new PrestaShop\ModuleLibServiceContainer\DependencyInjection\ServiceContainer(
			    $this->name,
			    $this->getLocalPath()
		    );
	    }

        $this->init();
    }


    /**
     * Checks if $this->firmlet is in a list of permitted firmlets to be run
     * @example correctFirmlet('FT', 'BF');
     *
     * @since 2.2.8
     * @param mixed Permitted Firmlet (string or array)
     * @param string ...
     * @return bool is this firmlet in a list?
     */
    public function correctFirmlet()
    {
        $args = func_get_args();
        return in_array(
            $this->firmlet,
            gettype(reset($args)) === 'array' ? reset($args) : $args
        );
    }



    private function getNotices()
    {
        return array(
            "errors" => array(
                1 => "Error 1",
                2 => "Error 2",
            ),
            "warnings" => array(
                1 => "Warning 1",
                2 => "Warning 2",
            ),
            "informations" => array(
                1 => "Information 1",
                2 => "Information 2",
            ),
            "confirmations" => array(
                1 => $this->l('conf_invoice_created'),
                2 => $this->l('conf_invoice_removed'),
            ),
        );
    }


    /**
     * Prepares notification for user based on get param.
     * @since 2.2.8
     */
    private function prepareDialogs()
    {
        $conf = (int) Tools::getValue('vosfacturesapp_conf');
        if (empty($conf)) {
            return;
        }

        $admin_obj = Context::getContext()->controller;
        if ($admin_obj instanceof AdminController) {
            $notices = $this->getNotices();

            switch (floor($conf / 100)) {
                case 1:
                    $type = "errors";
                    break;
                case 2:
                    $type = "warnings";
                    break;
                case 3:
                    $type = "informations";
                    break;
                case 4:
                    $type = "confirmations";
                    break;
                default:
                    return;
            }

            if (!empty($notices[$type][$conf % 100])) {
                $admin_obj->{$type}[] = $notices[$type][$conf % 100];
            }
        }
    }


    private function init()
    {
        include_once('includes/class-firmlet-container.php');
        include_once('includes/class-firmlet-invoice.php');
        include_once('includes/class-firmlet-database.php');

        $this->c = new VosFacturesAppContainer();
        $this->db = new VosFacturesAppDatabase();
        $this->displayName = 'VosFactures';
        $this->description = $this->l('description_short');

        if (!$this->c->isConfigured()) {
            $this->warning = $this->l('empty_configuration_warning');
        } else {
            try {
                $this->registerShopUuid();
            } catch (Exception $e) {
            }
        }

        if ($this->correctFirmlet('VF')) {
            // We override classes in vosfactures
            if ($this->isOverridingDisabled()) {
                $this->warning = $this->l('overriding_is_disabled');
            } elseif (!Configuration::get('PS_INVOICE')) {
                $this->warning = $this->l('invoices_are_disabled');
            }
        }

        //$this->prepareDialogs();
    }

    private function registerShopUuid()
    {
        if (Configuration::get('VOSFACTURES_UUID_SAVED')) {
            return;
        }

        if ($this->c->api_token == '') {
            return;
        }

        $accountsFacade  = $this->getService('ps_accounts.facade');
        $accountsService = $accountsFacade->getPsAccountsService();
        $shopUuid        = $accountsService->getShopUuid();

        if ($shopUuid == '') {
            return;
        }

        $data = [
            'api_token' => $this->c->api_token,
            'account'   => [
                'prestashop_shop_id' => $shopUuid,
            ],
        ];

        $this->makeRequest($this->getAccountUrlJson(), 'PATCH', $data);

        $this->addLog('Uuid saved on VosFactures ('.$shopUuid.')');

        Configuration::updateValue('VOSFACTURES_UUID_SAVED', true);
    }

    public function install()
    {
        if (_PS_VERSION_ < '1.5') {
            $this->_errors[] = $this->displayError($this->l('incompatible_version'));
            return false;
        }

        // Update Configuration values by Container.
        $this->c->install();
        $this->db->installDatabase();

        $success = parent::install()
            && $this->installtab()
            && $this->registerHook('actionOrderStatusPostUpdate');

        if (_PS_VERSION_ < '1.6') {
            $success = $success && $this->registerHook('adminOrder');
        } else {
            $success = $success
                && $this->registerHook('displayAdminOrderTabOrder')
                && $this->registerHook('displayAdminOrderContentOrder')
                && $this->registerHook('displayBackOfficeHeader');
        }

        if (version_compare(_PS_VERSION_, '1.7.7', '>=')) {
            $success = $success &&
                $this->registerHook('displayAdminOrderTabLink') &&
                $this->registerHook('displayAdminOrderTabContent');
        }

        if ($this->correctFirmlet('BF', 'FT')) {
            $success = $success &&
                $this->registerHook('displayCustomerAccount') &&
                $this->registerHook('displayHeader');
        }

        $success = $success && $this->getService('ps_accounts.installer')->install();

        $this->addLog('Install: ' . ($success ? 'success' : 'failure'));
        return $success;
    }

    /**
     * @param string $serviceName
     * @return mixed
     */
    public function getService($serviceName)
    {
        return $this->container->getService($serviceName);
    }

    public function uninstall()
    {
        $this->c->uninstall();
        $success = parent::uninstall() && $this->uninstallTab();
        $this->addLog('Uninstall: ' . ($success ? 'success' : 'failure'));
        return $success;
    }


    /**
     * Installs Admin Controller for Firmlet (guarantees security)
     *
     * @since 2.2.5
     */
    public function installTab()
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->name = array();
        $tab->class_name = 'AdminVosFacturesApp';
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'VosFacturesApp';
        }
        $tab->id_parent = -1;
        $tab->module = $this->name;
        return $tab->add();
    }

    /**
     * Uninstalls Admin Controller for Firmlet (guarantees security)
     *
     * @since 2.2.5
     */
    public function uninstallTab()
    {
        $id_tab = (int) Tab::getIdFromClassName('AdminVosFacturesApp');
        if ($id_tab) {
            $tab = new Tab($id_tab);
            return $tab->delete();
        }
        return true;
    }

    /**
     * Verifies if classes/controllers overriding is disabled.
     * Note that VosFactures version overrides Order and PDF class, therefore
     * this has to return "false"
     *
     * @since 2.2.0
     * @return bool is class/controller overriding disabled?
     */
    public function isOverridingDisabled()
    {
        return Configuration::get('PS_DISABLE_OVERRIDES');
    }

    private function postValidation()
    {
        if (Tools::isSubmit('vosfacturesapp_basic')) {
            if ($this->correctFirmlet('VF') && $this->isOverridingDisabled()) {
                $this->_postErrors[] = $this->overridingDisabledMessage();
            } else {
                if (!Tools::getValue('api_token')) {
                    $this->_postErrors[] = $this->l('api_token_required');
                } elseif (Tools::getValue('api_token') != $this->c->api_token ||
                       Tools::getValue('department_id') != $this->c->department_id ||
                       Tools::getValue('category_id') != $this->c->category_id ||
                       Tools::getValue('additional_fields') != $this->c->additional_fields) {
                    if (!$this->testIntegration1()) {
                        $this->_postErrors[] = $this->l('connection_test1_failed');
                    } elseif (!$this->testIntegration2()) {
                        $this->_postErrors[] = $this->l('connection_test2_failed');
                    } elseif (!$this->testIntegration2b()) {
                        $this->_postErrors[] = $this->l('connection_test2b_failed');
                    } elseif (!$this->testIntegration3()) {
                        $this->_postErrors[] = $this->l('connection_test3_failed');
                    }

                    assert($this->validateFields($this->c->additional_fields)); // asercja na poprzednie ustawienia
                    $additional_fields = $this->getAdditionalFields(Tools::getValue('additional_fields'));
                    if ($additional_fields == null) {
                        $this->_postErrors[] = $this->l('additional_fields_parse_failed');
                    } elseif (!$this->validateFields(Tools::getValue('additional_fields'))) {
                        $this->_postErrors[] = $this->l('additional_fields_illegal_params');
                    }
                }
            }
        }
    }

    private function postProcess()
    {
        if (Tools::isSubmit('vosfacturesapp_basic')) {
            // Sanitize settings
            $post = $_POST; // copied, $_POST won't be modified
            $post['api_token'] = trim($post['api_token']);

            if (!empty($post['override_carrier_name'])) {
                $post['override_carrier_name'] = trim($post['override_carrier_name']);
            }

            // Update settings
            $post_container = new VosFacturesAppContainer($post);
            $post_container->updateConfiguration();

            // Log updated fields
            $this->addLog("Configuration was updated");
            $this->addLog($this->c->getUpdatedFields($post_container));

            // Post Settings update
            if ($this->correctFirmlet('BF', 'FT')) {
                $issue_kinds = array('always_vat', 'always_proforma', 'always_estimate', 'always_bill');
                if (!in_array($post_container->issue_kind, $issue_kinds)) {
                    $this->enableReceipts();
                }
            }
        }
    }

    /**
     * Returns context for debug.tpl
     *
     * @since 2.2.5
     * @return array $debug_context
     */
    private function getDebugContext()
    {
        $debug_context = array('debug' => VOSFACTURES_DEBUG);
        if (VOSFACTURES_DEBUG) {
            $debug_context['shop_data'] = $this->getCurrentShopData();

            $id_order = Tools::getValue('id_order');
            if ($id_order) {
                $debug_context['order_data'] = $this->getOrderShopData($id_order);
            }
        }
        return $debug_context;
    }

    /**
     * Returns current shop data (group etc) for multistore purposes
     * @return array
     */
    private function getCurrentShopData()
    {
        $shop = new Shop(Shop::getContextShopID(true));
        $shop_group = new ShopGroup(Shop::getContextShopGroupID(true));
        $shop_container = $this->c;
        return array(
            'shop'            => $shop,
            'shop_name'       => $shop->name,
            'shop_group'      => $shop_group,
            'shop_group_name' => $shop_group->name,
            'container'       => $shop_container,
            'is_configured'   => $shop_container->isConfigured(),
        );
    }


    /**
     * Returns order's shop data (group etc) for multistore purposes
     * @param int $id_order Order ID
     * @return array
     */
    private function getOrderShopData($id_order)
    {
        if (empty($id_order)) {
            return false;
        }
        $order            = new Order($id_order);
        $order_shop       = new Shop($order->id_shop);
        $order_shop_group = new ShopGroup($order->id_shop_group);
        $order_container  = new VosFacturesAppContainer(null, null, $order->id_shop, $order->id_shop_group);
        return array(
            'shop'            => $order_shop,
            'shop_name'       => $order_shop->name,
            'shop_group'      => $order_shop_group,
            'shop_group_name' => $order_shop_group->name,
            'container'       => $order_container,
            'is_configured'   => $order_container->isConfigured()
        );
    }

    /**
     * Returns basic settings input configuration
     * @since 2.2.8
     */
    private function getFormInputBasic($is_configured)
    {
        $input = array();
        // Api token
        $input[] = array(
            'type' => 'text',
            'name' => 'api_token',
            'label' => $this->l('api_token_label'),
            'hint' => $this->l('api_token_hint'),
            'required' => true,
            'size' => 50
        );
        if ($is_configured) {
            // Auto issue
            $input[] = array(
                'type' => 'select',
                'name' => 'auto_issue',
                'label' => $this->l('auto_issue_label'),
                'hint' => $this->l('auto_issue_hint'),
                'options' => array(
                    'query' => array(
                        array('id' => 'disabled',        'name' => $this->l('auto_issue_manually')),
                        array('id' => 'order_creation',  'name' => $this->l('auto_issue_order_creation')),
                        array('id' => 'order_paid',      'name' => $this->l('auto_issue_order_paid')),
                        array('id' => 'order_shipped',      'name' => $this->l('auto_issue_order_shipped')),
                    ),
                    'id' => 'id',
                    'name' => 'name'
                )
            );
            if ($this->correctFirmlet('BF', 'FT')) {
                // Issue kind
                $input[] = array(
                    'type' => 'select',
                    'name' => 'issue_kind',
                    'label' => $this->l('issue_kind_label'),
                    'options' => array(
                        'query' => array(
                            array('id' => 'vat_or_receipt',   'name' => $this->l('vat_or_receipt')),
                            array('id' => 'always_vat',       'name' => $this->l('always_vat')),
                            array('id' => 'always_receipt',   'name' => $this->l('always_receipt')),
                            array('id' => 'always_proforma',  'name' => $this->l('always_proforma')),
                            array('id' => 'always_estimate',  'name' => $this->l('always_estimate')),
                            array('id' => 'always_bill',      'name' => $this->l('always_bill')),
                        ),
                        'id' => 'id',
                        'name' => 'name'
                    )
                );
            }

            // Auto send
            $input[] = array(
                'type' => (_PS_VERSION_ < '1.6' ? 'radio' : 'switch'),
                'label' => $this->l('auto_send_label'),
                'hint' => $this->l('auto_send_hint'),
                'name' => 'auto_send',
                'class' => 't',
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('enabled'),
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('disabled'),
                    )
                )
            );
        }

        return $input;
    }

    /**
     * Returns advanced settings input configuration
     * @since 2.2.8
     * @since 2.3.5 category_id, use_carrier_name and override_carrier_name, company_or_full_name
     */
    private function getFormInputAdvanced()
    {
        $input = array();

        // Department
        $post_api_token = Tools::htmlentitiesUTF8(Tools::getValue('api_token'));
        $db_api_token = isset($this->c->api_token) ? $this->c->api_token : null;
        if ($post_api_token && empty($this->_postErrors)) {
            $depts_from_post = $this->getDepartments($post_api_token);
            $depts = (empty($depts_from_post) ? $this->getDepartments($db_api_token) : $depts_from_post);
        } else {
            $depts= $this->getDepartments($db_api_token);
        }
        $departments = array(array('id' => 0, 'name' => '---'));
        foreach ($depts as $dep) {
            $departments[] = array('id' => $dep->id, 'name' => $dep->shortcut);
        }
        $input[] = array(
            'type' => 'select',
            'label' => $this->l('department_label'),
            'hint' => $this->l('department_hint'),
            'name' => 'department_id',
            'options' => array(
                'query' => $departments,
                'id' => 'id',
                'name' => 'name'
            )
        );

        // Categories
        if ($post_api_token && empty($this->_postErrors)) {
            $cats_from_post = $this->getCategories($post_api_token);
            $cats = (empty($depts_from_post) ? $this->getCategories($db_api_token) : $cats_from_post);
        } else {
            $cats = $this->getCategories($db_api_token);
        }
        $categories = array(array('id' => 0, 'name' => '---'));
        foreach ($cats as $cat) {
            $categories[] = array('id' => $cat->id, 'name' => $cat->name);
        }
        $input[] = array(
            'type' => 'select',
            'label' => $this->l('category_label'),
            'hint' => $this->l('category_hint'),
            'name' => 'category_id',
            'options' => array(
                'query' => $categories,
                'id' => 'id',
                'name' => 'name'
            )
        );

        // Include Free Shipment
        $input[] = array(
            'type' => (_PS_VERSION_ < '1.6' ? 'radio' : 'switch'),
            'label' => $this->l('include_free_shipment'),
            'hint' => $this->l('include_free_shipment_hint'),
            'name' => 'incl_free_shipment',
            'class' => 't',
            'is_bool' => true,
            'values' => array(
                array(
                    'id' => 'active_on',
                    'value' => 1,
                    'label' => $this->l('enabled'),
                ),
                array(
                    'id' => 'active_off',
                    'value' => 0,
                    'label' => $this->l('disabled'),
                )
            )
        );

        // Fill default descriptions
        $input[] = array(
            'type' => (_PS_VERSION_ < '1.6' ? 'radio' : 'switch'),
            'label' => $this->l('fill_default_descriptions'),
            'hint' => $this->l('fill_default_descriptions_hint'),
            'class' => 't',
            'name' => 'fill_default_desc',
            'is_bool' => true,
            'values' => array(
                array(
                    'id' => 'active_on',
                    'value' => 1,
                    'label' => $this->l('enabled'),
                ),
                array(
                    'id' => 'active_off',
                    'value' => 0,
                    'label' => $this->l('disabled'),
                )
            )
        );

        // Use carrier name
        $input[] = array(
            'type' => (_PS_VERSION_ < '1.6' ? 'radio' : 'switch'),
            'label' => $this->l('use_carrier_name_label'),
            'class' => 't',
            'name' => 'use_carrier_name',
            'hint' => $this->l('use_carrier_name_hint'),
            'is_bool' => true,
            'values' => array(
                array(
                    'id' => 'active_on',
                    'value' => 1,
                    'label' => $this->l('enabled'),
                ),
                array(
                    'id' => 'active_off',
                    'value' => 0,
                    'label' => $this->l('disabled'),
                )
            )
        );

        // Override carrier name
        $input[] = array(
            'type' => 'text',
            'name' => 'override_carrier_name',
            'label' => $this->l('override_carrier_name_label'),
            'hint' => $this->l('override_carrier_name_hint'),
            'size' => 50
        );

        $input[] = array(
            'type' => 'select',
            'name' => 'company_or_full_name',
            'label' => $this->l('company_or_full_name_label'),
            'hint' => $this->l('company_or_full_name_hint'),
            'options' => array(
                'query' => array(
                    array('id' => 'company',               'name' => $this->l('company')),
                    array('id' => 'full_name',             'name' => $this->l('full_name')),
                    array('id' => 'company_and_full_name', 'name' => $this->l('company_and_full_name')),
                ),
                'id' => 'id',
                'name' => 'name'
            )
        );

        // Include Delivery Date
        $input[] = array(
            'type' => (_PS_VERSION_ < '1.6' ? 'radio' : 'switch'),
            'label' => $this->l('include_delivery_date'),
            'name' => 'incl_delivery_date',
            'class' => 't',
            'is_bool' => true,
            'hint' => $this->l('include_delivery_date_hint'),
            'values' => array(
                array(
                    'id' => 'active_on',
                    'value' => 1,
                    'label' => $this->l('enabled'),
                ),
                array(
                    'id' => 'active_off',
                    'value' => 0,
                    'label' => $this->l('disabled'),
                )
            )
        );

        // Include Private Note
        $input[] = array(
            'type' => (_PS_VERSION_ < '1.6' ? 'radio' : 'switch'),
            'label' => $this->l('include_private_note'),
            'name' => 'incl_private_note',
            'class' => 't',
            'is_bool' => true,
            // 'hint' => $this->l('include_private_note_hint'),
            'values' => array(
                array(
                    'id' => 'active_on',
                    'value' => 1,
                    'label' => $this->l('enabled'),
                ),
                array(
                    'id' => 'active_off',
                    'value' => 0,
                    'label' => $this->l('disabled'),
                )
            )
        );

        // show on invoice product description from VF
        $input[] = array(
            'type' => (_PS_VERSION_ < '1.6' ? 'radio' : 'switch'),
            'label' => $this->l('prod_desc_from_vf'),
            'class' => 't',
            'name' => 'prod_desc_from_vf',
            'is_bool' => true,
            'values' => array(
                array(
                    'id' => 'active_on',
                    'value' => 1,
                    'label' => $this->l('enabled'),
                ),
                array(
                    'id' => 'active_off',
                    'value' => 0,
                    'label' => $this->l('disabled'),
                )
            )
        );

        if ($this->correctFirmlet('BF', 'FT')) {
            // Show my invoices (only in FT/BF/IO)
            $input[] = array(
                'type' => (_PS_VERSION_ < '1.6' ? 'radio' : 'switch'),
                'label' => $this->l('show_my_invoices_label'),
                'class' => 't',
                'name' => 'show_my_invoices',
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('enabled'),
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('disabled'),
                    )
                )
            );
        }

        // Identify OSS
        $input[] = array(
            'type' => (_PS_VERSION_ < '1.6' ? 'radio' : 'switch'),
            'label' => $this->l('identify_oss'),
            'hint' => $this->l('identify_oss_hint'),
            'name' => 'identify_oss',
            'class' => 't',
            'is_bool' => true,
            'values' => array(
                array(
                    'id' => 'active_on',
                    'value' => 1,
                    'label' => $this->l('enabled'),
                ),
                array(
                    'id' => 'active_off',
                    'value' => 0,
                    'label' => $this->l('disabled'),
                )
            )
        );

        // Show order messages
        $input[] = array(
            'type' => (_PS_VERSION_ < '1.6' ? 'radio' : 'switch'),
            'label' => $this->l('show_order_messages'),
            'hint' => $this->l('show_order_messages_hint'),
            'name' => 'show_order_messages',
            'class' => 't',
            'is_bool' => true,
            'values' => array(
                array(
                    'id' => 'active_on',
                    'value' => 1,
                    'label' => $this->l('enabled'),
                ),
                array(
                    'id' => 'active_off',
                    'value' => 0,
                    'label' => $this->l('disabled'),
                )
            )
        );
/*
        // Force tax
        $input[] = array(
            'type' => (_PS_VERSION_ < '1.6' ? 'radio' : 'switch'),
            'label' => $this->l('force_vat'),
            'hint' => $this->l('force_vat_hint'),
            'name' => 'force_vat',
            'class' => 't',
            'is_bool' => true,
            'values' => array(
                array(
                    'id' => 'active_on',
                    'value' => 1,
                    'label' => $this->l('enabled'),
                ),
                array(
                    'id' => 'active_off',
                    'value' => 0,
                    'label' => $this->l('disabled'),
                )
            )
        );
*/
        // Additional fields
        $this->context->smarty->assign(
            [
                'additional_fields_desc' => $this->l('additional_fields_desc'),
                'json_syntax_example' => $this->l('json_syntax_example'),
            ]
        );

        $input[] = array(
            'type' => 'textarea',
            'label' => $this->l('additional_fields'),
            'hint' => $this->l('additional_fields_hint'),
            'desc' => $this->display(__FILE__, 'views/templates/admin/configuration/input_advanced.tpl'),
            'name' => 'additional_fields',
            'columns' => 50,
        );

        return $input;
    }


    /**
     * Includes all required JS files. (Only if required)
     * @since 2.2.5
     */
    /*private function includeJS()
    {
        $this->context->controller->addJquery();
        $files = array(
        );
        foreach ($files as $f) {
            $this->context->controller->addJS($this->_path.'views/js/' . $f);
        }
    }*/

    /**
     * Includes all required CSS files. (Only if required)
     * @since 2.2.5
     */
    private function includeCSS()
    {
        $files = array(
            'vosfacturesapp.css',
        );
        foreach ($files as $f) {
            $this->context->controller->addCSS($this->_path.'views/css/' . $f);
        }
    }


    /**
     * Renders Firmlet configuration form.
     * @since 2.2.8
     */
    private function renderForm($is_configured)
    {
        $fields_form = array();
        // Basic options
        $fields_form[0] = array();
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('connection_settings'),
            ),
            'input' => $this->getFormInputBasic($is_configured),
            'submit' => array(
                'title' => $this->l('save_settings'),
                'name' => "vosfacturesapp_basic",
                'class' => (_PS_VERSION_ < '1.6' ? 'button' : 'btn btn-default pull-right')
            )
        );
        if ($is_configured) {
            // Advanced options
            $fields_form[1] = array();
            $fields_form[1]['form'] = array(
                'legend' => array(
                    'title' => $this->l('advanced_settings'),
                ),
                'input' => $this->getFormInputAdvanced(),
                'submit' => array(
                    'title' => $this->l('save_settings'),
                    'name' => "vosfacturesapp_basic",
                    'class' => (_PS_VERSION_ < '1.6' ? 'button' : 'btn btn-default pull-right')
                )
            );
        }

        $helper = new HelperForm();

        // Module, token and currentIndex.
        // Required in Prestashop_1.5
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->show_toolbar = false;

        // Language
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        // Load current values
        $helper->fields_value = $this->getFormValues();
        return $helper->generateForm($fields_form);
    }

    /**
     * Returns form values.
     * If field is present in post, will use it instead of container one
     * @since 2.2.8
     */
    private function getFormValues()
    {
        $fields = $this->c->getFieldKeys();
        $values = array();
        foreach ($fields as $field) {
            $values[$field] = Tools::getValue($field, $this->c->{$field});
        }
        return $values;
    }


    /**
     * Renders module information template (PS version based)
     * @since 2.2.8
     */
    private function renderModuleInformation($is_configured)
    {
        $this->context->smarty->assign($this->applyCommonSmarty(array(
            'help_url' => self::HELP_URL,
            'new_account_url' => self::NEW_ACCOUNT_URL,
            'is_configured' => $is_configured,
        )));

        return $this->display(__FILE__, 'views/templates/admin/configuration/module_info.tpl');
    }

    public function getContent()
    {
        // Allow to auto-install Account
        $accountsInstaller = $this->getService('ps_accounts.installer');
        $accountsInstaller->install();

        try {
            // Account
            $accountsFacade = $this->getService('ps_accounts.facade');
            $accountsService = $accountsFacade->getPsAccountsService();
            Media::addJsDef([
                'contextPsAccounts' => $accountsFacade->getPsAccountsPresenter()
                    ->present($this->name),
            ]);

            // Retrieve Account CDN
            $this->context->smarty->assign('urlAccountsCdn', $accountsService->getAccountsCdn());

            $billingFacade = $this->getService('ps_billings.facade');
            $partnerLogo = $this->getLocalPath() . 'views/img/partnerLogo.png';

            // Billing
            Media::addJsDef(
                $billingFacade->present([
                    'sandbox' => true,
                    'billingEnv' => 'preprod',
                    'logo' => $partnerLogo,
                    'tosLink' => 'https://vosfactures.fr/conditions-utilisation',
                    'privacyLink' => 'https://vosfactures.fr/conditions-utilisation',
                    'emailSupport' => 'integrations@vosfactures.fr',
                ])
            );

            $this->context->smarty->assign(
                'pathVendor',
                $this->getPathUri() . 'views/js/chunk-vendors-vosfactures.' . $this->version . '.js'
            );
            $this->context->smarty->assign(
                'pathApp',
                $this->getPathUri() . 'views/js/app-vosfactures.' . $this->version . '.js'
            );
        } catch (Exception $e) {
        }

        $html = '';
        if (Tools::isSubmit('vosfacturesapp_basic')) {
            if ($this->c->api_token != trim(Tools::getValue('api_token'))) {
                // We override department_id, cause we need to set it up again.
                $_POST['department_id'] = 0;
            }
            $this->postValidation();
            if (empty($this->_postErrors)) {
                $this->postProcess();
                $html .= $this->displayConfirmation($this->l('settings_saved'));
            } else {
                foreach ($this->_postErrors as $err) {
                    $html .= $this->displayError($err);
                }
            }
        }

        $post_api_token = Tools::htmlentitiesUTF8(Tools::getValue('api_token'));
        $is_configured = !empty($this->c->api_token) || ($post_api_token && empty($this->_postErrors));

        return $html
            . $this->renderModuleInformation($is_configured)
            . $this->renderForm($is_configured)
            . $this->renderExtra($is_configured);
    }

    private function renderExtra($is_configured)
    {
        $this->context->smarty->assign($this->applyCommonSmarty(array(
            'is_configured' => $is_configured,
        )));

        return $this->display(__FILE__, 'views/templates/admin/configuration/extra.tpl');
    }


    public function hookActionOrderStatusPostUpdate($params)
    {
        $order = new Order($params['id_order']);
        if (!Validate::isLoadedObject($order)) {
            $this->addLog(__METHOD__ . ': order was not validated.');
            return false;
        }

        $new_status = $params['newOrderStatus'];
        $last_invoice = $this->db->getLastInvoice($order->id);

        $this->addLog('hookActionOrderStatusPostUpdate ['.$order->getUniqReference().'] was triggered');

        if ($this->correctFirmlet('VF')) {
            if ($new_status->paid == '1' && $this->c->isConfigured() && !empty($last_invoice)) {
                if ($last_invoice['external_id'] != 0) {
                    $this->makeInvoiceStatusPaid($last_invoice['external_id']);
                }
            }
            return;
        }

        if ($this->c->isConfigured() &&
            in_array($this->c->auto_issue, array('order_creation', 'order_paid', 'order_shipped'))) {
            if ($this->c->auto_issue == 'order_creation') {
                //wystawiamy fakture w Fakturowni po raz zlozeniu zamowienia
                $url = $this->getInvoicesUrlJson();
                $invoice_data = $this->invoiceFromOrder($order->id);
                if ($new_status->id == 2) {
                    $invoice_data['invoice']['status'] = 'paid';
                }
                $result = $this->issueInvoiceTry($invoice_data, $url);
                $this->afterSendActions($result, $order);
            } elseif ($this->c->auto_issue == 'order_paid' && $new_status->paid == '1' && $order->hasBeenPaid() == 0) {
                $url = $this->getInvoicesUrlJson();
                $invoice_data = $this->invoiceFromOrder($order->id);
                $invoice_data['invoice']['status'] = 'paid';
                $result = $this->issueInvoiceTry($invoice_data, $url);
                $this->afterSendActions($result, $order);
            } elseif ($this->c->auto_issue == 'order_shipped' && $new_status->shipped == '1') {
                $url = $this->getInvoicesUrlJson();
                $invoice_data = $this->invoiceFromOrder($order->id);
                if ($order->hasBeenPaid() > 0) {
                    $invoice_data['invoice']['status'] = 'paid';
                }
                $result = $this->issueInvoiceTry($invoice_data, $url);
                $this->afterSendActions($result, $order);
            }
        }
    }


    /**
     * Makes a request to Firmlet and changes invoice status to 'paid'
     *
     * @since 2.2.3
     * @param array $invoice_row Invoice row from database. [id, external_invoice_id, view_link]
     */
    private function makeInvoiceStatusPaid($id_firmlet_invoice, $paid_date = null)
    {
        if ($paid_date == null) {
            $url = $this->getInvoiceUrl((int)$id_firmlet_invoice) . '/change_status.json';
            $data = array(
                'api_token' => $this->c->api_token,
                'status' => 'paid',
            );
            $this->makeRequest($url, 'POST', $data);
        } else { # in case of invoices issued manually paid date is taken from order
            $url = $this->getInvoiceUrl((int)$id_firmlet_invoice) . '.json';
            $data = array(
                'api_token' => $this->c->api_token,
                'invoice' => array( 'status' => 'paid', 'paid_date' => $paid_date ),
            );
            $this->makeRequest($url, 'PUT', $data);
        }
    }

    private function isSameShopAsContext($id_shop, $id_shop_group)
    {
        return Shop::getContextShopID(true) == $id_shop
            && Shop::getContextShopGroupID(true) == $id_shop_group;
    }


    /**
     * Applies commmon variables to smarty array for further assignment to
     * $this->context->smarty->assign()
     *
     * @param array $array Array of variables to be merged. Default: array()
     * @return array Merged array (with overriden values if present)
     * @since 2.2.5
     */
    public function applyCommonSmarty($array = array())
    {
        $arr = array_merge(
            array(
                'debug' => VOSFACTURES_DEBUG,
                'debug_context' => $this->getDebugContext(),
                'active_multistore' => Shop::isFeatureActive(),
                'ps_version' => _PS_VERSION_,
                'module_dir' => $this->_path,
                'FT' => 0,
                'VF' => 1,
                'BF' => 0,
            ),
            $array
        );
        return $arr;
    }

    /**
     * Hook for front office 'my-account' to display link to 'my-invoices'.
     * @since 2.2.5
     * @since 2.3.0 - displayed only if field is present
     */
    public function hookDisplayCustomerAccount()
    {
        if (!$this->c->show_my_invoices) {
            return '';
        }

        $this->context->smarty->assign($this->applyCommonSmarty(array(
            'this' => & $this
        )));
        return $this->display(__FILE__, 'views/templates/hook/my_account.tpl');
    }

    /**
     * Hook for new controller my-invoices.
     * @since 2.2.5
     */
    public function hookDisplayHeader($params)
    {
        // Adds CSS to front office based on PS Version.
        $version = Tools::substr(_PS_VERSION_, 0, 3);
        $this->context->controller->addCSS($this->_path."views/css/front-{$version}.css", 'all');
    }

    /**
     * Hook for backoffice controller
     * @since 2.2.8
     */
    public function hookDisplayBackOfficeHeader($params)
    {
        if (Tools::getValue('controller') == 'AdminOrders') {
            $invoices_with_errors = $this->db->invoicesWithErrors();
            if (! empty($invoices_with_errors)) {
                $errors = $this->l('invoices_with_errors_notice') . ', ' . PHP_EOL;
                foreach ($invoices_with_errors as $invoice) {
                    $this->context->smarty->assign(
                        [
                            'id_order' => $invoice['id_order'],
                            'token' => Tools::getAdminTokenLite('AdminOrders'),
                            'order' => $this->l('order')
                        ]
                    );

                    $errors = $errors . $this->display(__FILE__, 'views/templates/admin/order/error.tpl');
                }
                $this->adminDisplayWarning(($errors));
            }
        }

        $this->includeCSS();
    }

    /**
     * Hook for order to display Firmlet Invoice information
     */
    public function hookAdminOrder($params)
    {
        $id_order = Tools::getValue('id_order');
        $order = new Order($id_order);
        if (!Validate::isLoadedObject($order)) {
            return false;
        }

        /**
         * Multistore support.
         * @since 2.2.5
         */
        if (Shop::isFeatureActive()) {
            $container = $this->changeContainer(null, $order->id_shop, $order->id_shop_group, true);
        } else {
            $container = & $this->c;
        }

        $last_invoice = $this->db->getLastInvoice($id_order);
        $this->context->smarty->assign($this->applyCommonSmarty(array(
            'is_same_shop' => $this->isSameShopasContext($order->id_shop, $order->id_shop_group),
            'current_shop_data' => $this->getCurrentShopData(),
            'order_shop_data' => $this->getOrderShopData($id_order),
            'this' => & $this,
            'account_url' => $this->getApiUrl(),
            'container' => $container,
            'order' => $order,
            'invoice' => $last_invoice,
            'invoices_enabled' => Configuration::get('PS_INVOICE'),
            'vfAdminLink' => $this->context->link->getAdminLink('AdminVosFacturesApp'),
        )));

        return $this->display(__FILE__, 'views/templates/admin/order/main.tpl');
    }

    public function hookDisplayAdminOrderContentOrder($params)
    {
        return $this->hookAdminOrder($params);
    }

    public function hookDisplayAdminOrderTabContent($params)
    {
        return $this->hookAdminOrder($params);
    }

    public function hookDisplayAdminOrderTabOrder($params)
    {
        $this->context->smarty->assign(
            [
                'moduleName' => 'vosfacturesapp'
            ]
        );
        return $this->display(__FILE__, 'views/templates/admin/order/admin_order_tab.tpl');
    }

    public function hookDisplayAdminOrderTabLink($params)
    {
        $this->context->smarty->assign(
            [
                'moduleName' => 'vosfacturesapp'
            ]
        );
        return $this->display(__FILE__, 'views/templates/admin/order/admin_order_tab.tpl');
    }

    private function getTestInvoice()
    {
        $data = array(
            'invoice'   => array(
                'issue_date' => date('Y-m-d'),
                //'seller_name' => 'seller_name_test',
                'number' => 'prestashop_integration_test',
                'kind' => 'vat',
                'buyer_first_name' => 'buyer_first_name_test',
                'buyer_last_name' => 'buyer_last_name_test',
                'buyer_name' => 'prestashop_integration_test',
                'buyer_city' => 'buyer_city',
                'buyer_phone' => '221234567',
                'buyer_country' => 'PL',
                'buyer_post_code' => '01-345',
                'buyer_street' => 'buyer_street',
                'oid' => 'test_oid',
                'buyer_email' => 'buyer_email@test.pl',
                'buyer_tax_no' => '2923019583',
                'payment_type' => 'transfer',
                'lang' => 'pl',
                'currency' => 'PLN',
                'origin' => $this->getOrigin(),
                'positions' => array(
                    array(
                        'name' => 'prestashop integration test',
                        'kind' => 'text_separator',
                        'tax' => 'disabled',
                        'total_price_gross' => 0,
                        'quantity' => 0
                    )
                )
            )
        );

        $department_id = trim(Tools::getValue('department_id'));
        if (!empty($department_id)) {
            $data['invoice']['department_id'] = $department_id;
        }

        return $data;
    }

    // if failure, probably no internet connection
    // or tried to access 'fakturownia' from 'vosfactures account or vice versa'
    //czy w ogole api_token jest dobry
    private function testIntegration1()
    {
        $api_token = trim(Tools::getValue('api_token'));
        $url = $this->getInvoicesUrlJson($api_token) . '?page=1&api_token=' . $api_token;

        $result = $this->makeRequest($url, 'GET', null);
        return is_array($result);
    }

    //czy na koncie jest ustawiony department
    private function testIntegration2()
    {
        $api_token = trim(Tools::getValue('api_token'));
        // PHP 5.2.17 Compatibility - assign departments first
        $departments = $this->getDepartments($api_token);
        return !empty($departments[0]->id);
    }

    //czy podany department_id jest dobry
    private function testIntegration2b()
    {
        $api_token = trim(Tools::getValue('api_token'));
        $department_id = trim(Tools::getValue('department_id'));
        if (empty($department_id)) {
            return true;
        }

        foreach ($this->getDepartments($api_token) as $dep) {
            if ($dep->id == (int)$department_id) {
                return true;
            }
        }

        return false;
    }

    //czy da sie wystawic fakture
    private function testIntegration3()
    {
        $api_token = trim(Tools::getValue('api_token'));
        $url = $this->getInvoicesUrlJson($api_token);
        $invoice_data = $this->getTestInvoice();
        $invoice_data['api_token'] = $api_token;
        $additional_fields = $this->getAdditionalFields(Tools::getValue('additional_fields'));

        if ($additional_fields != null) {
            foreach ($additional_fields as $key => $value) {
                if (in_array($key, $this->getIllegalFields())) {
                    $this->_postErrors[] = $this->l('illegal_additional_field') . ' "' . $key . '"';
                    return false;
                } else {
                    $invoice_data['invoice'][$key] = $value;
                }
            }
        }

        $result = $this->issueInvoice($invoice_data, $url);
        if (isset($result->code) && $result->code === 'error' && isset($result->message)) {
            $error_message = "Test3: ";
            if (empty($result->message)) {
                $this->_postErrors[] = $error_message . $this->l('undefined_response');
            } elseif (isset($result->message->seller_tax_no)) {
                $this->_postErrors[] = $error_message . $result->message->seller_tax_no[0];
            } elseif (isset($result->message->seller_tax_no)) {
                $this->_postErrors[] = $error_message . $result->message->buyer_tax_no[0];
            } elseif ($result->message == "account_not_pro") {
                $this->setLastError(VOSFACTURES_ERR_ACCOUNT_NOT_PRO);
                $this->_postErrors[] = $error_message . $this->l('account_not_pro');
            } else {
                $this->_postErrors[] = $error_message . $result->message;
            }
        }

        if (empty($result->id)) {
            if (VOSFACTURES_DEBUG) {
                $this->addLog("Test integration 3 failure: " . json_encode($result));
            }
            return false;
        } else { // usuwanie dodanej faktury, klienta i sprzedawcy
            $url = $this->getInvoiceUrlJson($result->id, $api_token) . '?api_token=' . $api_token;
            $this->makeRequest($url, 'DELETE', null);

            $url = $this->getClientUrlJson($result->client_id, $api_token) . '?api_token=' . $api_token;
            $this->makeRequest($url, 'DELETE', null);
            return true;
        }
    }

    /**
     * Used in BF and FT
     */
    private function enableReceipts()
    {
        $url = $this->getAccountUrlJson();
        $data = array(
            'api_token' => $this->c->api_token,
            'account' => array('use_receipt' => true)
        );
        $this->makeRequest($url, 'PUT', $data);
    }


    /**
     * Returns api domain with in format "account_prefix.firmlet.com"
     *
     * @param string $api_token API Token from Firmlet. Default: null
     * @return string API domain
     * @since 2.3.2
     * @since 2.3.5 DEV - now redirecting to .test instead of .dev
     */
    private function getApiDomain($api_token = null)
    {
        if (is_null($api_token)) {
            $api_token = $this->c->api_token;
        }

        $account_prefix = explode('/', $api_token);
        $account_prefix = array_pop($account_prefix);
        $api_url = self::API_URL;

        if (VOSFACTURES_DEBUG) { // change domain to .test
            $api_url = explode('.', $api_url);
            array_pop($api_url);
            $api_url = implode($api_url) . '.test';
        }

        $domain = $account_prefix . '.' . $api_url;
        return $domain;
    }


    /**
     * Returns api url
     *
     * @param string $controller firmlet controller. Default: ''
     * @param string $api_token Firmlet API Token. Default: null
     * @return string API url
     */
    private function getApiUrl($controller = '', $api_token = null)
    {
        $http = VOSFACTURES_DEBUG ? 'http://' : 'https://';
        return $http . $this->getApiDomain($api_token) . '/' . $controller;
    }


    /**
     * Account urls methods
     */
    public function getAccountUrl($api_token = null)
    {
        return $this->getApiUrl('account', $api_token);
    }
    public function getAccountUrlJson($api_token = null)
    {
        return $this->getAccountUrl($api_token) . '.json';
    }


    /**
     * Invoices urls methods
     */
    public function getInvoicesUrl($api_token = null)
    {
        return $this->getApiUrl('invoices', $api_token);
    }
    public function getInvoicesUrlJson($api_token = null)
    {
        return $this->getInvoicesUrl($api_token) . '.json';
    }
    public function getInvoiceUrl($id, $api_token = null)
    {
        return $this->getInvoicesUrl($api_token) . '/' . $id;
    }
    public function getInvoiceUrlJson($id, $api_token = null)
    {
        return $this->getInvoiceUrl($id, $api_token) . '.json';
    }

    /**
     * Client urls methods
     */
    public function getClientsUrl($api_token = null)
    {
        return $this->getApiUrl('clients', $api_token);
    }
    public function getClientsUrlJson($api_token = null)
    {
        return $this->getClientsUrl($api_token) . '.json';
    }
    public function getClientUrl($id, $api_token = null)
    {
        return $this->getClientsUrl($api_token) . '/' . $id;
    }
    public function getClientUrlJson($id, $api_token = null)
    {
        return $this->getClientUrl($id, $api_token) . '.json';
    }

    /**
     * Department urls methods
     */
    public function getDepartmentsUrl($api_token = null)
    {
        return $this->getApiUrl('departments', $api_token);
    }
    public function getDepartmentsUrlJson($api_token = null)
    {
        return $this->getDepartmentsUrl($api_token) . '.json';
    }
    public function getDepartmentUrl($id, $api_token = null)
    {
        return $this->getDepartmentsUrl($api_token) . '/' . $id;
    }
    public function getDepartmentUrlJson($id, $api_token = null)
    {
        return $this->getDepartmentUrl($id, $api_token) . '.json';
    }

    /**
     * Category urls methods
     */
    public function getCategoriesUrl($api_token = null)
    {
        return $this->getApiUrl('categories', $api_token);
    }
    public function getCategoriesUrlJson($api_token = null)
    {
        return $this->getCategoriesUrl($api_token) . '.json';
    }
    public function getCategoryUrl($id, $api_token = null)
    {
        return $this->getCategoriesUrl($api_token) . '/' . $id;
    }
    public function getCategoryUrlJson($id, $api_token = null)
    {
        return $this->getCategoryUrl($id, $api_token) . '.json';
    }


    private function curl($url, $method, $data)
    {
        $data = json_encode($data);
        $cu = curl_init($url);
        curl_setopt($cu, CURLOPT_VERBOSE, 0);
        curl_setopt($cu, CURLOPT_HEADER, 0);
        curl_setopt($cu, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($cu, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($cu, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($cu, CURLOPT_POSTFIELDS, $data);
        curl_setopt($cu, CURLOPT_HTTPHEADER, array('Accept: application/json','Content-Type: application/json'));
        curl_setopt($cu, CURLOPT_CONNECTTIMEOUT, 5);
        $response = curl_exec($cu);
        curl_close($cu);


        $result = json_decode($response);

        /*
        $this->addLog($url);
        $this->addLog($method);
        $this->addLog(print_r($data, true));
        $this->addLog(print_r($result, true));open .
        */

        return $result;
    }


    private function makeRequest($url, $method, $data)
    {
        return $this->curl($url, $method, $data);
    }


    private function issueInvoice($invoice_data, $url)
    {
        if ($this->c->fill_default_desc) {
            $invoice_data['fill_default_descriptions'] = true;
        }

        if ($this->c->identify_oss) {
            $invoice_data['identify_oss'] = true;
        }

        if ($this->c->force_vat) {
            $invoice_data['oss_force_tax'] = true;
        }

        if ($this->c->prod_desc_from_vf) {
            $invoice_data['fill_products_descriptions'] = true;
        }

        return $this->makeRequest($url, 'POST', $invoice_data);
    }

    private function issueInvoiceTry($invoice_data, $url)
    {
        $result = null;
        $i = 0;
        do {
            $i += 1;
            $result = $this->issueInvoice($invoice_data, $url);
        } while (empty($result) && $i <= 10);

        if (! empty($result) && $i != 1) {
            $this->addLog('Invoice issued at try number ' . $i);
        } elseif (empty($result)) {
            $this->addLog('No response form server for url: ' . $url);
        }
        return $result;
    }

    private function deleteInvoice($url)
    {
        return $this->makeRequest($url, 'DELETE', null);
    }

    public function getCustomerInvoices($id_customer)
    {
        return array_filter(
            array_map(
                static function ($o) {
                    $last_invoice = VosFacturesApp::getInstance()->db->getLastInvoice($o["id_order"]);

                    return empty($last_invoice) ? array() : $last_invoice;
                },
                Order::getCustomerOrders($id_customer)
            ),
            static function ($i) {
                return !empty($i);
            }
        );
    }

    public function getOrigin()
    {
        return 'prestashopapp_' . _PS_VERSION_ . '|' . $this->name . '_' . $this->version;
    }

    private function getFromServer($api_token, $url)
    {
        $url = $url . '?api_token=' . $api_token;
        $result = $this->makeRequest($url, 'GET', null);

        if (isset($result->code) && $result->code === 'error') {
            // todo: tylko na devie?
            // Error detected. Possibly api_token is not set.
            $this->addLog($result->message);
            return array();
        }

        return count($result) > 0 ? $result : array(); // prevention against null in foreach
    }

    private function getCategories($api_token = null)
    {
        if ($api_token == null) {
            $api_token = $this->c->api_token;
        }
        $res = $this->getFromServer($api_token, $this->getCategoriesUrlJson($api_token));
        return $res;
    }

    private function getDepartments($api_token = null)
    {
        if ($api_token == null) {
            $api_token = $this->c->api_token;
        }
        return $this->getFromServer($api_token, $this->getDepartmentsUrlJson($api_token));
    }


    /**
     * @since 2.3.5 - so far array and object is unsupported TODO:
     */
    private function prepareFirmletError($message)
    {
        switch (gettype($message)) {
            case 'string':
                return $message;
            case 'object':
                $error = '';
                foreach ($message as $key => $value) {
                    foreach ($value as $array_value) {
                        $error .= $key . ' ' . $array_value . PHP_EOL;
                    }
                }
                return trim($error);
            default:
                return json_encode($message);
        }
    }


    /**
     * Creates an invoice in fakturownia_invoice table.
     * Assertion: each order has either 0 or 1 invoices from Firmlet
     * Assertion: should there be more than 1 invoices, the rest were generated with an error.
     * Assertion: __METHOD__ should be called if there is no correct invoice present in the database.
     * @since 2.2.5
     * @param $result
     * @param $order
     * @return bool True if OK else False
     */
    private function afterSendActions($result, $order)
    {
        $order_invoices = $this->db->getAllInvoices($order->id);
        if (count($order_invoices) > 0) {
            /**
             * Delete all previously generated invoices that were marked as an error
             * @since 2.3.5
             */
            $this->db->deleteInvoice($order->id);
        }

        if (empty($result)) {
            // Response is null. That probably means that the server is down.
            $error = $this->l('firmlet_is_down');
            $this->db->insertInvoiceWithError($order->id, $error);
            $this->addLog($error, 3, null, 'Order', (int) $order->id);
            return false;
        }

        if (empty($result->view_url) || empty($result->id)) {
            if ($result->code == 'error' && !empty($result->message)) {
                $error = $this->prepareFirmletError($result->message);
            } else {
                $error = $this->l('invoice_creation_failed').' ['.$order->getUniqReference().
                    ']. '.$this->l('invoice_not_created'). $this->getApiDomain() . '!';
            }
            $this->db->insertInvoiceWithError($order->id, $error);
            $this->addLog($error, 3, null, 'Order', (int) $order->id);
            return false;
        }

        $this->db->insertInvoice($order->id, $result->view_url, $result->id);

        if ($this->c->auto_send) {
            $url = $this->getInvoiceUrl($result->id).'/send_by_email.json?api_token='.$this->c->api_token;
            $this->makeRequest($url, 'POST', null);
        }

        if ($order->hasBeenPaid()) {
            $paid_date = null;
            $order_histories = $order->getHistory(Context::getContext()->language->id);
            foreach ($order_histories as $order_history) {
                if ($order_history[ 'id_order_state' ] == '2') {
                    $paid_date = $order_history[ 'date_add' ];
                    break;
                }
            }

            $this->makeInvoiceStatusPaid($result->id, $paid_date);
        }

        $this->setLastError(VOSFACTURES_ERR_OK); // everything is OK;
        return true;
    }

    /**
     * Creates an invoice from order
     *
     * @param int $order - Order object
     * @param string $kind Invoice kind, only in FT/BF
     * @return data for invoice or false
     */
    private function invoiceFromOrder($order_id, $kind = '')
    {
        $order = new Order($order_id);
        $invoice = new VosFacturesAppInvoice($order, $kind);
        $api_data = array(
            'api_token' => $this->c->api_token,
            'invoice' =>  $invoice->getFinalInvoiceData(),
        );
        return $api_data;
    }

    /**
     * Changes $this->c to completely new Container based on params.
     * Used for multistore purposes.
     *
     * NOTE: When constructing, send $id_lang as null, unless a new field
     *       gets added to the settings, that depends on translation.
     *
     * @since 2.2.5
     * @param integer $id_lang        Language ID                    Default: null
     * @param integer $id_shop        Shop ID                        Defualt: null
     * @param integer $id_shop_group  ShopGroup ID                   Default: null
     * @param bool    $return         Will return container if true  Default: false
     */
    private function changeContainer($id_lang = null, $id_shop = null, $id_shop_group = null, $return = false)
    {
        $container = new VosFacturesAppContainer(null, $id_lang, $id_shop, $id_shop_group);
        if ($return) {
            return $container;
        } else {
            $this->c = $container;
        }
    }


    /**
     * Sends invoices to firmlet
     *
     * @since 2.2.8
     * @param int $id_order
     * @param bool $force in VF
     * @param string $kind in FT/BF
     */
    public function sendInvoice($id_order, $issue_invoice, $kind)
    {
        if ($this->correctFirmlet('VF')) {
            $force = $kind;
        }
        $order = new Order((int) $id_order);

        /**
         * Multistore support
         * @since 2.2.5
         */
        if (Shop::isFeatureActive()) {
            $this->changeContainer(null, $order->id_shop, $order->id_shop_group);
        }

        assert(!empty($order->reference));
        if (!Validate::isLoadedObject($order)) {
            return false;
        }

        // message for logger
        $error_message = 'send_invoice [' . $order->id . ', ' . $order->reference .  ']: ';

        // checking whether to continue or not
        if ($this->correctFirmlet('VF')) {
            if (!$force) {
                if ($order->hasInvoice()) {
                    $this->addLog(
                        $error_message . "This order already has an invoice",
                        3,
                        null,
                        'Order',
                        (int)$order->id
                    );
                    return false;
                }
            }
            // todo: dodac to do konfiguracji (jak i to nizej) i wyswietlac to zawsze po instalacji modulu
            if (!Configuration::get('PS_INVOICE')) {
                $this->addLog($error_message . "Invoices have to be turned on", 3, null, 'Order', (int)$order->id);
                return false;
            }
        }

        if (!$this->c->isConfigured()) {
            $this->addLog($error_message . "Module is configured incorrectly.", 3, null, 'Order', (int)$order->id);
            return false;
        }

        $order_state = new OrderState($order->getCurrentState());

        $generate_invoice = ($this->c->auto_issue == 'order_creation' ||
            ($this->c->auto_issue == 'order_paid' && $order_state->paid == '1') ||
            ($this->c->auto_issue == 'order_shipped' && $order_state->shipped == '1'));

        if ($generate_invoice || $issue_invoice) {
            $last_invoice = $this->db->getLastInvoice($order->id);
            $url = $this->getInvoicesUrlJson();

            if (!empty($last_invoice) && $last_invoice['external_id'] != 0) {
                $this->addLog($error_message . "invoice was already created", 3, null, 'Order', (int)$order->id);
                return !$issue_invoice;
            }


            // everything is ok. Waiting for server response
            if ($this->correctFirmlet('BF', 'FT')) {
                $invoice_data = $this->invoiceFromOrder($order->id, $kind);
            } elseif ($this->correctFirmlet('VF')) {
                $invoice_data = $this->invoiceFromOrder($order->id);
            }

            if (VOSFACTURES_DEBUG) {
                $this->addLog($error_message . json_encode($invoice_data));
            }

            $result = $this->issueInvoiceTry($invoice_data, $url);

            if (isset($result->code) && $result->code == 'error' && isset($result->message)) {
                if (empty($result->message)) {
                    $this->addLog($error_message . "undefined response message", 3, null, 'Order', (int)$order->id);
                } else {
                    if ($result->message == "account_not_pro") {
                        $this->setLastError(VOSFACTURES_ERR_ACCOUNT_NOT_PRO);
                    }
                    $this->addLog($error_message . json_encode($result), 3, null, 'Order', (int)$order->id);
                }
            }
            if ($this->afterSendActions($result, $order)) {
                $this->addLog($error_message . "Successfully created an invoice");
                return true;
            } else {
                $this->addLog($error_message . "Failed to create an invoice");
                return false;
            }
        } elseif ($this->c->auto_issue == 'disabled') {
            return true;
        }
    }

    /**
     * @since 2.3.5 Added this method in o
     */
    public function getLastError()
    {
        return $this->l($this->c->last_error);
    }

    /**
     * @since 2.3.5 Added handling via constants included from container
     */
    private function setLastError($error)
    {
        if (!empty($error)) {
            $error = $this->l($error);
            $this->addLog("ERROR: " . $error);
        }

        $this->c->last_error = $error;
        Configuration::updateValue('VOSFACTURES_LAST_ERROR', $this->c->last_error);
    }

    private function removeInvoiceFirmlet($invoice_row, $error_message)
    {
        $url = $this->getInvoiceUrlJson($invoice_row['external_id']) . '?api_token=' . $this->c->api_token;
        $response = $this->deleteInvoice($url);

        switch (gettype($response)) {
            case 'object': // object, not an array
                if (!empty($response->status) && $response->status == '404') {
                    $this->addLog($error_message . $this->l('invoice_not_found'));
                    $this->addLog($error_message . $this->l('removing_from_database'));
                } elseif (!empty($response->code) && $response->code === 'error') {
                    $this->addLog($error_message . $this->l('api_token_incorrect'));
                    $this->setLastError(VOSFACTURES_ERR_INVALID_API_TOKEN);
                    return false;
                }
                break;
            case 'string':
                if ($response == 'ok') {
                    // success, removed from fakturownia
                    $this->addLog($error_message . $this->l('invoice_was_removed'));
                } else {
                    $this->addLog($error_message . $this->l('undefined_response'));
                    return false;
                }
                break;
            default:
                if (VOSFACTURES_DEBUG) { // fakturownia on developer mode returns null
                    $this->addLog($error_message . $this->l('invoice_was_removed'));
                } else {
                    $this->addLog($error_message . $this->l('undefined_response'));
                    $this->addLog($response);
                    return false; // we do not know what kind of an object is $response
                }
        }

        return true;
    }

    /**
     *  Makes API request to delete invoice. On success removes the row from local database
     *  @param int $id_order - order's id, that should be in Fakturownia entity
     *  @return bool true if success
     */
    public function removeInvoice($id_order)
    {
        $order = new Order((int)$id_order);
        assert(!empty($order->reference));
        if (!Validate::isLoadedObject($order)) {
            return false;
        }
        /**
         * Multistore support
         * @since 2.2.5
         */
        if (Shop::isFeatureActive()) {
            $this->changeContainer(null, $order->id_shop, $order->id_shop_group);
        }

        $error_message = "remove_invoice [{$order->id}, {$order->reference}]: "; // debug: logger
        $last_invoice = $this->db->getLastInvoice($id_order);
        if (empty($last_invoice)) {
            $this->addLog($error_message . $this->l('invoice_not_found'));
            return false;
        }

        assert(isset($last_invoice['external_id']));
        if ($last_invoice['external_id'] == 0) {
            $this->addLog($error_message . $this->l('invoice_not_found'));
            return false;
        }

        // firmlet side
        if (!$this->removeInvoiceFirmlet($last_invoice, $error_message)) {
            return false;
        }

        // Everything is ok. Invoice was removed/not found on the server, so we remove it from Prestashop's database
        $this->db->deleteInvoiceViaFirmletId($last_invoice['id_fakturownia_invoice']);
        $this->setLastError(VOSFACTURES_ERR_OK);
        return true;
    }


    /**
     * add a log item to the database and send a mail if configured for this $severity
     *
     * @param string $message the log message
     * @param int $severity
     * @param int $error_code
     * @param string $object_type
     * @param int $object_id
     * @param bool $allow_duplicate if set to true, can log several time the same information (not recommended)
     * @return bool true if succeed
     */
    public function addLog(
        $message,
        $severity = 1,
        $error_code = null,
        $object_type = null,
        $object_id = null,
        $allow_duplicate = true,
        $id_employee = null
    ) {
        if (VOSFACTURES_DEBUG) {
            error_log(print_r($message, true));
        }

        $message = $this->name . $this->version . ': ' .
            (gettype($message) == 'string' ? $message : json_encode($message));

        /**
         * Backwards compatibility:
         *     Any version before PS 1.5.6.1 (excluded) used validation class
         *     for all fields in "Logger". Since 1.5.6.1, devs stopped using
         *     method 'Validate::isMessage' for $message.
         *     If $message contained any of [<, >, {, }] characters, isMessage
         *     returned 'false' and fatal error appeared.
         *     Therefore, we need to replace these characters for logging
         *     purposes in all prestashop versions.
         * @since 2.2.4
         */
        if (_PS_VERSION_ < '1.6') {
            $replacements = array(
                '{' => '&lbrace',
                '}' => '&rbrace',
                '<' => '&lt',
                '>' => '&gt'
            );
            foreach ($replacements as $pattern => $replacement) {
                $message = preg_replace('/'.$pattern.'/i', $replacement, $message);
            }
        }

        if (class_exists('PrestaShopLogger')) { // PrestaShop ver 1.6
            return PrestaShopLogger::addLog(
                $message,
                $severity,
                $error_code,
                $object_type,
                $object_id,
                $allow_duplicate,
                $id_employee
            );
        } else { // PrestaShop ver 1.5
            return Logger::addLog(
                $message,
                $severity,
                $error_code,
                $object_type,
                $object_id,
                $allow_duplicate,
                $id_employee
            );
        }
    }


    /**
     *  create an stdClass object from given string
     *
     *  @param string $additional_fields - string in json
     *  @return mixed - variable decoded from json
     */
    public function getAdditionalFields($additional_fields = null)
    {
        if ($additional_fields === null) {
            $additional_fields = $this->c->additional_fields;
        }

        $result = json_decode('{' . $additional_fields . '}');
        if ($result != null) {
            return $result;
        }
        $this->addLog("get_additional_fields: error in parsing json", 4);
        return null;
    }

    /**
     *  return an array of illegal parameters for invoice
     */
    public function getIllegalFields()
    {
        return array(
            'id',
            'account_id',
            'deleted',
            'token',
        );
    }

    /**
     * Get a string in json format, encodes it and validates with illegal parameters
     *
     * @param string additional_fields : json string to validate
     * @return bool true if validation is successful
     */
    private function validateFields($additional_fields)
    {
        $additional_fields = $this->getAdditionalFields($additional_fields); // json encode from string
        foreach ($additional_fields as $key) {
            if (in_array($key, $this->getIllegalFields(), true)) {
                return false;
            }
        }

        return true;
    }


    /**
     * NOTE: used only in debug mode
     * Saves all products from the shop into .csv file with default delimiter
     * @param string $filename filename
     */
    public function exportProductsToCSV($filename = 'prestashop_products.csv')
    {
        // Get all products without attributes
        $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT DISTINCT
                p.id_product,
                p.reference,
                pl.description_short as description,
                pa.id_product_attribute
            FROM `'._DB_PREFIX_.'product` p
            LEFT JOIN `'._DB_PREFIX_.'product_lang`      pl ON p.id_product = pl.id_product
            LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON p.id_product = pa.id_product
            WHERE (pl.id_lang = '.$this->context->language->id.')'
        );
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename=products.csv');
        ob_start();
        $csv = fopen($filename, 'w');
        foreach ($products as $p) {
            $row = array(
                'id_product' => $p['id_product'],
                'name' => Product::getProductName($p['id_product'], $p['id_product_attribute']),
                'code' => $p['reference'],
                'total_tax_excl' => Product::getPriceStatic($p['id_product'], false, $p['id_product_attribute']),
                'tax' => Tax::getProductTaxRate($p['id_product']),
                'total_tax_incl' => Product::getPriceStatic($p['id_product'], true, $p['id_product_attribute']),
                'warehouse_quantity' => StockAvailable::getQuantityAvailableByProduct(
                    $p['id_product'],
                    $p['id_product_attribute']
                ),
                'description' => $p['description']
            );
            fputcsv($csv, $row);
        }
        fclose($csv);
        ob_end_flush();
    }
}
