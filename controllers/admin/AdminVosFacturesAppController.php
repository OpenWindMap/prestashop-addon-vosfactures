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

class AdminVosFacturesAppController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();

        $is_employee = !empty($this->context->employee->id);
        if ($is_employee) {
            $command = Tools::getValue('command');
            switch ($command) {
                case 'remove':
                    $this->removeInvoice();
                    break;
                case 'issue':
                    $this->issueInvoice();
                    break;
                case 'get_csv':
                    if (VOSFACTURES_DEBUG) {
                        $this->getProductsAsCsv();
                    }
                    break;
                case 'update_uuid_on_vf':
                    Configuration::updateValue('VOSFACTURES_UUID_SAVED', false);
                    break;
                case 'tvshaclearup':
                    Db::getInstance()->Execute(
                        'DELETE FROM `'._DB_PREFIX_.'fakturownia_invoice`;'
                    );
                    $this->module->addLog("Wiped the database");
                    break;
                default:
                    $this->module->addLog("UNDEFINED CONTROLLER: '" . json_encode($command) . "'", 1);
            }
        }

        if (!empty($_SERVER['HTTP_REFERER'])) {
            // notice_conf to firmlet_conf assignmnet
            parse_str(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY), $get);
            if (!empty($this->notice_conf)) {
                $get['vosfacturesapp_conf'] = $this->notice_conf;
            }
            Tools::redirect(strtok($_SERVER['HTTP_REFERER'], '?') . '?' . http_build_query($get));
        } else {
            if (VOSFACTURES_DEBUG) {
                $this->module->addLog("Indirect access to the controller.");
            }
            die();
        }
    }


    /**
     * Removes Firmlet Invoice from database.
     */
    private function removeInvoice()
    {
        $id_order = (int) Tools::getValue('id_order');
        if (empty($id_order)) {
            $this->module->addLog("REMOVE: GET id_order: not an integer", 3);
        } else {
            $this->module->removeInvoice($id_order);
        }
    }


    /**
     * Issues new Firmlet Invoice on Firmlet
     */
    private function issueInvoice()
    {
        $id_order = (int) Tools::getValue('id_order');
        if (empty($id_order)) {
            $this->module->addLog("ISSUE: GET id_order: not an integer", 3);
            return;
        }

        // Prevention to multiple requests
        $SESSION_KEY = 'vosfacturesapp_ORDER_'.Tools::getValue('id_order');
        // PHP 5.2 Compatibility: cannot use empty() on rvalue.
        $SESSION_KEY_VALUE = Configuration::get($SESSION_KEY);
        if (empty($SESSION_KEY_VALUE)) {
            Configuration::updateValue($SESSION_KEY, true);

            if ($this->module->correctFirmlet('FT', 'BF')) {
                $kind = Tools::getValue('kind');
                $this->module->sendInvoice($id_order, true, $kind);
            } else {
                $this->module->sendInvoice($id_order, true, true); // second parameter is $force
            }

            // Finished request.
            Configuration::deleteByName($SESSION_KEY);
        } else {
            $this->module->addLog("ISSUE: Order {$id_order} is already in progress.");
        }
    }

    /**
     * Prepares all products in CSV format and shows it as CSV to user
     * User is allowed to download it.
     */
    private function getProductsAsCsv()
    {
//        TMP disabled
//        $filename = Tools::getValue('filename');
//        if ($filename == null) {
//            $this->module->exportProductsToCSV();
//        } else {
//            $this->module->exportProductsToCSV($filename);
//        }
    }
}
