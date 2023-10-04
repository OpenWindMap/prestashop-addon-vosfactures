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
<div style='width: 100%; margin: 10px auto 20px; border: 1px solid blue; background-color: yellow; padding: 5px;'>
    You are in debug mode!:<br/>
    {if !($active_multistore && isset($context['order_data']))}
    	Context settings:
        {include './shop_data.tpl' data=$context['shop_data'] prefix='Current'}
    {else}
        <table style='border: 1px solid #000; background-color: #EEE'>
            <thead>
                <tr>
                    <th style='border: 1px solid #000'>Context settings:</th>
                    <th style='border: 1px solid #000'>Order settings:</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style='width: 300px'                             >{include './shop_data.tpl' data=$context['shop_data']  prefix='Current'}</td>
                    <td style='border-left: 1px solid #000; width: 300px'>{include './shop_data.tpl' data=$context['order_data'] prefix='Order'}</td>
                </tr>
            </tbody>
        </table>
    {/if}
</div>
