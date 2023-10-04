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

use Symfony\Polyfill\Ctype as p;

if (!function_exists('ctype_alnum')) {
    function ctype_alnum($input) { return p\Ctype::ctype_alnum($input); }
}
if (!function_exists('ctype_alpha')) {
    function ctype_alpha($input) { return p\Ctype::ctype_alpha($input); }
}
if (!function_exists('ctype_cntrl')) {
    function ctype_cntrl($input) { return p\Ctype::ctype_cntrl($input); }
}
if (!function_exists('ctype_digit')) {
    function ctype_digit($input) { return p\Ctype::ctype_digit($input); }
}
if (!function_exists('ctype_graph')) {
    function ctype_graph($input) { return p\Ctype::ctype_graph($input); }
}
if (!function_exists('ctype_lower')) {
    function ctype_lower($input) { return p\Ctype::ctype_lower($input); }
}
if (!function_exists('ctype_print')) {
    function ctype_print($input) { return p\Ctype::ctype_print($input); }
}
if (!function_exists('ctype_punct')) {
    function ctype_punct($input) { return p\Ctype::ctype_punct($input); }
}
if (!function_exists('ctype_space')) {
    function ctype_space($input) { return p\Ctype::ctype_space($input); }
}
if (!function_exists('ctype_upper')) {
    function ctype_upper($input) { return p\Ctype::ctype_upper($input); }
}
if (!function_exists('ctype_xdigit')) {
    function ctype_xdigit($input) { return p\Ctype::ctype_xdigit($input); }
}
