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
use Symfony\Component\DependencyInjection\Compiler\RegisterEnvVarProcessorsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\EnvVarProcessorInterface;

class RegisterEnvVarProcessorsPassTest extends TestCase
{
    public function testSimpleProcessor()
    {
        $container = new ContainerBuilder();
        $container->register('foo', SimpleProcessor::class)->addTag('container.env_var_processor');

        (new RegisterEnvVarProcessorsPass())->process($container);

        $this->assertTrue($container->has('container.env_var_processors_locator'));
        $this->assertInstanceOf(SimpleProcessor::class, $container->get('container.env_var_processors_locator')->get('foo'));

        $expected = [
            'foo' => ['string'],
            'base64' => ['string'],
            'bool' => ['bool'],
            'const' => ['bool', 'int', 'float', 'string', 'array'],
            'file' => ['string'],
            'float' => ['float'],
            'int' => ['int'],
            'json' => ['array'],
            'resolve' => ['string'],
            'string' => ['string'],
        ];

        $this->assertSame($expected, $container->getParameterBag()->getProvidedTypes());
    }

    public function testNoProcessor()
    {
        $container = new ContainerBuilder();

        (new RegisterEnvVarProcessorsPass())->process($container);

        $this->assertFalse($container->has('container.env_var_processors_locator'));
    }

    public function testBadProcessor()
    {
        $this->expectException('Symfony\Component\DependencyInjection\Exception\InvalidArgumentException');
        $this->expectExceptionMessage('Invalid type "foo" returned by "Symfony\Component\DependencyInjection\Tests\Compiler\BadProcessor::getProvidedTypes()", expected one of "array", "bool", "float", "int", "string".');
        $container = new ContainerBuilder();
        $container->register('foo', BadProcessor::class)->addTag('container.env_var_processor');

        (new RegisterEnvVarProcessorsPass())->process($container);
    }
}

class SimpleProcessor implements EnvVarProcessorInterface
{
    public function getEnv($prefix, $name, \Closure $getEnv)
    {
        return $getEnv($name);
    }

    public static function getProvidedTypes()
    {
        return ['foo' => 'string'];
    }
}

class BadProcessor extends SimpleProcessor
{
    public static function getProvidedTypes()
    {
        return ['foo' => 'string|foo'];
    }
}
