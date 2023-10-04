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

class ConstantNodeTest extends AbstractNodeTest
{
    public function getEvaluateData()
    {
        return [
            [false, new ConstantNode(false)],
            [true, new ConstantNode(true)],
            [null, new ConstantNode(null)],
            [3, new ConstantNode(3)],
            [3.3, new ConstantNode(3.3)],
            ['foo', new ConstantNode('foo')],
            [[1, 'b' => 'a'], new ConstantNode([1, 'b' => 'a'])],
        ];
    }

    public function getCompileData()
    {
        return [
            ['false', new ConstantNode(false)],
            ['true', new ConstantNode(true)],
            ['null', new ConstantNode(null)],
            ['3', new ConstantNode(3)],
            ['3.3', new ConstantNode(3.3)],
            ['"foo"', new ConstantNode('foo')],
            ['[0 => 1, "b" => "a"]', new ConstantNode([1, 'b' => 'a'])],
        ];
    }

    public function getDumpData()
    {
        return [
            ['false', new ConstantNode(false)],
            ['true', new ConstantNode(true)],
            ['null', new ConstantNode(null)],
            ['3', new ConstantNode(3)],
            ['3.3', new ConstantNode(3.3)],
            ['"foo"', new ConstantNode('foo')],
            ['foo', new ConstantNode('foo', true)],
            ['{0: 1, "b": "a", 1: true}', new ConstantNode([1, 'b' => 'a', true])],
            ['{"a\\"b": "c", "a\\\\b": "d"}', new ConstantNode(['a"b' => 'c', 'a\\b' => 'd'])],
            ['["c", "d"]', new ConstantNode(['c', 'd'])],
            ['{"a": ["b"]}', new ConstantNode(['a' => ['b']])],
        ];
    }
}
