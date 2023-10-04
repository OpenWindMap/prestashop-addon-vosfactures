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

namespace Symfony\Component\DependencyInjection\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\DefinitionDecorator;

/**
 * @group legacy
 */
class DefinitionDecoratorTest extends TestCase
{
    public function testConstructor()
    {
        $def = new DefinitionDecorator('foo');

        $this->assertEquals('foo', $def->getParent());
        $this->assertEquals([], $def->getChanges());
    }

    /**
     * @dataProvider getPropertyTests
     */
    public function testSetProperty($property, $changeKey)
    {
        $def = new DefinitionDecorator('foo');

        $getter = 'get'.ucfirst($property);
        $setter = 'set'.ucfirst($property);

        $this->assertNull($def->$getter());
        $this->assertSame($def, $def->$setter('foo'));
        $this->assertEquals('foo', $def->$getter());
        $this->assertEquals([$changeKey => true], $def->getChanges());
    }

    public function getPropertyTests()
    {
        return [
            ['class', 'class'],
            ['factory', 'factory'],
            ['configurator', 'configurator'],
            ['file', 'file'],
        ];
    }

    public function testSetPublic()
    {
        $def = new DefinitionDecorator('foo');

        $this->assertTrue($def->isPublic());
        $this->assertSame($def, $def->setPublic(false));
        $this->assertFalse($def->isPublic());
        $this->assertEquals(['public' => true], $def->getChanges());
    }

    public function testSetLazy()
    {
        $def = new DefinitionDecorator('foo');

        $this->assertFalse($def->isLazy());
        $this->assertSame($def, $def->setLazy(false));
        $this->assertFalse($def->isLazy());
        $this->assertEquals(['lazy' => true], $def->getChanges());
    }

    public function testSetAutowired()
    {
        $def = new DefinitionDecorator('foo');

        $this->assertFalse($def->isAutowired());
        $this->assertSame($def, $def->setAutowired(true));
        $this->assertTrue($def->isAutowired());
        $this->assertSame(['autowired' => true], $def->getChanges());
    }

    public function testSetArgument()
    {
        $def = new DefinitionDecorator('foo');

        $this->assertEquals([], $def->getArguments());
        $this->assertSame($def, $def->replaceArgument(0, 'foo'));
        $this->assertEquals(['index_0' => 'foo'], $def->getArguments());
    }

    public function testReplaceArgumentShouldRequireIntegerIndex()
    {
        $this->expectException('InvalidArgumentException');
        $def = new DefinitionDecorator('foo');

        $def->replaceArgument('0', 'foo');
    }

    public function testReplaceArgument()
    {
        $def = new DefinitionDecorator('foo');

        $def->setArguments([0 => 'foo', 1 => 'bar']);
        $this->assertEquals('foo', $def->getArgument(0));
        $this->assertEquals('bar', $def->getArgument(1));

        $this->assertSame($def, $def->replaceArgument(1, 'baz'));
        $this->assertEquals('foo', $def->getArgument(0));
        $this->assertEquals('baz', $def->getArgument(1));

        $this->assertEquals([0 => 'foo', 1 => 'bar', 'index_1' => 'baz'], $def->getArguments());
    }

    public function testGetArgumentShouldCheckBounds()
    {
        $this->expectException('OutOfBoundsException');
        $def = new DefinitionDecorator('foo');

        $def->setArguments([0 => 'foo']);
        $def->replaceArgument(0, 'foo');

        $def->getArgument(1);
    }
}
