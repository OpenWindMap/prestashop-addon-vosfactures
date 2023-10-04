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
use Symfony\Component\DependencyInjection\Argument\ServiceClosureArgument;
use Symfony\Component\DependencyInjection\Compiler\ResolveInvalidReferencesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Reference;

class ResolveInvalidReferencesPassTest extends TestCase
{
    public function testProcess()
    {
        $container = new ContainerBuilder();
        $def = $container
            ->register('foo')
            ->setArguments([
                new Reference('bar', ContainerInterface::NULL_ON_INVALID_REFERENCE),
                new Reference('baz', ContainerInterface::IGNORE_ON_INVALID_REFERENCE),
            ])
            ->addMethodCall('foo', [new Reference('moo', ContainerInterface::IGNORE_ON_INVALID_REFERENCE)])
        ;

        $this->process($container);

        $arguments = $def->getArguments();
        $this->assertSame([null, null], $arguments);
        $this->assertCount(0, $def->getMethodCalls());
    }

    public function testProcessIgnoreInvalidArgumentInCollectionArgument()
    {
        $container = new ContainerBuilder();
        $container->register('baz');
        $def = $container
            ->register('foo')
            ->setArguments([
                [
                    new Reference('bar', ContainerInterface::IGNORE_ON_INVALID_REFERENCE),
                    $baz = new Reference('baz', ContainerInterface::IGNORE_ON_INVALID_REFERENCE),
                    new Reference('moo', ContainerInterface::NULL_ON_INVALID_REFERENCE),
                ],
            ])
        ;

        $this->process($container);

        $arguments = $def->getArguments();
        $this->assertSame([$baz, null], $arguments[0]);
    }

    public function testProcessKeepMethodCallOnInvalidArgumentInCollectionArgument()
    {
        $container = new ContainerBuilder();
        $container->register('baz');
        $def = $container
            ->register('foo')
            ->addMethodCall('foo', [
                [
                    new Reference('bar', ContainerInterface::IGNORE_ON_INVALID_REFERENCE),
                    $baz = new Reference('baz', ContainerInterface::IGNORE_ON_INVALID_REFERENCE),
                    new Reference('moo', ContainerInterface::NULL_ON_INVALID_REFERENCE),
                ],
            ])
        ;

        $this->process($container);

        $calls = $def->getMethodCalls();
        $this->assertCount(1, $def->getMethodCalls());
        $this->assertSame([$baz, null], $calls[0][1][0]);
    }

    public function testProcessIgnoreNonExistentServices()
    {
        $container = new ContainerBuilder();
        $def = $container
            ->register('foo')
            ->setArguments([new Reference('bar')])
        ;

        $this->process($container);

        $arguments = $def->getArguments();
        $this->assertEquals('bar', (string) $arguments[0]);
    }

    public function testProcessRemovesPropertiesOnInvalid()
    {
        $container = new ContainerBuilder();
        $def = $container
            ->register('foo')
            ->setProperty('foo', new Reference('bar', ContainerInterface::IGNORE_ON_INVALID_REFERENCE))
        ;

        $this->process($container);

        $this->assertEquals([], $def->getProperties());
    }

    public function testProcessRemovesArgumentsOnInvalid()
    {
        $container = new ContainerBuilder();
        $def = $container
            ->register('foo')
            ->addArgument([
                [
                    new Reference('bar', ContainerInterface::IGNORE_ON_INVALID_REFERENCE),
                    new ServiceClosureArgument(new Reference('baz', ContainerInterface::IGNORE_ON_INVALID_REFERENCE)),
                ],
            ])
        ;

        $this->process($container);

        $this->assertSame([[[]]], $def->getArguments());
    }

    protected function process(ContainerBuilder $container)
    {
        $pass = new ResolveInvalidReferencesPass();
        $pass->process($container);
    }
}
