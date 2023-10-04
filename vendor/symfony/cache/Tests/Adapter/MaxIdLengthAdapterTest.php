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

namespace Symfony\Component\Cache\Tests\Adapter;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\AbstractAdapter;

class MaxIdLengthAdapterTest extends TestCase
{
    public function testLongKey()
    {
        $cache = $this->getMockBuilder(MaxIdLengthAdapter::class)
            ->setConstructorArgs([str_repeat('-', 10)])
            ->setMethods(['doHave', 'doFetch', 'doDelete', 'doSave', 'doClear'])
            ->getMock();

        $cache->expects($this->exactly(2))
            ->method('doHave')
            ->withConsecutive(
                [$this->equalTo('----------:0GTYWa9n4ed8vqNlOT2iEr:')],
                [$this->equalTo('----------:---------------------------------------')]
            );

        $cache->hasItem(str_repeat('-', 40));
        $cache->hasItem(str_repeat('-', 39));
    }

    public function testLongKeyVersioning()
    {
        $cache = $this->getMockBuilder(MaxIdLengthAdapter::class)
            ->setConstructorArgs([str_repeat('-', 26)])
            ->getMock();

        $cache
            ->method('doFetch')
            ->willReturn(['2:']);

        $reflectionClass = new \ReflectionClass(AbstractAdapter::class);

        $reflectionMethod = $reflectionClass->getMethod('getId');
        $reflectionMethod->setAccessible(true);

        // No versioning enabled
        $this->assertEquals('--------------------------:------------', $reflectionMethod->invokeArgs($cache, [str_repeat('-', 12)]));
        $this->assertLessThanOrEqual(50, \strlen($reflectionMethod->invokeArgs($cache, [str_repeat('-', 12)])));
        $this->assertLessThanOrEqual(50, \strlen($reflectionMethod->invokeArgs($cache, [str_repeat('-', 23)])));
        $this->assertLessThanOrEqual(50, \strlen($reflectionMethod->invokeArgs($cache, [str_repeat('-', 40)])));

        $reflectionProperty = $reflectionClass->getProperty('versioningIsEnabled');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($cache, true);

        // Versioning enabled
        $this->assertEquals('--------------------------:2:------------', $reflectionMethod->invokeArgs($cache, [str_repeat('-', 12)]));
        $this->assertLessThanOrEqual(50, \strlen($reflectionMethod->invokeArgs($cache, [str_repeat('-', 12)])));
        $this->assertLessThanOrEqual(50, \strlen($reflectionMethod->invokeArgs($cache, [str_repeat('-', 23)])));
        $this->assertLessThanOrEqual(50, \strlen($reflectionMethod->invokeArgs($cache, [str_repeat('-', 40)])));
    }

    public function testTooLongNamespace()
    {
        $this->expectException('Symfony\Component\Cache\Exception\InvalidArgumentException');
        $this->expectExceptionMessage('Namespace must be 26 chars max, 40 given ("----------------------------------------")');
        $this->getMockBuilder(MaxIdLengthAdapter::class)
            ->setConstructorArgs([str_repeat('-', 40)])
            ->getMock();
    }
}

abstract class MaxIdLengthAdapter extends AbstractAdapter
{
    protected $maxIdLength = 50;

    public function __construct($ns)
    {
        parent::__construct($ns);
    }
}
