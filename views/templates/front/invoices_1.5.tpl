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
{capture name=path}
	<a href="{html_entity_decode($link->getPageLink('my-account', true)|escape:'htmlall':'UTF-8')}">{l s='my_account' mod='vosfacturesapp'}</a>
	<span class="navigation-pipe">{html_entity_decode($navigationPipe|escape:'htmlall':'UTF-8')}</span>{l s='my_invoices' mod='vosfacturesapp'}
{/capture}
{include file="$tpl_dir./breadcrumb.tpl"}


<h1>{l s='my_invoices' mod='vosfacturesapp'}</h1>
<p>{l s='my_invoices_description' mod='vosfacturesapp'}</p>


{if !($invoices && count($invoices))}
	<p class="warning">{l s='no_invoices_detected' mod='vosfacturesapp'}</p>
{else}
	<table id="invoice-list" class="std">
		<tr>
			<th class="first_item">{l s='order_reference' mod='vosfacturesapp'}</th>
			<th class="item">{l s='order_placement_date' mod='vosfacturesapp'}</th>
			<th class="item">{l s='total_price' mod='vosfacturesapp'}</th>
			<th class="last_item">{l s='invoice' mod='vosfacturesapp'}</th>
			{* todo nbsp? *}
		</tr>
		{foreach from=$invoices item=invoice_data name=myLoop}
			<tr class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if} {if $smarty.foreach.myLoop.index % 2}alternate_item{/if}">
				<td class="invoice_order_reference">{$invoice_data['reference']|escape:'htmlall':'UTF-8'}</td>
				<td class="invoice_date_add">{dateFormat date=$invoice_data['date_add'] full=0}</td>
				<td class="history_price"><span class="price">{displayPrice price=$invoice_data['total_paid'] currency=$invoice_data['id_currency'] no_utf8=false convert=false}</span></td>
				<td class="invoice_view_url">
					<a href="{html_entity_decode($invoice_data['view_url']|escape:'htmlall':'UTF-8')}" title="{l s='invoice')|escape:'htmlall':'UTF-8'}" target="_blank"><img src="{html_entity_decode($img_dir|escape:'htmlall':'UTF-8')}icon/pdf.gif" alt="{$module->l('invoice' mod='vosfacturesapp'}" class="icon" /></a>
					<a href="{html_entity_decode($invoice_data['view_url']|escape:'htmlall':'UTF-8')}" title="{l s='invoice')|escape:'htmlall':'UTF-8'}" target="_blank">{$module->l('PDF' mod='vosfacturesapp'}</a>
				</td>
			</tr>
		{/foreach}
	</table>
{/if}


<ul class="footer_links clearfix">
	<li><a href="{html_entity_decode($link->getPageLink('my-account', true)|escape:'htmlall':'UTF-8')}"><img src="{html_entity_decode($img_dir|escape:'htmlall':'UTF-8')}icon/my-account.gif" alt="" class="icon" /> {l s='back_to_my_account' mod='vosfacturesapp'}</a></li>
	<li class="f_right"><a href="{html_entity_decode($base_dir|escape:'htmlall':'UTF-8')}"><img src="{html_entity_decode($img_dir|escape:'htmlall':'UTF-8')}icon/home.gif" alt="" class="icon" /> {l s='my_account_home' mod='vosfacturesapp'}</a></li>
</ul>