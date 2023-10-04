{*
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
*}
{if $ps_version >= '1.6'}
<div class="tab-pane " id="vosfacturesapp">
{else}
<br/>
{/if}
<fieldset>
    {* Module info *}
    <legend>
        <img src="{html_entity_decode($module_dir|escape:'htmlall':'UTF-8')}logo.png" height="32" width="32">
        {l s='integration_with' mod='vosfacturesapp'}
        <a target="_blank" href="{$account_url|escape:'htmlall':'UTF-8'}">{$account_url|escape:'htmlall':'UTF-8'}</a>
    </legend>


    {* Debug section *}
    {if $debug}
        {include '../debug/debug.tpl' active_multistore=$active_multistore context=$debug_context}
    {/if}


    {* Multistore warning *}
    {if $active_multistore}
        {if !$is_same_shop}
            {assign "current_shop_name" $current_shop_data['shop_name']}
            {assign "current_shop_group_name" $current_shop_data['shop_group_name']}
            {assign "order_shop_name" $order_shop_data['shop_name']}
            {assign "order_shop_group_name" $order_shop_data['shop_group_name']}

            <p>
                <strong><span style="color: red">{l s='multistore_is_enabled_warning' mod='vosfacturesapp'}</span></strong><br/>
                {if $current_shop_name && $current_shop_group_name}
                    {l s='currently_chosen_shop_and_group' sprintf=[$current_shop_name, $current_shop_group_name] mod='vosfacturesapp'}
                {elseif $current_shop_group_name}
                    {l s='currently_chosen_group' sprintf=$current_shop_group_name mod='vosfacturesapp'}
                {else}
                    {l s='currently_chosen_all' mod='vosfacturesapp'}
                {/if}
                <br/>
                {if $order_shop_name && $order_shop_group_name}
                    {l s='below_options_are_for_shop_and_group' sprintf=[$order_shop_name, $order_shop_group_name] mod='vosfacturesapp'}
                {elseif $order_shop_group_name}
                    {l s='below_options_are_for_group' sprintf=$order_shop_group_name mod='vosfacturesapp'}
                {else}
                    {l s='below_options_are_for_all' mod='vosfacturesapp'}
                {/if}
            </p>
        {/if}
    {/if}


    {* Error handling *}
    {if $VF}
        {if $this->isOverridingDisabled()}
            {html_entity_decode($this->displayError($this->l('overriding_is_disabled'))|escape:'htmlall':'UTF-8')}
        {/if}
    {/if}
    {if !empty($container->last_error)}
        {html_entity_decode($this->displayError($container->last_error)|escape:'htmlall':'UTF-8')}
    {/if}
    {if $VF && !$invoices_enabled}
        {html_entity_decode($this->displayError($this->l('invoices_are_disabled'))|escape:'htmlall':'UTF-8')}
    {elseif !$container->isConfigured()}
        {html_entity_decode($this->displayError($this->l('empty_configuration_warning'))|escape:'htmlall':'UTF-8')}
    {else}
        {assign "order_url" "{$vfAdminLink}&tab=AdminVosFacturesApp&amp;id_order={$order->id|intval}"}

        {if !empty($invoice) && !empty($invoice['external_id']) && empty($invoice['error'])}
            {* An invoice was detected! *}
            <a target="_blank" href="{html_entity_decode($this->getInvoiceUrl($invoice['external_id'])|escape:'htmlall':'UTF-8')}">{l s='show_document_on_account' mod='vosfacturesapp'}</a><br />
            <a target="_blank" href="{html_entity_decode($invoice['view_url']|escape:'htmlall':'UTF-8')}.pdf">{l s='download_pdf' mod='vosfacturesapp'}</a><br />
            <a href="{html_entity_decode($order_url|escape:'htmlall':'UTF-8')}&amp;command=remove"
                   onclick="return confirm('{l s='confirm_remove_invoice' mod='vosfacturesapp'}');">{l s='remove_invoice' mod='vosfacturesapp'}</a><br />
            <a target="_blank" href="{html_entity_decode($this->getInvoicesUrl()|escape:'htmlall':'UTF-8')}/new?from={$invoice['external_id']|intval}&amp;kind=correction">{l s='issue_correction_invoice' mod='vosfacturesapp'}</a><br />
        {else}
            {if !empty($invoice) && (empty($invoice['external_id']) || !empty($invoice['error']))}
                {html_entity_decode($this->displayError($invoice['error'])|escape:'htmlall':'UTF-8')}
            {/if}

            {* No invoice detected or error form *}
            {if $VF}
                {* VosFactures version *}
                {l s='no_invoice' mod='vosfacturesapp'}<br />
                <a href="{html_entity_decode($order_url|escape:'htmlall':'UTF-8')}&amp;command=issue">{l s='issue_invoice' mod='vosfacturesapp'}</a><br />
            {elseif $FT || $BF}
                {if $FT}
                    <a href="{html_entity_decode($order_url|escape:'htmlall':'UTF-8')}&amp;command=issue&amp;kind=vat_or_receipt">{l s='issue_vat_or_receipt' mod='vosfacturesapp'}</a><br />
                {/if}
                <a href="{html_entity_decode($order_url|escape:'htmlall':'UTF-8')}&amp;command=issue&amp;kind=vat">{l s='issue_vat' mod='vosfacturesapp'}</a><br />
                <a href="{html_entity_decode($order_url|escape:'htmlall':'UTF-8')}&amp;command=issue&amp;kind=receipt">{l s='issue_receipt' mod='vosfacturesapp'}</a><br />
                <a href="{html_entity_decode($order_url|escape:'htmlall':'UTF-8')}&amp;command=issue&amp;kind=proforma">{l s='issue_proforma' mod='vosfacturesapp'}</a><br />
                {if $FT}
                    <a href="{html_entity_decode($order_url|escape:'htmlall':'UTF-8')}&amp;command=issue&amp;kind=estimate">{l s='issue_estimate' mod='vosfacturesapp'}</a><br />
                    <a href="{html_entity_decode($order_url|escape:'htmlall':'UTF-8')}&amp;command=issue&amp;kind=bill">{l s='issue_bill' mod='vosfacturesapp'}</a><br />
                {/if}
            {/if}
        {/if}
    {/if}
</fieldset>
{if $ps_version >= '1.6'}
</div>
{/if}
