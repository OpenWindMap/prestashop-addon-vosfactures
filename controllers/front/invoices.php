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

class VosFacturesAppInvoicesModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    public function initContent()
    {
        parent::initContent();
        $module = & VosFacturesApp::getInstance();

        if (!$this->context->customer->isLogged() || !$module->c->show_my_invoices) {
            Tools::redirect('index.php?controller=authentication&redirect=module&module=vosfacturesapp&action=invoices');
        }

        if ($this->context->customer->id) {
            $invoices_data = array_map(
                static function ($i) {
                    $o = new Order($i["id_order"]);

                    return array(
                        "reference"   => $o->getUniqReference(),
                        "date_add"    => $o->date_add,
                        "total_paid"  => Tools::displayPrice($o->total_paid, (int) $o->id_currency),
                        "id_currency" => $o->id_currency,
                        "view_url"    => $i["view_url"],
                    );
                },
                $module->getCustomerInvoices($this->context->customer->id)
            );

            $this->context->smarty->assign(
                $module->applyCommonSmarty(array(
                    'module' => $module,
                    'invoices' => $invoices_data,
                    'ps_version' => _PS_VERSION_,
                ))
            );
            $version = Tools::substr(_PS_VERSION_, 0, 3);

            /**
             * Prestashop 1.7 modified the way templates get rendered, so we
             * have to modify the template path for this.
             * @since 2.3.0
             */
            if (_PS_VERSION_ < '1.7') {
                $tpl = "invoices_{$version}.tpl";
            } else {
                $tpl = "module:vosfacturesapp/views/templates/front/invoices_{$version}.tpl";
            }
            $this->setTemplate($tpl);
        }
    }


    /**
     * Function to show breadcrumbs.
     * This will not work prior to PS 1.7.
     * @since 2.3.0
     */
    public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();
        return $breadcrumb;
    }
}
