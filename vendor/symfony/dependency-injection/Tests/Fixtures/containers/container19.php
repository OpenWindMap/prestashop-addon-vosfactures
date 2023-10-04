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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

require_once __DIR__.'/../includes/classes.php';

$container = new ContainerBuilder();

$container->setParameter('env(FOO)', 'Bar\FaooClass');
$container->setParameter('foo', '%env(FOO)%');

$container
    ->register('service_from_anonymous_factory', '%foo%')
    ->setFactory([new Definition('%foo%'), 'getInstance'])
    ->setPublic(true)
;

$anonymousServiceWithFactory = new Definition('Bar\FooClass');
$anonymousServiceWithFactory->setFactory('Bar\FooClass::getInstance');
$container
    ->register('service_with_method_call_and_factory', 'Bar\FooClass')
    ->addMethodCall('setBar', [$anonymousServiceWithFactory])
    ->setPublic(true)
;

return $container;
