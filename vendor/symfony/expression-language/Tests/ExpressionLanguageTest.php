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

namespace Symfony\Component\ExpressionLanguage\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\ParsedExpression;
use Symfony\Component\ExpressionLanguage\Tests\Fixtures\TestProvider;

class ExpressionLanguageTest extends TestCase
{
    public function testCachedParse()
    {
        $cacheMock = $this->getMockBuilder('Psr\Cache\CacheItemPoolInterface')->getMock();
        $cacheItemMock = $this->getMockBuilder('Psr\Cache\CacheItemInterface')->getMock();
        $savedParsedExpression = null;
        $expressionLanguage = new ExpressionLanguage($cacheMock);

        $cacheMock
            ->expects($this->exactly(2))
            ->method('getItem')
            ->with('1%20%2B%201%2F%2F')
            ->willReturn($cacheItemMock)
        ;

        $cacheItemMock
            ->expects($this->exactly(2))
            ->method('get')
            ->willReturnCallback(function () use (&$savedParsedExpression) {
                return $savedParsedExpression;
            })
        ;

        $cacheItemMock
            ->expects($this->exactly(1))
            ->method('set')
            ->with($this->isInstanceOf(ParsedExpression::class))
            ->willReturnCallback(function ($parsedExpression) use (&$savedParsedExpression) {
                $savedParsedExpression = $parsedExpression;
            })
        ;

        $cacheMock
            ->expects($this->exactly(1))
            ->method('save')
            ->with($cacheItemMock)
        ;

        $parsedExpression = $expressionLanguage->parse('1 + 1', []);
        $this->assertSame($savedParsedExpression, $parsedExpression);

        $parsedExpression = $expressionLanguage->parse('1 + 1', []);
        $this->assertSame($savedParsedExpression, $parsedExpression);
    }

    /**
     * @group legacy
     */
    public function testCachedParseWithDeprecatedParserCacheInterface()
    {
        $cacheMock = $this->getMockBuilder('Symfony\Component\ExpressionLanguage\ParserCache\ParserCacheInterface')->getMock();

        $savedParsedExpression = null;
        $expressionLanguage = new ExpressionLanguage($cacheMock);

        $cacheMock
            ->expects($this->exactly(1))
            ->method('fetch')
            ->with('1%20%2B%201%2F%2F')
            ->willReturn($savedParsedExpression)
        ;

        $cacheMock
            ->expects($this->exactly(1))
            ->method('save')
            ->with('1%20%2B%201%2F%2F', $this->isInstanceOf(ParsedExpression::class))
            ->willReturnCallback(function ($key, $expression) use (&$savedParsedExpression) {
                $savedParsedExpression = $expression;
            })
        ;

        $parsedExpression = $expressionLanguage->parse('1 + 1', []);
        $this->assertSame($savedParsedExpression, $parsedExpression);
    }

    public function testWrongCacheImplementation()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Cache argument has to implement "Psr\Cache\CacheItemPoolInterface".');
        $cacheMock = $this->getMockBuilder('Psr\Cache\CacheItemSpoolInterface')->getMock();
        new ExpressionLanguage($cacheMock);
    }

    public function testConstantFunction()
    {
        $expressionLanguage = new ExpressionLanguage();
        $this->assertEquals(\PHP_VERSION, $expressionLanguage->evaluate('constant("PHP_VERSION")'));

        $expressionLanguage = new ExpressionLanguage();
        $this->assertEquals('\constant("PHP_VERSION")', $expressionLanguage->compile('constant("PHP_VERSION")'));
    }

    public function testProviders()
    {
        $expressionLanguage = new ExpressionLanguage(null, [new TestProvider()]);
        $this->assertEquals('foo', $expressionLanguage->evaluate('identity("foo")'));
        $this->assertEquals('"foo"', $expressionLanguage->compile('identity("foo")'));
        $this->assertEquals('FOO', $expressionLanguage->evaluate('strtoupper("foo")'));
        $this->assertEquals('\strtoupper("foo")', $expressionLanguage->compile('strtoupper("foo")'));
        $this->assertEquals('foo', $expressionLanguage->evaluate('strtolower("FOO")'));
        $this->assertEquals('\strtolower("FOO")', $expressionLanguage->compile('strtolower("FOO")'));
        $this->assertTrue($expressionLanguage->evaluate('fn_namespaced()'));
        $this->assertEquals('\Symfony\Component\ExpressionLanguage\Tests\Fixtures\fn_namespaced()', $expressionLanguage->compile('fn_namespaced()'));
    }

    /**
     * @dataProvider shortCircuitProviderEvaluate
     */
    public function testShortCircuitOperatorsEvaluate($expression, array $values, $expected)
    {
        $expressionLanguage = new ExpressionLanguage();
        $this->assertEquals($expected, $expressionLanguage->evaluate($expression, $values));
    }

    /**
     * @dataProvider shortCircuitProviderCompile
     */
    public function testShortCircuitOperatorsCompile($expression, array $names, $expected)
    {
        $result = null;
        $expressionLanguage = new ExpressionLanguage();
        eval(sprintf('$result = %s;', $expressionLanguage->compile($expression, $names)));
        $this->assertSame($expected, $result);
    }

    public function testParseThrowsInsteadOfNotice()
    {
        $this->expectException('Symfony\Component\ExpressionLanguage\SyntaxError');
        $this->expectExceptionMessage('Unexpected end of expression around position 6 for expression `node.`.');
        $expressionLanguage = new ExpressionLanguage();
        $expressionLanguage->parse('node.', ['node']);
    }

    public function shortCircuitProviderEvaluate()
    {
        $object = $this->getMockBuilder('stdClass')->setMethods(['foo'])->getMock();
        $object->expects($this->never())->method('foo');

        return [
            ['false and object.foo()', ['object' => $object], false],
            ['false && object.foo()', ['object' => $object], false],
            ['true || object.foo()', ['object' => $object], true],
            ['true or object.foo()', ['object' => $object], true],
        ];
    }

    public function shortCircuitProviderCompile()
    {
        return [
            ['false and foo', ['foo' => 'foo'], false],
            ['false && foo', ['foo' => 'foo'], false],
            ['true || foo', ['foo' => 'foo'], true],
            ['true or foo', ['foo' => 'foo'], true],
        ];
    }

    public function testCachingForOverriddenVariableNames()
    {
        $expressionLanguage = new ExpressionLanguage();
        $expression = 'a + b';
        $expressionLanguage->evaluate($expression, ['a' => 1, 'b' => 1]);
        $result = $expressionLanguage->compile($expression, ['a', 'B' => 'b']);
        $this->assertSame('($a + $B)', $result);
    }

    public function testStrictEquality()
    {
        $expressionLanguage = new ExpressionLanguage();
        $expression = '123 === a';
        $result = $expressionLanguage->compile($expression, ['a']);
        $this->assertSame('(123 === $a)', $result);
    }

    public function testCachingWithDifferentNamesOrder()
    {
        $cacheMock = $this->getMockBuilder('Psr\Cache\CacheItemPoolInterface')->getMock();
        $cacheItemMock = $this->getMockBuilder('Psr\Cache\CacheItemInterface')->getMock();
        $expressionLanguage = new ExpressionLanguage($cacheMock);
        $savedParsedExpression = null;

        $cacheMock
            ->expects($this->exactly(2))
            ->method('getItem')
            ->with('a%20%2B%20b%2F%2Fa%7CB%3Ab')
            ->willReturn($cacheItemMock)
        ;

        $cacheItemMock
            ->expects($this->exactly(2))
            ->method('get')
            ->willReturnCallback(function () use (&$savedParsedExpression) {
                return $savedParsedExpression;
            })
        ;

        $cacheItemMock
            ->expects($this->exactly(1))
            ->method('set')
            ->with($this->isInstanceOf(ParsedExpression::class))
            ->willReturnCallback(function ($parsedExpression) use (&$savedParsedExpression) {
                $savedParsedExpression = $parsedExpression;
            })
        ;

        $cacheMock
            ->expects($this->exactly(1))
            ->method('save')
            ->with($cacheItemMock)
        ;

        $expression = 'a + b';
        $expressionLanguage->compile($expression, ['a', 'B' => 'b']);
        $expressionLanguage->compile($expression, ['B' => 'b', 'a']);
    }

    public function testOperatorCollisions()
    {
        $expressionLanguage = new ExpressionLanguage();
        $expression = 'foo.not in [bar]';
        $compiled = $expressionLanguage->compile($expression, ['foo', 'bar']);
        $this->assertSame('in_array($foo->not, [0 => $bar])', $compiled);

        $result = $expressionLanguage->evaluate($expression, ['foo' => (object) ['not' => 'test'], 'bar' => 'test']);
        $this->assertTrue($result);
    }

    /**
     * @dataProvider getRegisterCallbacks
     */
    public function testRegisterAfterParse($registerCallback)
    {
        $this->expectException('LogicException');
        $el = new ExpressionLanguage();
        $el->parse('1 + 1', []);
        $registerCallback($el);
    }

    /**
     * @dataProvider getRegisterCallbacks
     */
    public function testRegisterAfterEval($registerCallback)
    {
        $this->expectException('LogicException');
        $el = new ExpressionLanguage();
        $el->evaluate('1 + 1');
        $registerCallback($el);
    }

    public function testCallBadCallable()
    {
        $this->expectException('RuntimeException');
        $this->expectExceptionMessageMatches('/Unable to call method "\w+" of object "\w+"./');
        $el = new ExpressionLanguage();
        $el->evaluate('foo.myfunction()', ['foo' => new \stdClass()]);
    }

    /**
     * @dataProvider getRegisterCallbacks
     */
    public function testRegisterAfterCompile($registerCallback)
    {
        $this->expectException('LogicException');
        $el = new ExpressionLanguage();
        $el->compile('1 + 1');
        $registerCallback($el);
    }

    public function getRegisterCallbacks()
    {
        return [
            [
                function (ExpressionLanguage $el) {
                    $el->register('fn', function () {}, function () {});
                },
            ],
            [
                function (ExpressionLanguage $el) {
                    $el->addFunction(new ExpressionFunction('fn', function () {}, function () {}));
                },
            ],
            [
                function (ExpressionLanguage $el) {
                    $el->registerProvider(new TestProvider());
                },
            ],
        ];
    }
}
