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

namespace Symfony\Component\Cache\Traits;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @internal
 */
class RedisProxy
{
    private $redis;
    private $initializer;
    private $ready = false;

    public function __construct(\Redis $redis, \Closure $initializer)
    {
        $this->redis = $redis;
        $this->initializer = $initializer;
    }

    public function __call($method, array $args)
    {
        $this->ready ?: $this->ready = $this->initializer->__invoke($this->redis);

        return \call_user_func_array([$this->redis, $method], $args);
    }

    public function hscan($strKey, &$iIterator, $strPattern = null, $iCount = null)
    {
        $this->ready ?: $this->ready = $this->initializer->__invoke($this->redis);

        return $this->redis->hscan($strKey, $iIterator, $strPattern, $iCount);
    }

    public function scan(&$iIterator, $strPattern = null, $iCount = null)
    {
        $this->ready ?: $this->ready = $this->initializer->__invoke($this->redis);

        return $this->redis->scan($iIterator, $strPattern, $iCount);
    }

    public function sscan($strKey, &$iIterator, $strPattern = null, $iCount = null)
    {
        $this->ready ?: $this->ready = $this->initializer->__invoke($this->redis);

        return $this->redis->sscan($strKey, $iIterator, $strPattern, $iCount);
    }

    public function zscan($strKey, &$iIterator, $strPattern = null, $iCount = null)
    {
        $this->ready ?: $this->ready = $this->initializer->__invoke($this->redis);

        return $this->redis->zscan($strKey, $iIterator, $strPattern, $iCount);
    }
}
