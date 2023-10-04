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
{if $is_configured}
	{if $debug}
		{* Header *}
		{if $ps_version < '1.6'}
			<br/>
			<fieldset><legend>{l s='various' mod='vosfacturesapp'}</legend>
		{else}
			<div class="panel">
			<div class="panel-heading">{l s='various' mod='vosfacturesapp'}</div>
		{/if}

		{* Body *}
			{assign "get_csv_url" "index.php?tab=AdminVosFacturesApp&amp;command=get_csv&amp;token={getAdminToken tab='AdminVosFacturesApp'}"}
			<a href='{html_entity_decode($get_csv_url|escape:'htmlall':'UTF-8')}'>{l s='get_csv_products' mod='vosfacturesapp'}</a>
			<br />
			{assign "update_uuid_on_vf" "index.php?tab=AdminVosFacturesApp&amp;command=update_uuid_on_vf&amp;token={getAdminToken tab='AdminVosFacturesApp'}"}
			<a href='{html_entity_decode($update_uuid_on_vf|escape:'htmlall':'UTF-8')}'>Zaktualizuj UUID sklepu</a>

		{* Footer *}
		{if $ps_version < '1.6'}
			</fieldset>
		{else}
			</div>
		{/if}
	{/if}
{/if}


<script>
	if(!$('#identify_oss_on').is(':checked')) {
		$('#force_vat_on').parent().parent().parent().hide();
	}

	$('#identify_oss_on').on('change', function() {
		if(!$('#identify_oss_on').is(':checked')) {
			$('#force_vat_on').parent().parent().parent().hide();
		} else {
			$('#force_vat_on').parent().parent().parent().show();
		}
	});

	$('#identify_oss_off').on('change', function() {
		if(!$('#identify_oss_on').is(':checked')) {
			$('#force_vat_on').parent().parent().parent().hide();
		} else {
			$('#force_vat_on').parent().parent().parent().show();
		}
	});
</script>
