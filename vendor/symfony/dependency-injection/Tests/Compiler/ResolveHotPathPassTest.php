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

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Tests\Compiler;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Argument\IteratorArgument;
use Symfony\Component\DependencyInjection\Compiler\ResolveHotPathPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class ResolveHotPathPassTest extends TestCase
{
    public function testProcess()
    {
        $container = new ContainerBuilder();

        $container->register('foo')
            ->addTag('container.hot_path')
            ->addArgument(new IteratorArgument([new Reference('lazy')]))
            ->addArgument(new Reference('service_container'))
            ->addArgument(new Definition('', [new Reference('bar')]))
            ->addArgument(new Reference('baz', ContainerBuilder::IGNORE_ON_UNINITIALIZED_REFERENCE))
            ->addArgument(new Reference('missing'))
        ;

        $container->register('lazy');
        $container->register('bar')
            ->addArgument(new Reference('buz'))
            ->addArgument(new Reference('deprec_ref_notag'));
        $container->register('baz')
            ->addArgument(new Reference('lazy'))
            ->addArgument(new Reference('lazy'));
        $container->register('buz');
        $container->register('deprec_with_tag')->setDeprecated()->addTag('container.hot_path');
        $container->register('deprec_ref_notag')->setDeprecated();

        (new ResolveHotPathPass())->process($container);

        $this->assertFalse($container->getDefinition('lazy')->hasTag('container.hot_path'));
        $this->assertTrue($container->getDefinition('bar')->hasTag('container.hot_path'));
        $this->assertTrue($container->getDefinition('buz')->hasTag('container.hot_path'));
        $this->assertFalse($container->getDefinition('baz')->hasTag('container.hot_path'));
        $this->assertFalse($container->getDefinition('service_container')->hasTag('container.hot_path'));
        $this->assertFalse($container->getDefinition('deprec_with_tag')->hasTag('container.hot_path'));
        $this->assertFalse($container->getDefinition('deprec_ref_notag')->hasTag('container.hot_path'));
    }
}
