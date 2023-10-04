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

use Symfony\Component\DependencyInjection\Argument\IteratorArgument;
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

$container = new ContainerBuilder();

$container
    ->register('foo1', 'stdClass')
    ->setPublic(true)
;

$container
    ->register('foo2', 'stdClass')
    ->setPublic(false)
;

$container
    ->register('foo3', 'stdClass')
    ->setPublic(false)
;

$container
    ->register('baz', 'stdClass')
    ->setProperty('foo3', new Reference('foo3'))
    ->setPublic(true)
;

$container
    ->register('bar', 'stdClass')
    ->setProperty('foo1', new Reference('foo1', $container::IGNORE_ON_UNINITIALIZED_REFERENCE))
    ->setProperty('foo2', new Reference('foo2', $container::IGNORE_ON_UNINITIALIZED_REFERENCE))
    ->setProperty('foo3', new Reference('foo3', $container::IGNORE_ON_UNINITIALIZED_REFERENCE))
    ->setProperty('closures', [
        new ServiceClosureArgument(new Reference('foo1', $container::IGNORE_ON_UNINITIALIZED_REFERENCE)),
        new ServiceClosureArgument(new Reference('foo2', $container::IGNORE_ON_UNINITIALIZED_REFERENCE)),
        new ServiceClosureArgument(new Reference('foo3', $container::IGNORE_ON_UNINITIALIZED_REFERENCE)),
    ])
    ->setProperty('iter', new IteratorArgument([
        'foo1' => new Reference('foo1', $container::IGNORE_ON_UNINITIALIZED_REFERENCE),
        'foo2' => new Reference('foo2', $container::IGNORE_ON_UNINITIALIZED_REFERENCE),
        'foo3' => new Reference('foo3', $container::IGNORE_ON_UNINITIALIZED_REFERENCE),
    ]))
    ->setPublic(true)
;

return $container;
