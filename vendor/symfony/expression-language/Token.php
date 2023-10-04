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

namespace Symfony\Component\ExpressionLanguage;

/**
 * Represents a Token.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Token
{
    public $value;
    public $type;
    public $cursor;

    const EOF_TYPE = 'end of expression';
    const NAME_TYPE = 'name';
    const NUMBER_TYPE = 'number';
    const STRING_TYPE = 'string';
    const OPERATOR_TYPE = 'operator';
    const PUNCTUATION_TYPE = 'punctuation';

    /**
     * @param string                $type   The type of the token (self::*_TYPE)
     * @param string|int|float|null $value  The token value
     * @param int                   $cursor The cursor position in the source
     */
    public function __construct($type, $value, $cursor)
    {
        $this->type = $type;
        $this->value = $value;
        $this->cursor = $cursor;
    }

    /**
     * Returns a string representation of the token.
     *
     * @return string A string representation of the token
     */
    public function __toString()
    {
        return sprintf('%3d %-11s %s', $this->cursor, strtoupper($this->type), $this->value);
    }

    /**
     * Tests the current token for a type and/or a value.
     *
     * @param string      $type  The type to test
     * @param string|null $value The token value
     *
     * @return bool
     */
    public function test($type, $value = null)
    {
        return $this->type === $type && (null === $value || $this->value == $value);
    }
}
