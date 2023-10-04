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

namespace Symfony\Polyfill\Php70;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @internal
 */
final class Php70
{
    public static function intdiv($dividend, $divisor)
    {
        $dividend = self::intArg($dividend, __FUNCTION__, 1);
        $divisor = self::intArg($divisor, __FUNCTION__, 2);

        if (0 === $divisor) {
            throw new \DivisionByZeroError('Division by zero');
        }
        if (-1 === $divisor && ~PHP_INT_MAX === $dividend) {
            throw new \ArithmeticError('Division of PHP_INT_MIN by -1 is not an integer');
        }

        return ($dividend - ($dividend % $divisor)) / $divisor;
    }

    public static function preg_replace_callback_array(array $patterns, $subject, $limit = -1, &$count = 0)
    {
        $count = 0;
        $result = (string) $subject;
        if (0 === $limit = self::intArg($limit, __FUNCTION__, 3)) {
            return $result;
        }

        foreach ($patterns as $pattern => $callback) {
            $result = preg_replace_callback($pattern, $callback, $result, $limit, $c);
            $count += $c;
        }

        return $result;
    }

    public static function error_clear_last()
    {
        static $handler;
        if (!$handler) {
            $handler = function () { return false; };
        }
        set_error_handler($handler);
        @trigger_error('');
        restore_error_handler();
    }

    private static function intArg($value, $caller, $pos)
    {
        if (\is_int($value)) {
            return $value;
        }
        if (!\is_numeric($value) || PHP_INT_MAX <= ($value += 0) || ~PHP_INT_MAX >= $value) {
            throw new \TypeError(sprintf('%s() expects parameter %d to be integer, %s given', $caller, $pos, \gettype($value)));
        }

        return (int) $value;
    }
}
