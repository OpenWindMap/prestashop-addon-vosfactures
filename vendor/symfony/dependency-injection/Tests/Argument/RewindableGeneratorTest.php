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

namespace Symfony\Component\DependencyInjection\Tests\Argument;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;

class RewindableGeneratorTest extends TestCase
{
    public function testImplementsCountable()
    {
        $this->assertInstanceOf(\Countable::class, new RewindableGenerator(function () {
            yield 1;
        }, 1));
    }

    public function testCountUsesProvidedValue()
    {
        $generator = new RewindableGenerator(function () {
            yield 1;
        }, 3);

        $this->assertCount(3, $generator);
    }

    public function testCountUsesProvidedValueAsCallback()
    {
        $called = 0;
        $generator = new RewindableGenerator(function () {
            yield 1;
        }, function () use (&$called) {
            ++$called;

            return 3;
        });

        $this->assertSame(0, $called, 'Count callback is called lazily');
        $this->assertCount(3, $generator);

        \count($generator);

        $this->assertSame(1, $called, 'Count callback is called only once');
    }
}
