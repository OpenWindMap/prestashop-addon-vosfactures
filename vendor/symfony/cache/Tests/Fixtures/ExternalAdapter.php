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

namespace Symfony\Component\Cache\Tests\Fixtures;

use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

/**
 * Adapter not implementing the {@see \Symfony\Component\Cache\Adapter\AdapterInterface}.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class ExternalAdapter implements CacheItemPoolInterface
{
    private $cache;

    public function __construct($defaultLifetime = 0)
    {
        $this->cache = new ArrayAdapter($defaultLifetime);
    }

    public function getItem($key)
    {
        return $this->cache->getItem($key);
    }

    public function getItems(array $keys = [])
    {
        return $this->cache->getItems($keys);
    }

    public function hasItem($key)
    {
        return $this->cache->hasItem($key);
    }

    public function clear()
    {
        return $this->cache->clear();
    }

    public function deleteItem($key)
    {
        return $this->cache->deleteItem($key);
    }

    public function deleteItems(array $keys)
    {
        return $this->cache->deleteItems($keys);
    }

    public function save(CacheItemInterface $item)
    {
        return $this->cache->save($item);
    }

    public function saveDeferred(CacheItemInterface $item)
    {
        return $this->cache->saveDeferred($item);
    }

    public function commit()
    {
        return $this->cache->commit();
    }
}
