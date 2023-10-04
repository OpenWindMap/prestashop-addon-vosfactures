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

/**
 * @since 2.2.8:
 * - Replaced database configuration keys
 * - Removed trash keys from 2.2.6/2.2.7
 * - Removed hookAdminOrder in PS 1.5. Replaced it with displayAdminOrderTabOrder/ContentOrder
 */
function upgrade_module_2_2_8($object)
{
    $firmlet_version = Configuration::get('FAKTUROWNIA_VERSION');
    if (empty($firmlet_version)) {
        // Started supporting upgrades since 2.2.5
        $firmlet_version = 'before 2.2.6';
    }
    $fields = array(
        // Key               => Default
        'api_token'          => '',
        'account_prefix'     => '',
        'auto_send'          => 'disabled',
        'incl_free_shipment' => 'disabled',
        'fill_default_desc'  => 'enabled',
        'department_id'      => '0',
        'additional_fields'  => '',
        'last_error'         => '',
        'version'            => '2.2.8'
    );
    if ($object->correctFirmlet('BF', 'FT')) {
        $fields['auto_issue'] = 'disabled';
        // issue_kind visible only in fakturownia and bitfactura
        $fields['issue_kind'] = 'vat_or_receipt';
    } else {
        $fields['auto_issue'] = 'order_paid';
    }

    $confs = array();
    foreach ($fields as $field => $default) {
        // 2.2.6/2.2.7 fix
        // in these, we accidentaly saved keys as '__DB_PREFIX__'...
        $fakturownia_val1 = Configuration::get(Tools::strtoupper('FAKTUROWNIA_' . $field));
        $fakturownia_val2 = Configuration::get(Tools::strtoupper('FAKTUROWNIA' . $field));
        $db_prefix_val   = Configuration::get(Tools::strtoupper('__DB_PREFIX__' . $field));
        if ($db_prefix_val !== false) {
            $confs[$field] = $db_prefix_val;
        } elseif ($fakturownia_val2 !== false) {
            $confs[$field] = $fakturownia_val2;
        } elseif ($fakturownia_val1 !== false) {
            $confs[$field] = $fakturownia_val1;
        } else {
            $confs[$field] = $default;
        }
    }

    $success = true;
    foreach ($confs as $key => $value) {
        if (in_array($key, array('auto_send', 'incl_free_shipment', 'fill_default_desc'))) {
            // On 2.2.8, we changed configuration form - enabled/disabled are now (int) bool values.
            $value = $value === 'enabled';
        }
        $success = $success && Configuration::updateValue(Tools::strtoupper('VOSFACTURES_' . $key), $value);
    }



    // Hook management
    if (_PS_VERSION_ >= '1.6') {
        // Changed the way order invoice management is displayed
        $success = $success
            && $object->registerHook('displayAdminOrderTabOrder')
            && $object->registerHook('displayAdminOrderContentOrder')
            && $object->registerHook('displayBackOfficeHeader')
            && $object->unregisterHook('adminOrder');
    }

    if ($success) {
        $is_installed = Module::isInstalled('fakturownia');
        foreach ($fields as $field => $default) {
            if (!$is_installed) {
                // Fakturownia module was not detected, so we remove these keys
                Configuration::deleteByName(Tools::strtoupper('FAKTUROWNIA_' . $field));
            }
            // 2.2.6/2.2.7 fix
            Configuration::deleteByName(Tools::strtoupper('__DB_PREFIX__' . $field));
            Configuration::deleteByName(Tools::strtoupper('FAKTUROWNIA' . $field));
            Configuration::deleteByName(Tools::strtoupper('VOSFACTURES' . $field));
        }
        // really old field
        Configuration::deleteByName('FAKTUROWNIA_API_URL');
    }

    $object->addLog("Upgrade from " . $firmlet_version . " to 2.2.8: " . ($success ? "success" : "failure"));
    return $success;
}
