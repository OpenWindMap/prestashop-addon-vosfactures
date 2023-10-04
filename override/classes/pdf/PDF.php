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

class PDF extends PDFCore
{
    public function render($display = true)
    {
        $module = Module::getInstanceByName('vosfacturesapp');
        $module->addlog("Template: " . PDF::TEMPLATE_INVOICE . ", " .
                        json_encode($this->template==PDF::TEMPLATE_INVOICE));
        if ($this->template == PDF::TEMPLATE_INVOICE) {
            if (!empty($this->objects)) {
                $order_invoice = null;
                if (gettype($this->objects) == 'array') { // PS 1.5
                    $order_invoice = $this->objects[0];
                } else {
                    $order_invoice = $this->objects->getFirst(); // PS 1.6
                }
                $id_order = $order_invoice->id_order;

                require_once(_PS_MODULE_DIR_.'vosfacturesapp/vosfacturesapp.php');
                $module = new VosFacturesApp();
                $invoice = $module->db->getLastInvoice($id_order);
                if (!empty($invoice)) {
                    if (!empty($invoice['view_url'])) {
                        $file = $invoice['view_url'].'.pdf';

                        if ($display) {
                            header('Content-type: application/pdf; filename="the.pdf"');
                            return @readfile($file);
                        } else {
                            return Tools::file_get_contents($file);
                        }
                    }
                }
            }
        }

        return parent::render($display);
    }
}
