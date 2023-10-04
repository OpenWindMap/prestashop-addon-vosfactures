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
use Symfony\Component\DependencyInjection\Compiler\CheckDefinitionValidityPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CheckDefinitionValidityPassTest extends TestCase
{
    public function testProcessDetectsSyntheticNonPublicDefinitions()
    {
        $this->expectException('Symfony\Component\DependencyInjection\Exception\RuntimeException');
        $container = new ContainerBuilder();
        $container->register('a')->setSynthetic(true)->setPublic(false);

        $this->process($container);
    }

    public function testProcessDetectsNonSyntheticNonAbstractDefinitionWithoutClass()
    {
        $this->expectException('Symfony\Component\DependencyInjection\Exception\RuntimeException');
        $container = new ContainerBuilder();
        $container->register('a')->setSynthetic(false)->setAbstract(false);

        $this->process($container);
    }

    public function testProcess()
    {
        $container = new ContainerBuilder();
        $container->register('a', 'class');
        $container->register('b', 'class')->setSynthetic(true)->setPublic(true);
        $container->register('c', 'class')->setAbstract(true);
        $container->register('d', 'class')->setSynthetic(true);

        $this->process($container);

        $this->addToAssertionCount(1);
    }

    public function testValidTags()
    {
        $container = new ContainerBuilder();
        $container->register('a', 'class')->addTag('foo', ['bar' => 'baz']);
        $container->register('b', 'class')->addTag('foo', ['bar' => null]);
        $container->register('c', 'class')->addTag('foo', ['bar' => 1]);
        $container->register('d', 'class')->addTag('foo', ['bar' => 1.1]);

        $this->process($container);

        $this->addToAssertionCount(1);
    }

    public function testInvalidTags()
    {
        $this->expectException('Symfony\Component\DependencyInjection\Exception\RuntimeException');
        $container = new ContainerBuilder();
        $container->register('a', 'class')->addTag('foo', ['bar' => ['baz' => 'baz']]);

        $this->process($container);
    }

    public function testDynamicPublicServiceName()
    {
        $this->expectException('Symfony\Component\DependencyInjection\Exception\EnvParameterException');
        $container = new ContainerBuilder();
        $env = $container->getParameterBag()->get('env(BAR)');
        $container->register("foo.$env", 'class')->setPublic(true);

        $this->process($container);
    }

    public function testDynamicPublicAliasName()
    {
        $this->expectException('Symfony\Component\DependencyInjection\Exception\EnvParameterException');
        $container = new ContainerBuilder();
        $env = $container->getParameterBag()->get('env(BAR)');
        $container->setAlias("foo.$env", 'class')->setPublic(true);

        $this->process($container);
    }

    public function testDynamicPrivateName()
    {
        $container = new ContainerBuilder();
        $env = $container->getParameterBag()->get('env(BAR)');
        $container->register("foo.$env", 'class');
        $container->setAlias("bar.$env", 'class');

        $this->process($container);

        $this->addToAssertionCount(1);
    }

    protected function process(ContainerBuilder $container)
    {
        $pass = new CheckDefinitionValidityPass();
        $pass->process($container);
    }
}
