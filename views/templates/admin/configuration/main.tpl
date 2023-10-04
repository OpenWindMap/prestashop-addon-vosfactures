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
{* Errors section. Each $err is already an html element. *}
{if $settings['is_submit']}
	{if empty($errors)}
		{html_entity_decode($this->displayConfirmation($this->l('settings_saved'))|escape:'htmlall':'UTF-8')}
	{else}
		{foreach from=$errors item=err}
			{html_entity_decode($err|escape:'htmlall':'UTF-8')}
		{/foreach}
	{/if}
{/if}


{* Debug section *}
{if $debug}
	{include '../debug/debug.tpl' active_multistore=$active_multistore context=$debug_context}
{/if}


{* Module info *}
<h2>
	{$this->displayName|escape:'htmlall':'UTF-8'}
</h2>
<img src='{$module_dir|escape:'htmlall':'UTF-8'}logo.png' height="32" width="32" style='float:left; margin-right:15px;'>
<p>
	<b>{l s='description_short' mod='vosfacturesapp'}</b><br/><br/>
	{l s='description_long' mod='vosfacturesapp'}
</p>
<p>
	{l s='new_account_info' mod='vosfacturesapp'}
	<a href='{$this->new_account_url|escape:'htmlall':'UTF-8'}' target='_blank'>
		<button>{l s='create_new_account' mod='vosfacturesapp'}</button>
	</a>
</p>
<p>
	<a href='{$this->help_url|escape:'htmlall':'UTF-8'}' target='_blank'>
		<button>{l s='help_url_label' mod='vosfacturesapp'}</button>
	</a>
</p><br>


{* Form *}
{include './_form.tpl' settings=$settings FT=$FT VF=$VF BF=$BF}


{* Extra *}
{if $is_configured}
	{if $debug}
		<br/>
		<fieldset>
			<legend>{l s='various' mod='vosfacturesapp'}</legend>
			<a href='{$module_dir|escape:'htmlall':'UTF-8'}module_controller.php?c=get_csv'>{l s='get_csv_products' mod='vosfacturesapp'}</a>
		</fieldset>
	{/if}
{/if}
