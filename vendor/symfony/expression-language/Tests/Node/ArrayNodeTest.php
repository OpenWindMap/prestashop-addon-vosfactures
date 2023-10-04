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

class ArrayNodeTest extends AbstractNodeTest
{
    public function testSerialization()
    {
        $node = $this->createArrayNode();
        $node->addElement(new ConstantNode('foo'));

        $serializedNode = serialize($node);
        $unserializedNode = unserialize($serializedNode);

        $this->assertEquals($node, $unserializedNode);
        $this->assertNotEquals($this->createArrayNode(), $unserializedNode);
    }

    public function getEvaluateData()
    {
        return [
            [['b' => 'a', 'b'], $this->getArrayNode()],
        ];
    }

    public function getCompileData()
    {
        return [
            ['["b" => "a", 0 => "b"]', $this->getArrayNode()],
        ];
    }

    public function getDumpData()
    {
        yield ['{"b": "a", 0: "b"}', $this->getArrayNode()];

        $array = $this->createArrayNode();
        $array->addElement(new ConstantNode('c'), new ConstantNode('a"b'));
        $array->addElement(new ConstantNode('d'), new ConstantNode('a\b'));
        yield ['{"a\\"b": "c", "a\\\\b": "d"}', $array];

        $array = $this->createArrayNode();
        $array->addElement(new ConstantNode('c'));
        $array->addElement(new ConstantNode('d'));
        yield ['["c", "d"]', $array];
    }

    protected function getArrayNode()
    {
        $array = $this->createArrayNode();
        $array->addElement(new ConstantNode('a'), new ConstantNode('b'));
        $array->addElement(new ConstantNode('b'));

        return $array;
    }

    protected function createArrayNode()
    {
        return new ArrayNode();
    }
}
