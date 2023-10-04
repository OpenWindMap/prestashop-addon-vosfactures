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
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ExtensionCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

/**
 * @author Wouter J <wouter@wouterj.nl>
 */
class ExtensionCompilerPassTest extends TestCase
{
    private $container;
    private $pass;

    protected function setUp()
    {
        $this->container = new ContainerBuilder();
        $this->pass = new ExtensionCompilerPass();
    }

    public function testProcess()
    {
        $extension1 = new CompilerPassExtension('extension1');
        $extension2 = new DummyExtension('extension2');
        $extension3 = new DummyExtension('extension3');
        $extension4 = new CompilerPassExtension('extension4');

        $this->container->registerExtension($extension1);
        $this->container->registerExtension($extension2);
        $this->container->registerExtension($extension3);
        $this->container->registerExtension($extension4);

        $this->pass->process($this->container);

        $this->assertTrue($this->container->hasDefinition('extension1'));
        $this->assertFalse($this->container->hasDefinition('extension2'));
        $this->assertFalse($this->container->hasDefinition('extension3'));
        $this->assertTrue($this->container->hasDefinition('extension4'));
    }
}

class DummyExtension extends Extension
{
    private $alias;

    public function __construct($alias)
    {
        $this->alias = $alias;
    }

    public function getAlias()
    {
        return $this->alias;
    }

    public function load(array $configs, ContainerBuilder $container)
    {
    }

    public function process(ContainerBuilder $container)
    {
        $container->register($this->alias);
    }
}

class CompilerPassExtension extends DummyExtension implements CompilerPassInterface
{
}
