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

namespace Symfony\Component\Config\Tests\Loader;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Loader\LoaderResolver;

class LoaderResolverTest extends TestCase
{
    public function testConstructor()
    {
        $resolver = new LoaderResolver([
            $loader = $this->getMockBuilder('Symfony\Component\Config\Loader\LoaderInterface')->getMock(),
        ]);

        $this->assertEquals([$loader], $resolver->getLoaders(), '__construct() takes an array of loaders as its first argument');
    }

    public function testResolve()
    {
        $loader = $this->getMockBuilder('Symfony\Component\Config\Loader\LoaderInterface')->getMock();
        $resolver = new LoaderResolver([$loader]);
        $this->assertFalse($resolver->resolve('foo.foo'), '->resolve() returns false if no loader is able to load the resource');

        $loader = $this->getMockBuilder('Symfony\Component\Config\Loader\LoaderInterface')->getMock();
        $loader->expects($this->once())->method('supports')->willReturn(true);
        $resolver = new LoaderResolver([$loader]);
        $this->assertEquals($loader, $resolver->resolve(function () {}), '->resolve() returns the loader for the given resource');
    }

    public function testLoaders()
    {
        $resolver = new LoaderResolver();
        $resolver->addLoader($loader = $this->getMockBuilder('Symfony\Component\Config\Loader\LoaderInterface')->getMock());

        $this->assertEquals([$loader], $resolver->getLoaders(), 'addLoader() adds a loader');
    }
}
