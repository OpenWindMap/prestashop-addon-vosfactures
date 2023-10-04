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

namespace Symfony\Component\DependencyInjection\Tests\Loader;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\ClosureLoader;
use Symfony\Component\DependencyInjection\Loader\IniFileLoader;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class LoaderResolverTest extends TestCase
{
    private static $fixturesPath;

    /** @var LoaderResolver */
    private $resolver;

    protected function setUp()
    {
        self::$fixturesPath = realpath(__DIR__.'/../Fixtures/');

        $container = new ContainerBuilder();
        $this->resolver = new LoaderResolver([
            new XmlFileLoader($container, new FileLocator(self::$fixturesPath.'/xml')),
            new YamlFileLoader($container, new FileLocator(self::$fixturesPath.'/yaml')),
            new IniFileLoader($container, new FileLocator(self::$fixturesPath.'/ini')),
            new PhpFileLoader($container, new FileLocator(self::$fixturesPath.'/php')),
            new ClosureLoader($container),
        ]);
    }

    public function provideResourcesToLoad()
    {
        return [
            ['ini_with_wrong_ext.xml', 'ini', IniFileLoader::class],
            ['xml_with_wrong_ext.php', 'xml', XmlFileLoader::class],
            ['php_with_wrong_ext.yml', 'php', PhpFileLoader::class],
            ['yaml_with_wrong_ext.ini', 'yaml', YamlFileLoader::class],
        ];
    }

    /**
     * @dataProvider provideResourcesToLoad
     */
    public function testResolvesForcedType($resource, $type, $expectedClass)
    {
        $this->assertInstanceOf($expectedClass, $this->resolver->resolve($resource, $type));
    }
}
