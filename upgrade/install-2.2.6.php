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
 * @since 2.2.6:
 * - Added new controller (therefore requires new Tab()
 * - Added two hooks in FT/BF
 * - Added version key to settings
 */
function upgrade_module_2_2_6($object)
{
    $firmlet_version = Configuration::get('FAKTUROWNIA_VERSION');
    if (empty($firmlet_version)) {
        // Started supporting upgrades since 2.2.5
        $firmlet_version = 'before 2.2.6';
    }
    $object = Module::getInstanceByName('vosfacturesapp');
    $tab = new Tab();
    $tab->active = 1;
    $tab->name = array();
    $tab->class_name = 'AdminVosFacturesApp';
    foreach (Language::getLanguages(true) as $lang) {
        $tab->name[$lang['id_lang']] = 'VosFacturesApp';
    }
    $tab->id_parent = -1;
    $tab->module = 'vosfacturesapp';

    $success = $tab->add();
    if ($object->correctFirmlet('BF', 'FT')) {
        // Only in bitfactura/fakturownia
        $success = $success &&
            $object->registerHook('displayCustomerAccount') &&
            $object->registerHook('displayHeader');
    }

    Configuration::updateValue('FAKTUROWNIA_VERSION', '2.2.6');
    $object->addLog("Upgrade from " . $firmlet_version . " to 2.2.6: " . ($success ? "success" : "failure"));
    return $success;
}
