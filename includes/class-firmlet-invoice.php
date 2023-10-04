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

class VosFacturesAppInvoice
{
    public function __construct($order, $kind)
    {
        $this->order = $order;
        $this->internal_note = null;

        if (!$this->init($this->order)) {
            "FATAL ERROR"; // TODO
        }

        if ($this->c->identify_oss) {
            $this->module->addLog('order ' . $order->id . ' - identify_oss enabled');
        }

        if ($this->c->force_vat) {
            $this->module->addLog('order ' . $order->id . ' - force_vat enabled');
        }

        // DO NOT CHANGE THIS ORDER
        $this->setupPositions();
        $this->setupCarriage();
        $this->setupWrapping();
        $this->disableTaxesIfNecessary();
        $this->calculateDiscounts();
        $this->addCarriage();
        $this->addOrderFee();
        $this->setupKind($kind);
        $this->setupPhone();
        $this->setupBuyerName();
        $this->getFastBayDetails();

        $this->prepareFinalInvoiceData();
    }

    public function getFinalInvoiceData()
    {
        return $this->invoice_data;
    }

    private function addCustomerMessages()
    {
        if (!$this->c->show_order_messages) {
            return;
        }

        $orderId = $this->order->id;
        $customerId = $this->order->getCustomer()->id;

        $db = new VosFacturesAppDatabase();
        $messages = $db->getCustomerMessagesOrder($customerId, $orderId);
        $messageContent = '';

        foreach ($messages as $key => $message) {
            if ($key > 0) {
                $messageContent .= PHP_EOL;
            }

            $messageContent .= $message['message'];
        }

        if ($this->invoice_data['description'] != null && $this->invoice_data['description'] != '') {
            $this->invoice_data['description'] .= PHP_EOL;
        }

        $this->invoice_data['description'] .= $messageContent;
    }

    /* Calculates product's discount amount based on summed discount and product's tax */
    private function calculatePositionDiscount($total_price_gross, $sum_total_price_gross, $discount)
    {
        if ($sum_total_price_gross == 0) {
            return 0;
        } elseif ($sum_total_price_gross <= $discount) {
            return $total_price_gross;
        }
        return ($total_price_gross / $sum_total_price_gross) * $discount;
    }

    private function calculateDiscounts()
    {
        // ->getDiscounts() is deprecated since PS-1.5.0.1 and it uses ->getCartRules
        $discounts_value = 0;
        $cart_rules = $this->order->getCartRules();

        $discounts = [];

        if (!empty($cart_rules)) {
            $db = new VosFacturesAppDatabase();

            foreach ($cart_rules as $cart_rule) {
                $cart_rule_details = $db->getCartRule($cart_rule['id_cart_rule']);

                if ($this->isFee($cart_rule_details[0])) {
                    if (isset($this->positions[0]['tax'])) {
                        $tax = $this->positions[0]['tax'];
                    } else {
                        $tax = $cart_rule['tax_rate'];
                    }

                    $this->positions[] = [
                        'name' => $cart_rule['name'],
                        'quantity' => $cart_rule['quantity'],
                        'total_price_gross' => $cart_rule['value'],
                        'tax' => round((float) $tax, 2),
                        'service' => true,
                    ];
                } else {
                    $discounts[] = $cart_rule;
                }
            }
        } else {
            return null;
        }

        foreach ($discounts as &$discount) {
            $discounts_value += (float) $discount['value'];
        }

        $this->discount_is_present = $discounts_value > 0;
        if ($this->discount_is_present) {
            /**
             * @since 2.3.0
             * if marked as true, will skip discount processing
             */
            $skip_discount_processing = false;
            // Getting the sum of all products
            $sum_total_price_gross = 0;
            foreach ($this->positions as &$p) {
                $sum_total_price_gross += $p['total_price_gross'];
            }

            /**
             * @since 2.2.7
             * Carriage discount support was added. Each cart rule has a bool field
             * called 'free_shipping'. It is set as 1 only if it was made from a voucher,
             * so this part will only work for voucher coupons.
             *
             * @since 2.3.1
             * Remember that right now, $sum_total_price_gross does not contain the carriage price
             */
            $carriage_discount = array_filter(
                $discounts,
                static function ($d) {
                    return $d["free_shipping"];
                }
            );
            if (count($carriage_discount) > 1) {
                $error_message = 'send_invoice [' . $this->order->id . ', ' . $this->order->reference .  ']: ';
                $this->addLog($error_message . "There are " . (string) count($carriage_discount) .
                              " carriage discounts!");
                return false;
            } elseif ($this->should_add_carriage) {
                if (count($carriage_discount) === 1) {
                    // Carriage is now free, that is, a discount is added
                    $carriage_discount = reset($carriage_discount);
                    if ($carriage_discount['value'] > $this->carriage['total_price_gross']) {
                        // carriage discount is bundled with product discount
                        $discounts_value -= $this->carriage['total_price_gross'];
                    } else {
                        $discounts_value -= $carriage_discount['value'];
                    }
                    $this->carriage['discount'] = $this->carriage['total_price_gross'];
                } elseif (round($sum_total_price_gross) == round($discounts_value, 2)) {
                    /**
                     * @since 2.3.0
                     * discounts_value == products_value
                     * so discount won't be applied to carriage, but will be
                     * applied to every other thing
                     */
                    foreach ($this->positions as &$p) {
                        $p['discount'] = $p['total_price_gross'];
                    }
                    $skip_discount_processing = true;
                }
            }
            unset($carriage_discount);

            if (!$skip_discount_processing) {
                foreach ($this->positions as &$p) {
                    $p['discount'] = round(
                        $this->calculatePositionDiscount(
                            $p['total_price_gross'],
                            $sum_total_price_gross,
                            $discounts_value
                        ),
                        2
                    );
                }

                // Adding one penny to discount if overflow
                $check_discount = 0;
                # Using & cause of some kind of a bug - iterated penultimate loop twice
                foreach ($this->positions as &$p) {
                    $check_discount += $p['discount'];
                }

                // Round for precision errors, not to return unwanted value
                $check_discount = round($check_discount, 3);
                $discounts_value = round($discounts_value, 3);
                if ($check_discount !== $discounts_value) {
                    // There was an overflow. Will add a penny to the most expensive position.
                    $prices = array_map(
                        static function ($p) {
                            return $p["total_price_gross"];
                        },
                        $this->positions
                    );
                    $indexes_with_highest_value = array_keys($prices, max($prices));
                    $position = reset($indexes_with_highest_value);

                    // fixing the penny
                    $this->positions[$position]['discount'] += $discounts_value - $check_discount;

                    $check_discount = 0;
                    foreach ($this->positions as &$p) {
                        $check_discount += $p['discount'];
                    }

                    assert($check_discount === $discounts_value);
                }
            }
        }
    }

    private function isFee($cart_rule)
    {
        return isset($cart_rule['is_fee']) && $cart_rule['is_fee'] > 0;
    }

    private function setupCarriage()
    {
        // Checking if "include_free_shipment" is set. If the shipment costs 0 and previous is true, does not include
        // the shipment to invoice
        if ($this->c->incl_free_shipment || round((float) $this->order->total_shipping_tax_incl, 2) > 0) {
            $name = $this->module->l('shipping');

            if ($this->c->use_carrier_name) {
                $shipping = $this->order->getShipping();
                if (!empty($shipping[0]['carrier_name'])) {
                    $name = $shipping[0]['carrier_name'];
                }
            } elseif (!empty($this->c->override_carrier_name)) {
                $name = $this->c->override_carrier_name;
            }

            $this->carriage = array(
                'name' => $name,
                'quantity' => 1,
                'total_price_gross' => $this->order->total_shipping_tax_incl,
                'tax' => round((float) $this->order->carrier_tax_rate, 2),
                'service' => true,
            );
        }
    }

    private function addCarriage()
    {
        if ($this->should_add_carriage) {
            if (((
                    isset($this->carriage['discount']) &&
                    $this->carriage['discount'] == $this->carriage['total_price_gross']
                ) ||
                (
                    !isset($this->carriage['discount']) &&
                    $this->carriage['total_price_gross'] == 0
                )) &&
                $this->c->incl_free_shipment
            ) {
                $this->carriage['total_price_gross'] = 0;
                $this->carriage['discount'] = 0;
                $this->carriage['tax'] = 0;
                $this->positions[] = $this->carriage;
            } elseif ((isset($this->carriage['discount']) &&
                    $this->carriage['discount'] != $this->carriage['total_price_gross']) ||
                    (!isset($this->carriage['discount']) && $this->carriage['total_price_gross'] > 0)) {
                $this->positions[] = $this->carriage;
            }
        }
    }

    private function addOrderFee()
    {
        // Adding fee as last position
        if (isset($this->order->payment_fee)) {
            $fee = round((float) $this->order->payment_fee, 2);
            if ($fee) {
                $this->positions[] = array(
                    'name' => $this->module->l('commission'),
                    'quantity' => 1,
                    'total_price_gross' => $fee,
                    'tax' => 0,
                    'service' => true,
                    //'code' =>
                );
            }
        }
    }

    private function setupWrapping()
    {
        // Gift wrap stuff
        $wrapping_total_price_gloss = round($this->order->total_wrapping_tax_incl, 2);
        $wrapping_tax_rule_group = Configuration::get('PS_GIFT_WRAPPING_TAX_RULES_GROUP');
        if ($wrapping_tax_rule_group > 0) {
            $wrapping_tax = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
                'SELECT *
                FROM `'._DB_PREFIX_.'tax_rule` tr
                LEFT JOIN `'._DB_PREFIX_.'tax` ta ON (tr.id_tax = ta.id_tax)
                WHERE tr.`id_tax_rules_group`='.(int)$wrapping_tax_rule_group.' AND tr.`id_country`='.$this->country_id
            );

            if (!empty($wrapping_tax)) {
                $wrapping_tax = $wrapping_tax[0]['rate'];
                assert(!empty($wrapping_tax));
            }
        }
        if (empty($wrapping_tax)) {
            // Heuristic: take first correct VAT percentage from $positions
            $wrapping_tax = array_filter(
                $this->positions,
                static function ($e) {
                    $t = (float) $e["tax"];
                    return !empty($t);
                }
            );
            $wrapping_tax = (empty($wrapping_tax) ? 0 : reset($wrapping_tax));
        }
        if ($wrapping_total_price_gloss > 0) {
            $this->wrapping = array(
                'name' => $this->module->l('wrapping'),
                'quantity' => 1,
                'total_price_gross' => $wrapping_total_price_gloss,
                'tax' => round($wrapping_tax, 2),
                'service' => true,
            );
        }
    }

    private function disableTaxesIfNecessary()
    {
        // Checking whether to disable taxes or not
        $disable_taxes = true;
        foreach ($this->positions as $pos) {
            if ($pos['tax'] != 'disabled') {
                $disable_taxes = false;
                break;
            }
        }

        if (isset($this->carriage) && gettype($this->carriage) == 'array') {
            $disable_carriage_tax = $disable_taxes &&
                ($this->carriage['tax'] == 0 || $this->order->total_shipping_tax_incl == 0);
        }

        if (isset($this->wrapping) && gettype($this->wrapping) == 'array') {
            $disable_wrapping_tax = $disable_taxes &&
                ($this->wrapping['tax'] == 0 || $this->order->total_wrapping_tax_incl == 0);
        }

        if (isset($disable_wrapping_tax) && $disable_wrapping_tax &&
            isset($disable_carriage_tax) && $disable_carriage_tax) {
            $this->wrapping['tax'] = 'disabled';
            $this->carriage['tax'] = 'disabled';
        } elseif (isset($disable_wrapping_tax) && $disable_wrapping_tax && !isset($disable_carriage_tax)) {
            $this->wrapping['tax'] = 'disabled';
        } elseif (isset($disable_carriage_tax) && $disable_carriage_tax && !isset($disable_wrapping_tax)) {
            $this->carriage['tax'] = 'disabled';
        }

        // Adding to positions
        if (isset($this->wrapping) && gettype($this->wrapping) == 'array') {
            $this->positions[] = $this->wrapping;
        }

        // Decide whether to add carriage or not.
        $this->should_add_carriage = isset($this->carriage) && gettype($this->carriage) == 'array';
    }

    private function setupKind($kind = '')
    {
        if ($this->module->correctFirmlet('BF', 'FT') && $kind == '') {
            // Automatically issued invoice
            if ($this->c->issue_kind === 'always_receipt') {
                $kind = 'receipt';
            } elseif ($this->c->issue_kind === 'always_proforma') {
                $kind = 'proforma';
            } elseif ($this->module->correctFirmlet('FT')) {
                // Kinds available only in FT
                if ($this->c->issue_kind === 'always_estimate') {
                    $kind = 'estimate';
                } elseif ($this->c->issue_kind === 'vat_or_receipt') {
                    $kind = ($this->isValidNIP($this->address->vat_number) &&
                        !empty($this->address->company)) ? 'vat' : 'receipt';
                } elseif ($this->c->issue_kind === 'always_bill') {
                    $kind = 'bill';
                }
            } else {
                $kind = 'vat';
            }
        } elseif ($this->module->correctFirmlet('FT')) {
            if (!in_array($kind, array('vat', 'receipt', 'proforma', 'estimate', 'bill'))) {
                // faktura tworzona reczn
                $kind = ($this->isValidNIP($this->address->vat_number) &&
                    !empty($this->address->company)) ? 'vat' : 'receipt';
            }
        } else {
            // VosFactures
            $kind = 'vat';
        }

        if ($this->module->correctFirmlet('FT')) {
            // Tax is disabled on bills
            if ($kind === 'bill') {
                foreach ($this->positions as &$p) {
                    $p['tax'] = 'disabled';
                }
            }
        }

        $this->kind = $kind;
    }

    // TODO: use context instead of direct constructs
    private function init($order)
    {
        $this->module = & VosFacturesApp::getInstance();
        $this->c = & $this->module->c;

        $this->address = new Address((int) $order->id_address_invoice);
        if (!Validate::isLoadedObject($this->address)) {
            return false;
        }

        // Used only if the deliver and invoice addresses differ
        $this->address_delivery = new Address((int) $order->id_address_delivery);
        if (!Validate::isLoadedObject($this->address_delivery)) {
            return false;
        }
        $this->country = new Country((int) $this->address->id_country);
        if (!Validate::isLoadedObject($this->country)) {
            return false;
        }

        $this->country_id = (int) Context::getContext()->country->id;

        $this->country_delivery = new Country((int) $this->address_delivery->id_country);
        if (!Validate::isLoadedObject($this->country_delivery)) {
            return false;
        }
        $this->lang = new Language((int) $order->id_lang);
        if (!Validate::isLoadedObject($this->lang)) {
            return false;
        }
        $this->currency = new Currency((int) $order->id_currency);
        if (!Validate::isLoadedObject($this->currency)) {
            return false;
        }
        $this->customer = new Customer((int) $order->id_customer);
        if (!Validate::isLoadedObject($this->customer)) {
            return false;
        }
        // We do not validate it - no $gender->id means that there was no selected title.
        $this->gender = new Gender((int) $this->customer->id_gender, (int) Context::getContext()->language->id);
        if (!is_object($this->gender)) {
            return false;
        }

        return true;
    }

    private function setupPositions()
    {
        $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT
                od.product_name as name,
                od.product_quantity as quantity,
                od.total_price_tax_incl as total_price_gross,
                t.rate as tax,
                od.product_reference as code
            FROM `'._DB_PREFIX_.'order_detail` od
            LEFT JOIN `'._DB_PREFIX_.'order_detail_tax` odt ON (od.id_order_detail = odt.id_order_detail)
            LEFT JOIN `'._DB_PREFIX_.'tax` t ON (odt.id_tax = t.id_tax)
            WHERE od.id_order = ' . (int) ($this->order->id)
        );


        $positions = array();
        foreach ($products as $p) {
            $positions[] = array(
                'name' => $p['name'],
                'quantity' => $p['quantity'],
                'total_price_gross' => $p['total_price_gross'],
                'tax' => (is_null($p['tax']) ? 'disabled' : round((float) $p['tax'], 2)),
                'code' => $p['code'],
            );
        }

        $this->positions = $positions;
    }

    /**
     * @since 2.3.5 Fixed the problem with empty phone
     */
    private function setupPhone()
    {
        if (empty($this->address->phone) && empty($this->address->phone_mobile)) {
            $this->phone = "";
        } elseif (empty($this->address->phone)) {
            $this->phone = $this->address->phone_mobile;
        } elseif (empty($this->address->phone_mobile)) {
            $this->phone = $this->address->phone;
        } else {
            $this->phone = "{$this->address->phone}, {$this->address->phone_mobile}";
        }
    }

    private function getFastBayDetails() {
        $db = new VosFacturesAppDatabase();
        $info = $db->getFastBayInfo((int)$this->order->id);

        if ($info) {
            $this->internal_note = 'Ebay order id: ' . $info['ebay_order_id'].', Ebay buyer: ' . $info['ebay_buyer'];
        }
    }

    private function setupBuyerName()
    {
        $company = $this->address->company;
        $full_name = trim($this->address->firstname . ' ' . $this->address->lastname);

        switch ($this->c->company_or_full_name) {
            case 'company':
                $this->buyer_name = empty($company) ? $full_name : $company;
                break;
            case 'full_name':
                $this->buyer_name = empty($full_name) ? $company : $full_name;
                break;
            case 'company_and_full_name':
                if (empty($company)) {
                    $this->buyer_name = $full_name;
                } elseif (empty($full_name)) {
                    $this->buyer_name = $company;
                } else {
                    $this->buyer_name = $company . ', ' . $full_name;
                }
                break;
            default:
                $this->module->addLog("Company or full name is incorrect");
        }
    }


    private function prepareInvoiceData()
    {
        $this->invoice_data = array(
            'kind' => $this->kind,
            'issue_date' => $this->order->invoice_date,
            'buyer_first_name' => $this->address->firstname,
            'buyer_last_name' => $this->address->lastname,
            'internal_note' => $this->internal_note,
            'buyer_name' => $this->buyer_name,
            'buyer_city' => $this->address->city,
            'buyer_phone' => $this->phone,
            'buyer_country' => $this->country->iso_code,
            'buyer_post_code' => $this->address->postcode,
            'buyer_street' => (
                empty($this->address->address2)
                ? $this->address->address1
                : $this->address->address1 . ', ' . $this->address->address2
            ),
            'oid' => $this->order->getUniqReference(),
            'buyer_email' => $this->customer->email,
            'buyer_tax_no' => $this->address->vat_number,
            'payment_type' => $this->order->payment,
            'positions' => $this->positions,
            'lang' => Tools::strtolower(Tools::substr($this->lang->language_code, 0, 2)),
            'currency' => $this->currency->iso_code,
            'buyer_title' => $this->gender->name,
            'buyer_company' => !empty($this->address->company),
            'origin' => $this->module->getOrigin(),
            'skip_buyer_last_name_validation' => true,
        );
        /*
        * Greek language symbol in presta is 'el' while in firmlet this is 'he'
        */
        if ($this->invoice_data['lang'] == 'el') {
            $this->invoice_data['lang'] = 'he';
        }

        if ($this->c->incl_delivery_date && $this->order->hasBeenDelivered()) {
            $this->invoice_data['sell_date'] = date('Y-m-d', strtotime($this->order->delivery_date));
        }

        if ($this->c->incl_private_note && $this->order->getCustomer() && $this->order->getCustomer()->note) {
            $this->invoice_data['description'] = $this->order->getCustomer()->note;
        }
    }

    public function setupDeliveryAddress()
    {
        if ($this->address->id == $this->address_delivery->id) {
            return;
        }

        $this->invoice_data['use_delivery_address'] = true;
        $this->invoice_data['delivery_address'] =
            (!empty($this->address_delivery->company) ?
                $this->address_delivery->company :
                $this->address_delivery->firstname . ' ' . $this->address_delivery->lastname)
            . PHP_EOL . $this->address_delivery->address1 .
            (empty($this->address_delivery->address2) ? '' : PHP_EOL . $this->address_delivery->address2)
            . PHP_EOL . $this->address_delivery->postcode . ' ' . $this->address_delivery->city
            . ', ' . $this->country_delivery->name['2']; // Country name in customer's language
    }

    public function replaceInvoiceDataFields()
    {
        /* Setting additional fields
            Note: if option is illegal, it is not added to the invoice data
                  and a new line is added to logger */
        $additional_fields = $this->module->getAdditionalFields();
        if ($additional_fields != null) {
            foreach ($additional_fields as $key => $value) {
                if (in_array($key, $this->module->getIllegalFields())) {
                    $error_message = "create invoice data: '" . $key . "' CANNOT be defined by additional_fields";
                    $this->module->addLog($error_message, 3, null, 'Order', (int)$this->order->id);
                } else {
                    $this->invoice_data[$key] = $value;
                }
            }
        }
    }

    private function prepareFinalInvoiceData()
    {
        $this->prepareInvoiceData();
        $this->addCustomerMessages();

        if (isset($this->discount_is_present) && $this->discount_is_present) {
            $this->invoice_data['discount_kind'] = 'amount';
            $this->invoice_data['show_discount'] = 'true';
        }

        $this->setupDeliveryAddress();

        if (!empty($this->c->department_id)) {
            $this->invoice_data['department_id'] = $this->c->department_id;
        }

        if (!empty($this->c->category_id)) {
            $this->invoice_data['category_id'] = $this->c->category_id;
        }

        $this->replaceInvoiceDataFields();

        return $this->invoice_data;
    }


    /**
     * Used in FT only
     */
    private function isValidNIP($nip)
    {
        $nip = preg_replace('/\D/', '', $nip);

        if (Tools::strlen($nip) != 10) {
            return false;
        }

        $weights = array(6, 5, 7, 2, 3, 4, 5, 6, 7);
        $control = 0;

        for ($i = 0; $i < 9; $i++) {
            $control += $weights[$i] * $nip[$i];
        }
        $control = $control % 11;

        return $control == $nip[9];
    }
}
