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
{* Header *}
{if $ps_version < '1.6'}
    <h2>VosFactures</h2>
{else}
    <div class="panel">
        <div class="panel-heading">VosFactures</div>
{/if}
        {if $is_configured && isset($pathVendor)}
            <div style="margin-bottom: 20px;">
                <link href="{$pathVendor|escape:'htmlall':'UTF-8'}" rel=preload as=script>
                <link href="{$pathApp|escape:'htmlall':'UTF-8'}" rel=preload as=script>

                <div id="app"></div>

                <script src="{$pathVendor|escape:'htmlall':'UTF-8'}"></script>
                <script src="{$pathApp|escape:'htmlall':'UTF-8'}"></script>

                <style>
                    #content.nobootstrap div.bootstrap.panel {
                        display: none;
                    }
                </style>
            </div>
        {/if}

    {if $debug}
        {* Debug section *}
        {include '../debug/debug.tpl' active_multistore=$active_multistore context=$debug_context}
    {/if}

    {* Module info *}
    <img src="{$module_dir|escape:'htmlall':'UTF-8'}logo.png" height="32" width="32" style="float:left; margin-right:15px;">
    <p>
        <b>{l s='description_short' mod='vosfacturesapp'}</b><br/><br/>
        {l s='description_long' mod='vosfacturesapp'}
    </p>
    <p>
        {l s='new_account_info' mod='vosfacturesapp'}
        <a href="http://app.vosfactures.fr/account/new" target="_blank">
            <button class="{if $ps_version < '1.6'}button{else}btn btn-default{/if}">{l s='create_new_account' mod='vosfacturesapp'}</button>
        </a>
    </p>
    <p>
        <a href="http://aide.vosfactures.fr/929756-Module-Prestashop-Int-grer-votre-compte-Prestashop-avec-VosFacture..." target="_blank">
            <button class="{if $ps_version < '1.6'}button{else}btn btn-default{/if}">{l s='help_url_label' mod='vosfacturesapp'}</button>
        </a>
    </p>

{* Footer *}
{if $ps_version < '1.6'}
    <br/>
{else}
    </div>
{/if}
