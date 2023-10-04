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

use Psr\Log\NullLogger;
use Symfony\Component\Cache\Adapter\ApcuAdapter;

class ApcuAdapterTest extends AdapterTestCase
{
    protected $skippedTests = [
        'testExpiration' => 'Testing expiration slows down the test suite',
        'testHasItemReturnsFalseWhenDeferredItemIsExpired' => 'Testing expiration slows down the test suite',
        'testDefaultLifeTime' => 'Testing expiration slows down the test suite',
    ];

    public function createCachePool($defaultLifetime = 0)
    {
        if (!\function_exists('apcu_fetch') || !filter_var(ini_get('apc.enabled'), \FILTER_VALIDATE_BOOLEAN)) {
            $this->markTestSkipped('APCu extension is required.');
        }
        if ('cli' === \PHP_SAPI && !filter_var(ini_get('apc.enable_cli'), \FILTER_VALIDATE_BOOLEAN)) {
            if ('testWithCliSapi' !== $this->getName()) {
                $this->markTestSkipped('apc.enable_cli=1 is required.');
            }
        }
        if ('\\' === \DIRECTORY_SEPARATOR) {
            $this->markTestSkipped('Fails transiently on Windows.');
        }

        return new ApcuAdapter(str_replace('\\', '.', __CLASS__), $defaultLifetime);
    }

    public function testUnserializable()
    {
        $pool = $this->createCachePool();

        $item = $pool->getItem('foo');
        $item->set(function () {});

        $this->assertFalse($pool->save($item));

        $item = $pool->getItem('foo');
        $this->assertFalse($item->isHit());
    }

    public function testVersion()
    {
        $namespace = str_replace('\\', '.', static::class);

        $pool1 = new ApcuAdapter($namespace, 0, 'p1');

        $item = $pool1->getItem('foo');
        $this->assertFalse($item->isHit());
        $this->assertTrue($pool1->save($item->set('bar')));

        $item = $pool1->getItem('foo');
        $this->assertTrue($item->isHit());
        $this->assertSame('bar', $item->get());

        $pool2 = new ApcuAdapter($namespace, 0, 'p2');

        $item = $pool2->getItem('foo');
        $this->assertFalse($item->isHit());
        $this->assertNull($item->get());

        $item = $pool1->getItem('foo');
        $this->assertFalse($item->isHit());
        $this->assertNull($item->get());
    }

    public function testNamespace()
    {
        $namespace = str_replace('\\', '.', static::class);

        $pool1 = new ApcuAdapter($namespace.'_1', 0, 'p1');

        $item = $pool1->getItem('foo');
        $this->assertFalse($item->isHit());
        $this->assertTrue($pool1->save($item->set('bar')));

        $item = $pool1->getItem('foo');
        $this->assertTrue($item->isHit());
        $this->assertSame('bar', $item->get());

        $pool2 = new ApcuAdapter($namespace.'_2', 0, 'p1');

        $item = $pool2->getItem('foo');
        $this->assertFalse($item->isHit());
        $this->assertNull($item->get());

        $item = $pool1->getItem('foo');
        $this->assertTrue($item->isHit());
        $this->assertSame('bar', $item->get());
    }

    public function testWithCliSapi()
    {
        try {
            // disable PHPUnit error handler to mimic a production environment
            $isCalled = false;
            set_error_handler(function () use (&$isCalled) {
                $isCalled = true;
            });
            $pool = new ApcuAdapter(str_replace('\\', '.', __CLASS__));
            $pool->setLogger(new NullLogger());

            $item = $pool->getItem('foo');
            $item->isHit();
            $pool->save($item->set('bar'));
            $this->assertFalse($isCalled);
        } finally {
            restore_error_handler();
        }
    }
}