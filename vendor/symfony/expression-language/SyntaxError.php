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

class SyntaxError extends \LogicException
{
    public function __construct($message, $cursor = 0, $expression = '', $subject = null, array $proposals = null)
    {
        $message = sprintf('%s around position %d', rtrim($message, '.'), $cursor);
        if ($expression) {
            $message = sprintf('%s for expression `%s`', $message, $expression);
        }
        $message .= '.';

        if (null !== $subject && null !== $proposals) {
            $minScore = \INF;
            foreach ($proposals as $proposal) {
                $distance = levenshtein($subject, $proposal);
                if ($distance < $minScore) {
                    $guess = $proposal;
                    $minScore = $distance;
                }
            }

            if (isset($guess) && $minScore < 3) {
                $message .= sprintf(' Did you mean "%s"?', $guess);
            }
        }

        parent::__construct($message);
    }
}
