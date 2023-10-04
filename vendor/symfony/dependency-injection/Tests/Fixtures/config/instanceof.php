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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use App\FooService;
use Symfony\Component\DependencyInjection\Tests\Fixtures\Prototype;

return function (ContainerConfigurator $c) {
    $s = $c->services();
    $s->instanceof(Prototype\Foo::class)
        ->property('p', 0)
        ->call('setFoo', [ref('foo')])
        ->tag('tag', ['k' => 'v'])
        ->share(false)
        ->lazy()
        ->configurator('c')
        ->property('p', 1);

    $s->load(Prototype::class.'\\', '../Prototype')->exclude('../Prototype/*/*');

    $s->set('foo', FooService::class);
};
