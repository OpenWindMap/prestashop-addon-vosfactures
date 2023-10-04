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
 * @since 2.3.5:
 * - Added 'override_carrier_name' and 'use_carrier_name' fields to container
 */
function upgrade_module_2_3_5($object)
{
    $firmlet_version = Configuration::get('VOSFACTURES_VERSION');
    if (empty($firmlet_version)) {
        // Started supporting upgrades since 2.2.5
        $firmlet_version = 'before 2.2.6';
    }

    $success = true;
    $success = $success && Configuration::updateValue(Tools::strtoupper('VOSFACTURES_' . 'use_carrier_name'), false);
    $success = $success && Configuration::updateValue(Tools::strtoupper('VOSFACTURES_' . 'override_carrier_name'), '');
    $success = $success && Configuration::updateValue(Tools::strtoupper('VOSFACTURES_' . 'category_id'), 0);
    $success = $success &&
        Configuration::updateValue(Tools::strtoupper('VOSFACTURES_' . 'company_or_full_name'), 'company');
    $success = $success && Configuration::updateValue(Tools::strtoupper('VOSFACTURES_' . 'version'), '2.3.5');

    $success = $success && Db::getInstance()->execute('
        ALTER TABLE `'._DB_PREFIX_.'fakturownia_invoice`
        ADD `error` VARCHAR(255) DEFAULT NULL');

    $object->addLog("Upgrade from " . $firmlet_version . " to 2.3.5: " . ($success ? "success" : "failure"));
    return $success;
}
