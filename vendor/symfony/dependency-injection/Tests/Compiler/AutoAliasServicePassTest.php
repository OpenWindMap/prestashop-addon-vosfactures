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
use Symfony\Component\DependencyInjection\Compiler\AutoAliasServicePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AutoAliasServicePassTest extends TestCase
{
    public function testProcessWithMissingParameter()
    {
        $this->expectException('Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException');
        $container = new ContainerBuilder();

        $container->register('example')
            ->addTag('auto_alias', ['format' => '%non_existing%.example']);

        $pass = new AutoAliasServicePass();
        $pass->process($container);
    }

    public function testProcessWithMissingFormat()
    {
        $this->expectException('Symfony\Component\DependencyInjection\Exception\InvalidArgumentException');
        $container = new ContainerBuilder();

        $container->register('example')
            ->addTag('auto_alias', []);
        $container->setParameter('existing', 'mysql');

        $pass = new AutoAliasServicePass();
        $pass->process($container);
    }

    public function testProcessWithNonExistingAlias()
    {
        $container = new ContainerBuilder();

        $container->register('example', 'Symfony\Component\DependencyInjection\Tests\Compiler\ServiceClassDefault')
            ->addTag('auto_alias', ['format' => '%existing%.example']);
        $container->setParameter('existing', 'mysql');

        $pass = new AutoAliasServicePass();
        $pass->process($container);

        $this->assertEquals('Symfony\Component\DependencyInjection\Tests\Compiler\ServiceClassDefault', $container->getDefinition('example')->getClass());
    }

    public function testProcessWithExistingAlias()
    {
        $container = new ContainerBuilder();

        $container->register('example', 'Symfony\Component\DependencyInjection\Tests\Compiler\ServiceClassDefault')
            ->addTag('auto_alias', ['format' => '%existing%.example']);

        $container->register('mysql.example', 'Symfony\Component\DependencyInjection\Tests\Compiler\ServiceClassMysql');
        $container->setParameter('existing', 'mysql');

        $pass = new AutoAliasServicePass();
        $pass->process($container);

        $this->assertTrue($container->hasAlias('example'));
        $this->assertEquals('mysql.example', $container->getAlias('example'));
        $this->assertSame('Symfony\Component\DependencyInjection\Tests\Compiler\ServiceClassMysql', $container->getDefinition('mysql.example')->getClass());
    }

    public function testProcessWithManualAlias()
    {
        $container = new ContainerBuilder();

        $container->register('example', 'Symfony\Component\DependencyInjection\Tests\Compiler\ServiceClassDefault')
            ->addTag('auto_alias', ['format' => '%existing%.example']);

        $container->register('mysql.example', 'Symfony\Component\DependencyInjection\Tests\Compiler\ServiceClassMysql');
        $container->register('mariadb.example', 'Symfony\Component\DependencyInjection\Tests\Compiler\ServiceClassMariaDb');
        $container->setAlias('example', 'mariadb.example');
        $container->setParameter('existing', 'mysql');

        $pass = new AutoAliasServicePass();
        $pass->process($container);

        $this->assertTrue($container->hasAlias('example'));
        $this->assertEquals('mariadb.example', $container->getAlias('example'));
        $this->assertSame('Symfony\Component\DependencyInjection\Tests\Compiler\ServiceClassMariaDb', $container->getDefinition('mariadb.example')->getClass());
    }
}

class ServiceClassDefault
{
}

class ServiceClassMysql extends ServiceClassDefault
{
}

class ServiceClassMariaDb extends ServiceClassMysql
{
}
