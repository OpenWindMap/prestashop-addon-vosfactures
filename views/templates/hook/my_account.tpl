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
{if $ps_version >= '1.7'}
	<a id="fakturownia-my-invoices-link"
		class="col-lg-4 col-md-6 col-sm-6 col-xs-12"
		title="{l s='my_invoices' mod='vosfacturesapp'}"
		href="{html_entity_decode($link->getModuleLink('vosfacturesapp', 'invoices')|escape:'htmlall':'UTF-8')}" title="{l s='my_invoices' mod='vosfacturesapp'}">
		<span class="link-item">
			<i class="material-icons"></i>
			{l s='my_invoices' mod='vosfacturesapp'}
		</span>
	</a>
{else}
<li>
	{if $ps_version >= '1.6'}
		<a href="{html_entity_decode($link->getModuleLink('vosfacturesapp', 'invoices')|escape:'htmlall':'UTF-8')}" title="{l s='my_invoices' mod='vosfacturesapp'}">
			<i class="icon-file-o"></i>
			<span>{l s='my_invoices' mod='vosfacturesapp'}</span>
		</a>
	{else if $ps_version >= '1.5'}
		<a href="{html_entity_decode($link->getModuleLink('vosfacturesapp', 'invoices')|escape:'htmlall':'UTF-8')}" title="{l s='my_invoices' mod='vosfacturesapp'}">
			<img src="{html_entity_decode($img_dir|escape:'htmlall':'UTF-8')}icon/slip.gif" alt="{$this->l('my_invoices')|escape:'htmlall':'UTF-8'}" class="icon" /> {l s='my_invoices' mod='vosfacturesapp'}
		</a>
	{/if}
</li>
{/if}
