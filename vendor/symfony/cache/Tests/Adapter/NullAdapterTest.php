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
use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\NullAdapter;

/**
 * @group time-sensitive
 */
class NullAdapterTest extends TestCase
{
    public function createCachePool()
    {
        return new NullAdapter();
    }

    public function testGetItem()
    {
        $adapter = $this->createCachePool();

        $item = $adapter->getItem('key');
        $this->assertFalse($item->isHit());
        $this->assertNull($item->get(), "Item's value must be null when isHit is false.");
    }

    public function testHasItem()
    {
        $this->assertFalse($this->createCachePool()->hasItem('key'));
    }

    public function testGetItems()
    {
        $adapter = $this->createCachePool();

        $keys = ['foo', 'bar', 'baz', 'biz'];

        /** @var CacheItemInterface[] $items */
        $items = $adapter->getItems($keys);
        $count = 0;

        foreach ($items as $key => $item) {
            $itemKey = $item->getKey();

            $this->assertEquals($itemKey, $key, 'Keys must be preserved when fetching multiple items');
            $this->assertContains($key, $keys, 'Cache key can not change.');
            $this->assertFalse($item->isHit());

            // Remove $key for $keys
            foreach ($keys as $k => $v) {
                if ($v === $key) {
                    unset($keys[$k]);
                }
            }

            ++$count;
        }

        $this->assertSame(4, $count);
    }

    public function testIsHit()
    {
        $adapter = $this->createCachePool();

        $item = $adapter->getItem('key');
        $this->assertFalse($item->isHit());
    }

    public function testClear()
    {
        $this->assertTrue($this->createCachePool()->clear());
    }

    public function testDeleteItem()
    {
        $this->assertTrue($this->createCachePool()->deleteItem('key'));
    }

    public function testDeleteItems()
    {
        $this->assertTrue($this->createCachePool()->deleteItems(['key', 'foo', 'bar']));
    }

    public function testSave()
    {
        $adapter = $this->createCachePool();

        $item = $adapter->getItem('key');
        $this->assertFalse($item->isHit());
        $this->assertNull($item->get(), "Item's value must be null when isHit is false.");

        $this->assertFalse($adapter->save($item));
    }

    public function testDeferredSave()
    {
        $adapter = $this->createCachePool();

        $item = $adapter->getItem('key');
        $this->assertFalse($item->isHit());
        $this->assertNull($item->get(), "Item's value must be null when isHit is false.");

        $this->assertFalse($adapter->saveDeferred($item));
    }

    public function testCommit()
    {
        $adapter = $this->createCachePool();

        $item = $adapter->getItem('key');
        $this->assertFalse($item->isHit());
        $this->assertNull($item->get(), "Item's value must be null when isHit is false.");

        $this->assertFalse($adapter->saveDeferred($item));
        $this->assertFalse($this->createCachePool()->commit());
    }
}
