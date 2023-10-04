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

namespace Symfony\Component\Config\Tests\Definition;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\ArrayNode;
use Symfony\Component\Config\Definition\ScalarNode;

class ScalarNodeTest extends TestCase
{
    /**
     * @dataProvider getValidValues
     */
    public function testNormalize($value)
    {
        $node = new ScalarNode('test');
        $this->assertSame($value, $node->normalize($value));
    }

    public function getValidValues()
    {
        return [
            [false],
            [true],
            [null],
            [''],
            ['foo'],
            [0],
            [1],
            [0.0],
            [0.1],
        ];
    }

    public function testSetDeprecated()
    {
        $childNode = new ScalarNode('foo');
        $childNode->setDeprecated('"%node%" is deprecated');

        $this->assertTrue($childNode->isDeprecated());
        $this->assertSame('"foo" is deprecated', $childNode->getDeprecationMessage($childNode->getName(), $childNode->getPath()));

        $node = new ArrayNode('root');
        $node->addChild($childNode);

        $deprecationTriggered = 0;
        $deprecationHandler = function ($level, $message, $file, $line) use (&$prevErrorHandler, &$deprecationTriggered) {
            if (\E_USER_DEPRECATED === $level) {
                return ++$deprecationTriggered;
            }

            return $prevErrorHandler ? $prevErrorHandler($level, $message, $file, $line) : false;
        };

        $prevErrorHandler = set_error_handler($deprecationHandler);
        $node->finalize([]);
        restore_error_handler();
        $this->assertSame(0, $deprecationTriggered, '->finalize() should not trigger if the deprecated node is not set');

        $prevErrorHandler = set_error_handler($deprecationHandler);
        $node->finalize(['foo' => '']);
        restore_error_handler();
        $this->assertSame(1, $deprecationTriggered, '->finalize() should trigger if the deprecated node is set');
    }

    /**
     * @dataProvider getInvalidValues
     */
    public function testNormalizeThrowsExceptionOnInvalidValues($value)
    {
        $this->expectException('Symfony\Component\Config\Definition\Exception\InvalidTypeException');
        $node = new ScalarNode('test');
        $node->normalize($value);
    }

    public function getInvalidValues()
    {
        return [
            [[]],
            [['foo' => 'bar']],
            [new \stdClass()],
        ];
    }

    public function testNormalizeThrowsExceptionWithoutHint()
    {
        $node = new ScalarNode('test');

        $this->expectException('Symfony\Component\Config\Definition\Exception\InvalidTypeException');
        $this->expectExceptionMessage('Invalid type for path "test". Expected scalar, but got array.');

        $node->normalize([]);
    }

    public function testNormalizeThrowsExceptionWithErrorMessage()
    {
        $node = new ScalarNode('test');
        $node->setInfo('"the test value"');

        $this->expectException('Symfony\Component\Config\Definition\Exception\InvalidTypeException');
        $this->expectExceptionMessage("Invalid type for path \"test\". Expected scalar, but got array.\nHint: \"the test value\"");

        $node->normalize([]);
    }

    /**
     * @dataProvider getValidNonEmptyValues
     *
     * @param mixed $value
     */
    public function testValidNonEmptyValues($value)
    {
        $node = new ScalarNode('test');
        $node->setAllowEmptyValue(false);

        $this->assertSame($value, $node->finalize($value));
    }

    public function getValidNonEmptyValues()
    {
        return [
            [false],
            [true],
            ['foo'],
            [0],
            [1],
            [0.0],
            [0.1],
        ];
    }

    /**
     * @dataProvider getEmptyValues
     *
     * @param mixed $value
     */
    public function testNotAllowedEmptyValuesThrowException($value)
    {
        $this->expectException('Symfony\Component\Config\Definition\Exception\InvalidConfigurationException');
        $node = new ScalarNode('test');
        $node->setAllowEmptyValue(false);
        $node->finalize($value);
    }

    public function getEmptyValues()
    {
        return [
            [null],
            [''],
        ];
    }
}
