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

class VosFacturesAppDatabase
{
    private static $prefix = 'fakturownia_invoice';

    private static $invoice_keys = array(
        'id_order',
        'view_url',
        'external_id',
        'error',
    );

    /**
     * @since 2.3.5 Wrapper for addlog
     */
    private function addLog($msg)
    {
        $module = & VosFacturesApp::getInstance();
        $module->addLog("DB: " . $msg);
    }

    public function installDatabase()
    {
        Db::getInstance()->Execute(
            'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'fakturownia_invoice` (
            `id_fakturownia_invoice` INT(11) UNSIGNED NOT null auto_increment,
            `id_order` INT(11) UNSIGNED NOT null,
            `view_url` VARCHAR(255),
            `external_id` INT(11),
            `error` VARCHAR(255),
            PRIMARY KEY (`id_fakturownia_invoice`),
            KEY `id_order` (`id_order`)
            ) DEFAULT CHARSET=utf8;'
        );
    }

    /**
     * returns last created invoice for $id_order
     * @param int $id_order
     * @param bool $all Should return all invoices for $id_order?
     *                  Default: false
     * @return array - if any rows are present
     *         null - if no records are present
     */
    public function getLastInvoice($id_order, $all = false)
    {
        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT *
            FROM `'._DB_PREFIX_.'fakturownia_invoice`
            WHERE `id_order` = '.(int)($id_order).
            ' ORDER BY `id_fakturownia_invoice`'. ($all ? '' : ' DESC LIMIT 1')
        );
        if ($all) {
            return $row;
        }
        return count($row) > 0 ? $row[0] : null;
    }

    public function getAllInvoices($id_order)
    {
        return $this->getLastInvoice($id_order, true);
    }

    public function insertInvoice($id_order, $view_url, $external_id)
    {
        $this->addLog(
            "Insert invoice [id_order => " . $id_order
                        . ", view_url => " . $view_url
                        . ", external_id => " .$external_id . ']'
        );

        return Db::getInstance()->insert(
            self::$prefix,
            array(
                'id_order' => (int) $id_order,
                'view_url' => pSQL($view_url),
                'external_id' => (int) $external_id,
            )
        );
    }

    public function insertInvoiceWithError($id_order, $error)
    {
        $this->addLog("Insert invoice [id_order => " . $id_order . ", error => " . $error. ']');

        $success =  Db::getInstance()->insert(
            self::$prefix,
            array(
                'id_order' => (int) $id_order,
                'error' => pSQL($error),
            )
        );
        return $success;
    }

    public function invoicesWithErrors()
    {
        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT `id_order`
            FROM `'._DB_PREFIX_.'fakturownia_invoice`
            WHERE `error` IS NOT NULL
            '
        );
        return $row;
    }

    public function updateInvoice($id_order, $fields)
    {
        foreach (array_keys($fields) as $key) {
            if (!in_array($key, self::$invoice_keys)) {
                $this->addLog('Update invoice attempt on "' . $key . '"');
                return false;
            }
        }

        $this->addLog("Update on id = " . $id_order);
        return Db::getInstance()->update(
            self::$prefix,
            array_map('pSQL', $fields)
        );
    }

    public function deleteInvoice($id_order)
    {
        $this->addLog("Delete on id = " . $id_order);
        return Db::getInstance()->delete(
            self::$prefix,
            'id_order = '.(int) $id_order
        );
    }

    public function deleteInvoiceViaFirmletId($id_firmlet_invoice)
    {
        $this->addLog("Delete on firmlet_id = " . $id_firmlet_invoice);
        return Db::getInstance()->delete(
            self::$prefix,
            'id_fakturownia_invoice = '.(int) $id_firmlet_invoice
        );
    }

    public function getCartRule($cart_rule_id)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT * FROM `'._DB_PREFIX_.'cart_rule` WHERE `id_cart_rule` = ' . (int) $cart_rule_id
        );
    }

    public function getFastBayInfo($order_id) {
        try {
            $query = 'SELECT * FROM `' . _DB_PREFIX_ . 'fastbay1_order_detail` WHERE `id_order` = ' . $order_id . ' LIMIT 1';

            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);

            if ($result && is_array($result) && count($result) > 0) {
                return $result[0];
            }
        } catch(Exception $ignored) {
            // Ignored, return null below
        }

        return null;
    }

    public static function getCustomerMessagesOrder($id_customer, $id_order)
    {
        $sql = 'SELECT cm.*, c.`firstname` AS cfirstname, c.`lastname` AS clastname,
                e.`firstname` AS efirstname, e.`lastname` AS elastname
			FROM ' . _DB_PREFIX_ . 'customer_thread ct
			LEFT JOIN ' . _DB_PREFIX_ . 'customer_message cm
				ON ct.id_customer_thread = cm.id_customer_thread
            LEFT JOIN `' . _DB_PREFIX_ . 'customer` c 
                ON ct.`id_customer` = c.`id_customer`
            LEFT OUTER JOIN `' . _DB_PREFIX_ . 'employee` e 
                ON e.`id_employee` = cm.`id_employee`
			WHERE ct.id_customer = ' . (int) $id_customer .
            ' AND ct.`id_order` = ' . (int) $id_order . '
            GROUP BY cm.id_customer_message
		 	ORDER BY cm.date_add DESC';

        return Db::getInstance()->executeS($sql);
    }
}
