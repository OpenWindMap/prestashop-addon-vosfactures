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

namespace Symfony\Polyfill\Apcu;

/**
 * Apcu for Zend Server Data Cache.
 *
 * @author Kate Gray <opensource@codebykate.com>
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @internal
 */
final class Apcu
{
    public static function apcu_add($key, $var = null, $ttl = 0)
    {
        if (!\is_array($key)) {
            return apc_add($key, $var, $ttl);
        }

        $errors = array();
        foreach ($key as $k => $v) {
            if (!apc_add($k, $v, $ttl)) {
                $errors[$k] = -1;
            }
        }

        return $errors;
    }

    public static function apcu_store($key, $var = null, $ttl = 0)
    {
        if (!\is_array($key)) {
            return apc_store($key, $var, $ttl);
        }

        $errors = array();
        foreach ($key as $k => $v) {
            if (!apc_store($k, $v, $ttl)) {
                $errors[$k] = -1;
            }
        }

        return $errors;
    }

    public static function apcu_exists($keys)
    {
        if (!\is_array($keys)) {
            return apc_exists($keys);
        }

        $existing = array();
        foreach ($keys as $k) {
            if (apc_exists($k)) {
                $existing[$k] = true;
            }
        }

        return $existing;
    }

    public static function apcu_fetch($key, &$success = null)
    {
        if (!\is_array($key)) {
            return apc_fetch($key, $success);
        }

        $succeeded = true;
        $values = array();
        foreach ($key as $k) {
            $v = apc_fetch($k, $success);
            if ($success) {
                $values[$k] = $v;
            } else {
                $succeeded = false;
            }
        }
        $success = $succeeded;

        return $values;
    }

    public static function apcu_delete($key)
    {
        if (!\is_array($key)) {
            return apc_delete($key);
        }

        $success = true;
        foreach ($key as $k) {
            $success = apc_delete($k) && $success;
        }

        return $success;
    }
}
