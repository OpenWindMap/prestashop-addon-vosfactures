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
{extends file='customer/page.tpl'}

{block name='page_title'}
  {l s='my_invoices' mod='vosfacturesapp'}
{/block}

{block name="page_content"}
	<h6>{l s='my_invoices_description' mod='vosfacturesapp'}</h6>

	<div class="block-center" id="block-history">
		{if !($invoices && count($invoices))}
			<p class="alert alert-warning">{l s='no_invoices_detected' mod='vosfacturesapp'}</p>
		{else}
			<table id="invoice-list" class="table table-striped table-bordered table-labeled hidden-sm-down">
				<thead class="thead-default">
					<tr>
						<th class="first_item" data-sort-ignore="true">{l s='order_reference' mod='vosfacturesapp'}</th>
						<th class="item">{l s='order_placement_date' mod='vosfacturesapp'}</th>
						<th class="item" data-hide="phone">{l s='total_price' mod='vosfacturesapp'}</th>
						<th class="last_item" data-sort-ignore="true" data-hide="phone,tablet">{l s='invoice' mod='vosfacturesapp'}</th>
					</tr>
				</thead>
				<tbody>
					{foreach from=$invoices item=invoice_data name=myLoop}
						<tr class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if} {if $smarty.foreach.myLoop.index % 2}alternate_item{/if}">
							<td class="invoice_order_reference bold">{$invoice_data['reference']|escape:'htmlall':'UTF-8'}</td>
							<td class="invoice_date_add bold" data-value="{html_entity_decode($invoice_data['date_add']|regex_replace:"/[\-\:\ ]/":""|escape:'htmlall':'UTF-8')}">
								{dateFormat date=$invoice_data['date_add'] full=0}
							</td>
							<td class="history_price">
								<span class="price">{$invoice_data['total_paid']|escape:'htmlall':'UTF-8'}</span>
							</td>
							<td class="invoice_view_url">
								<a class="link-button" href="{html_entity_decode($invoice_data['view_url']|escape:'htmlall':'UTF-8')}" title="{l s='invoice' mod='vosfacturesapp'}" target="_blank">
									<i class="material-icons">&#xE415;</i>
								</a>
							</td>
						</tr>
					{/foreach}
				</tbody>
			</table>
		{/if}
	</div>
{/block}
