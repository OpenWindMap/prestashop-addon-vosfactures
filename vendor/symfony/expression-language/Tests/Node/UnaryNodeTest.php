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

namespace Symfony\Component\ExpressionLanguage\Tests\Node;

use Symfony\Component\ExpressionLanguage\Node\ConstantNode;
use Symfony\Component\ExpressionLanguage\Node\UnaryNode;

class UnaryNodeTest extends AbstractNodeTest
{
    public function getEvaluateData()
    {
        return [
            [-1, new UnaryNode('-', new ConstantNode(1))],
            [3, new UnaryNode('+', new ConstantNode(3))],
            [false, new UnaryNode('!', new ConstantNode(true))],
            [false, new UnaryNode('not', new ConstantNode(true))],
        ];
    }

    public function getCompileData()
    {
        return [
            ['(-1)', new UnaryNode('-', new ConstantNode(1))],
            ['(+3)', new UnaryNode('+', new ConstantNode(3))],
            ['(!true)', new UnaryNode('!', new ConstantNode(true))],
            ['(!true)', new UnaryNode('not', new ConstantNode(true))],
        ];
    }

    public function getDumpData()
    {
        return [
            ['(- 1)', new UnaryNode('-', new ConstantNode(1))],
            ['(+ 3)', new UnaryNode('+', new ConstantNode(3))],
            ['(! true)', new UnaryNode('!', new ConstantNode(true))],
            ['(not true)', new UnaryNode('not', new ConstantNode(true))],
        ];
    }
}
