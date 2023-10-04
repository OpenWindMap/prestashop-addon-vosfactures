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

use Symfony\Component\ExpressionLanguage\Node\ArrayNode;
use Symfony\Component\ExpressionLanguage\Node\ConstantNode;
use Symfony\Component\ExpressionLanguage\Node\GetAttrNode;
use Symfony\Component\ExpressionLanguage\Node\NameNode;

class GetAttrNodeTest extends AbstractNodeTest
{
    public function getEvaluateData()
    {
        return [
            ['b', new GetAttrNode(new NameNode('foo'), new ConstantNode(0), $this->getArrayNode(), GetAttrNode::ARRAY_CALL), ['foo' => ['b' => 'a', 'b']]],
            ['a', new GetAttrNode(new NameNode('foo'), new ConstantNode('b'), $this->getArrayNode(), GetAttrNode::ARRAY_CALL), ['foo' => ['b' => 'a', 'b']]],

            ['bar', new GetAttrNode(new NameNode('foo'), new ConstantNode('foo'), $this->getArrayNode(), GetAttrNode::PROPERTY_CALL), ['foo' => new Obj()]],

            ['baz', new GetAttrNode(new NameNode('foo'), new ConstantNode('foo'), $this->getArrayNode(), GetAttrNode::METHOD_CALL), ['foo' => new Obj()]],
            ['a', new GetAttrNode(new NameNode('foo'), new NameNode('index'), $this->getArrayNode(), GetAttrNode::ARRAY_CALL), ['foo' => ['b' => 'a', 'b'], 'index' => 'b']],
        ];
    }

    public function getCompileData()
    {
        return [
            ['$foo[0]', new GetAttrNode(new NameNode('foo'), new ConstantNode(0), $this->getArrayNode(), GetAttrNode::ARRAY_CALL)],
            ['$foo["b"]', new GetAttrNode(new NameNode('foo'), new ConstantNode('b'), $this->getArrayNode(), GetAttrNode::ARRAY_CALL)],

            ['$foo->foo', new GetAttrNode(new NameNode('foo'), new ConstantNode('foo'), $this->getArrayNode(), GetAttrNode::PROPERTY_CALL), ['foo' => new Obj()]],

            ['$foo->foo(["b" => "a", 0 => "b"])', new GetAttrNode(new NameNode('foo'), new ConstantNode('foo'), $this->getArrayNode(), GetAttrNode::METHOD_CALL), ['foo' => new Obj()]],
            ['$foo[$index]', new GetAttrNode(new NameNode('foo'), new NameNode('index'), $this->getArrayNode(), GetAttrNode::ARRAY_CALL)],
        ];
    }

    public function getDumpData()
    {
        return [
            ['foo[0]', new GetAttrNode(new NameNode('foo'), new ConstantNode(0), $this->getArrayNode(), GetAttrNode::ARRAY_CALL)],
            ['foo["b"]', new GetAttrNode(new NameNode('foo'), new ConstantNode('b'), $this->getArrayNode(), GetAttrNode::ARRAY_CALL)],

            ['foo.foo', new GetAttrNode(new NameNode('foo'), new NameNode('foo'), $this->getArrayNode(), GetAttrNode::PROPERTY_CALL), ['foo' => new Obj()]],

            ['foo.foo({"b": "a", 0: "b"})', new GetAttrNode(new NameNode('foo'), new NameNode('foo'), $this->getArrayNode(), GetAttrNode::METHOD_CALL), ['foo' => new Obj()]],
            ['foo[index]', new GetAttrNode(new NameNode('foo'), new NameNode('index'), $this->getArrayNode(), GetAttrNode::ARRAY_CALL)],
        ];
    }

    protected function getArrayNode()
    {
        $array = new ArrayNode();
        $array->addElement(new ConstantNode('a'), new ConstantNode('b'));
        $array->addElement(new ConstantNode('b'));

        return $array;
    }
}

class Obj
{
    public $foo = 'bar';

    public function foo()
    {
        return 'baz';
    }
}
