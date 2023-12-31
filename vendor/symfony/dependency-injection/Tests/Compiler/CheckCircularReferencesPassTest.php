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
use Symfony\Component\DependencyInjection\Compiler\AnalyzeServiceReferencesPass;
use Symfony\Component\DependencyInjection\Compiler\CheckCircularReferencesPass;
use Symfony\Component\DependencyInjection\Compiler\Compiler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class CheckCircularReferencesPassTest extends TestCase
{
    public function testProcess()
    {
        $this->expectException('Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException');
        $container = new ContainerBuilder();
        $container->register('a')->addArgument(new Reference('b'));
        $container->register('b')->addArgument(new Reference('a'));

        $this->process($container);
    }

    public function testProcessWithAliases()
    {
        $this->expectException('Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException');
        $container = new ContainerBuilder();
        $container->register('a')->addArgument(new Reference('b'));
        $container->setAlias('b', 'c');
        $container->setAlias('c', 'a');

        $this->process($container);
    }

    public function testProcessWithFactory()
    {
        $this->expectException('Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException');
        $container = new ContainerBuilder();

        $container
            ->register('a', 'stdClass')
            ->setFactory([new Reference('b'), 'getInstance']);

        $container
            ->register('b', 'stdClass')
            ->setFactory([new Reference('a'), 'getInstance']);

        $this->process($container);
    }

    public function testProcessDetectsIndirectCircularReference()
    {
        $this->expectException('Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException');
        $container = new ContainerBuilder();
        $container->register('a')->addArgument(new Reference('b'));
        $container->register('b')->addArgument(new Reference('c'));
        $container->register('c')->addArgument(new Reference('a'));

        $this->process($container);
    }

    public function testProcessDetectsIndirectCircularReferenceWithFactory()
    {
        $this->expectException('Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException');
        $container = new ContainerBuilder();

        $container->register('a')->addArgument(new Reference('b'));

        $container
            ->register('b', 'stdClass')
            ->setFactory([new Reference('c'), 'getInstance']);

        $container->register('c')->addArgument(new Reference('a'));

        $this->process($container);
    }

    public function testDeepCircularReference()
    {
        $this->expectException('Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException');
        $container = new ContainerBuilder();
        $container->register('a')->addArgument(new Reference('b'));
        $container->register('b')->addArgument(new Reference('c'));
        $container->register('c')->addArgument(new Reference('b'));

        $this->process($container);
    }

    public function testProcessIgnoresMethodCalls()
    {
        $container = new ContainerBuilder();
        $container->register('a')->addArgument(new Reference('b'));
        $container->register('b')->addMethodCall('setA', [new Reference('a')]);

        $this->process($container);

        $this->addToAssertionCount(1);
    }

    public function testProcessIgnoresLazyServices()
    {
        $container = new ContainerBuilder();
        $container->register('a')->setLazy(true)->addArgument(new Reference('b'));
        $container->register('b')->addArgument(new Reference('a'));

        $this->process($container);

        // just make sure that a lazily loaded service does not trigger a CircularReferenceException
        $this->addToAssertionCount(1);
    }

    public function testProcessIgnoresIteratorArguments()
    {
        $container = new ContainerBuilder();
        $container->register('a')->addArgument(new Reference('b'));
        $container->register('b')->addArgument(new IteratorArgument([new Reference('a')]));

        $this->process($container);

        // just make sure that an IteratorArgument does not trigger a CircularReferenceException
        $this->addToAssertionCount(1);
    }

    protected function process(ContainerBuilder $container)
    {
        $compiler = new Compiler();
        $passConfig = $compiler->getPassConfig();
        $passConfig->setOptimizationPasses([
            new AnalyzeServiceReferencesPass(true),
            new CheckCircularReferencesPass(),
        ]);
        $passConfig->setRemovingPasses([]);

        $compiler->compile($container);
    }
}
