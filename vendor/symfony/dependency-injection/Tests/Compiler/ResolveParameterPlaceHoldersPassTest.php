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
use Symfony\Component\DependencyInjection\Compiler\ResolveParameterPlaceHoldersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;

class ResolveParameterPlaceHoldersPassTest extends TestCase
{
    private $compilerPass;
    private $container;
    private $fooDefinition;

    protected function setUp()
    {
        $this->compilerPass = new ResolveParameterPlaceHoldersPass();
        $this->container = $this->createContainerBuilder();
        $this->compilerPass->process($this->container);
        $this->fooDefinition = $this->container->getDefinition('foo');
    }

    public function testClassParametersShouldBeResolved()
    {
        $this->assertSame('Foo', $this->fooDefinition->getClass());
    }

    public function testFactoryParametersShouldBeResolved()
    {
        $this->assertSame(['FooFactory', 'getFoo'], $this->fooDefinition->getFactory());
    }

    public function testArgumentParametersShouldBeResolved()
    {
        $this->assertSame(['bar', ['bar' => 'baz']], $this->fooDefinition->getArguments());
    }

    public function testMethodCallParametersShouldBeResolved()
    {
        $this->assertSame([['foobar', ['bar', ['bar' => 'baz']]]], $this->fooDefinition->getMethodCalls());
    }

    public function testPropertyParametersShouldBeResolved()
    {
        $this->assertSame(['bar' => 'baz'], $this->fooDefinition->getProperties());
    }

    public function testFileParametersShouldBeResolved()
    {
        $this->assertSame('foo.php', $this->fooDefinition->getFile());
    }

    public function testAliasParametersShouldBeResolved()
    {
        $this->assertSame('foo', $this->container->getAlias('bar')->__toString());
    }

    public function testBindingsShouldBeResolved()
    {
        list($boundValue) = $this->container->getDefinition('foo')->getBindings()['$baz']->getValues();

        $this->assertSame($this->container->getParameterBag()->resolveValue('%env(BAZ)%'), $boundValue);
    }

    public function testParameterNotFoundExceptionsIsThrown()
    {
        $this->expectException(ParameterNotFoundException::class);
        $this->expectExceptionMessage('The service "baz_service_id" has a dependency on a non-existent parameter "non_existent_param".');

        $containerBuilder = new ContainerBuilder();
        $definition = $containerBuilder->register('baz_service_id');
        $definition->setArgument(0, '%non_existent_param%');

        $pass = new ResolveParameterPlaceHoldersPass();
        $pass->process($containerBuilder);
    }

    public function testParameterNotFoundExceptionsIsNotThrown()
    {
        $containerBuilder = new ContainerBuilder();
        $definition = $containerBuilder->register('baz_service_id');
        $definition->setArgument(0, '%non_existent_param%');

        $pass = new ResolveParameterPlaceHoldersPass(true, false);
        $pass->process($containerBuilder);

        $this->assertCount(1, $definition->getErrors());
    }

    private function createContainerBuilder()
    {
        $containerBuilder = new ContainerBuilder();

        $containerBuilder->setParameter('foo.class', 'Foo');
        $containerBuilder->setParameter('foo.factory.class', 'FooFactory');
        $containerBuilder->setParameter('foo.arg1', 'bar');
        $containerBuilder->setParameter('foo.arg2', ['%foo.arg1%' => 'baz']);
        $containerBuilder->setParameter('foo.method', 'foobar');
        $containerBuilder->setParameter('foo.property.name', 'bar');
        $containerBuilder->setParameter('foo.property.value', 'baz');
        $containerBuilder->setParameter('foo.file', 'foo.php');
        $containerBuilder->setParameter('alias.id', 'bar');

        $fooDefinition = $containerBuilder->register('foo', '%foo.class%');
        $fooDefinition->setFactory(['%foo.factory.class%', 'getFoo']);
        $fooDefinition->setArguments(['%foo.arg1%', ['%foo.arg1%' => 'baz']]);
        $fooDefinition->addMethodCall('%foo.method%', ['%foo.arg1%', '%foo.arg2%']);
        $fooDefinition->setProperty('%foo.property.name%', '%foo.property.value%');
        $fooDefinition->setFile('%foo.file%');
        $fooDefinition->setBindings(['$baz' => '%env(BAZ)%']);

        $containerBuilder->setAlias('%alias.id%', 'foo');

        return $containerBuilder;
    }
}
