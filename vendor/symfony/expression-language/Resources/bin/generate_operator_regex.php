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

$operators = ['not', '!', 'or', '||', '&&', 'and', '|', '^', '&', '==', '===', '!=', '!==', '<', '>', '>=', '<=', 'not in', 'in', '..', '+', '-', '~', '*', '/', '%', 'matches', '**'];
$operators = array_combine($operators, array_map('strlen', $operators));
arsort($operators);

$regex = [];
foreach ($operators as $operator => $length) {
    // Collisions of character operators:
    // - an operator that begins with a character must have a space or a parenthesis before or starting at the beginning of a string
    // - an operator that ends with a character must be followed by a whitespace or a parenthesis
    $regex[] =
        (ctype_alpha($operator[0]) ? '(?<=^|[\s(])' : '')
        .preg_quote($operator, '/')
        .(ctype_alpha($operator[$length - 1]) ? '(?=[\s(])' : '');
}

echo '/'.implode('|', $regex).'/A';
