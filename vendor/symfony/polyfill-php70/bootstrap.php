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

use Symfony\Polyfill\Php70 as p;

if (PHP_VERSION_ID >= 70000) {
    return;
}

if (!defined('PHP_INT_MIN')) {
    define('PHP_INT_MIN', ~PHP_INT_MAX);
}

if (!function_exists('intdiv')) {
    function intdiv($num1, $num2) { return p\Php70::intdiv($num1, $num2); }
}
if (!function_exists('preg_replace_callback_array')) {
    function preg_replace_callback_array(array $pattern, $subject, $limit = -1, &$count = 0, $flags = null) { return p\Php70::preg_replace_callback_array($pattern, $subject, $limit, $count); }
}
if (!function_exists('error_clear_last')) {
    function error_clear_last() { return p\Php70::error_clear_last(); }
}
