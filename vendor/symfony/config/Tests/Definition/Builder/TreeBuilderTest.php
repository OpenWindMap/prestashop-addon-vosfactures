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

namespace Symfony\Component\Config\Tests\Definition\Builder;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Tests\Fixtures\Builder\NodeBuilder as CustomNodeBuilder;

class TreeBuilderTest extends TestCase
{
    public function testUsingACustomNodeBuilder()
    {
        $builder = new TreeBuilder();
        $root = $builder->root('custom', 'array', new CustomNodeBuilder());

        $nodeBuilder = $root->children();

        $this->assertInstanceOf('Symfony\Component\Config\Tests\Fixtures\Builder\NodeBuilder', $nodeBuilder);

        $nodeBuilder = $nodeBuilder->arrayNode('deeper')->children();

        $this->assertInstanceOf('Symfony\Component\Config\Tests\Fixtures\Builder\NodeBuilder', $nodeBuilder);
    }

    public function testOverrideABuiltInNodeType()
    {
        $builder = new TreeBuilder();
        $root = $builder->root('override', 'array', new CustomNodeBuilder());

        $definition = $root->children()->variableNode('variable');

        $this->assertInstanceOf('Symfony\Component\Config\Tests\Fixtures\Builder\VariableNodeDefinition', $definition);
    }

    public function testAddANodeType()
    {
        $builder = new TreeBuilder();
        $root = $builder->root('override', 'array', new CustomNodeBuilder());

        $definition = $root->children()->barNode('variable');

        $this->assertInstanceOf('Symfony\Component\Config\Tests\Fixtures\Builder\BarNodeDefinition', $definition);
    }

    public function testCreateABuiltInNodeTypeWithACustomNodeBuilder()
    {
        $builder = new TreeBuilder();
        $root = $builder->root('builtin', 'array', new CustomNodeBuilder());

        $definition = $root->children()->booleanNode('boolean');

        $this->assertInstanceOf('Symfony\Component\Config\Definition\Builder\BooleanNodeDefinition', $definition);
    }

    public function testPrototypedArrayNodeUseTheCustomNodeBuilder()
    {
        $builder = new TreeBuilder();
        $root = $builder->root('override', 'array', new CustomNodeBuilder());

        $root->prototype('bar')->end();

        $this->assertInstanceOf('Symfony\Component\Config\Tests\Fixtures\BarNode', $root->getNode(true)->getPrototype());
    }

    public function testAnExtendedNodeBuilderGetsPropagatedToTheChildren()
    {
        $builder = new TreeBuilder();

        $builder->root('propagation')
            ->children()
                ->setNodeClass('extended', 'Symfony\Component\Config\Definition\Builder\BooleanNodeDefinition')
                ->node('foo', 'extended')->end()
                ->arrayNode('child')
                    ->children()
                        ->node('foo', 'extended')
                    ->end()
                ->end()
            ->end()
        ->end();

        $node = $builder->buildTree();
        $children = $node->getChildren();

        $this->assertInstanceOf('Symfony\Component\Config\Definition\BooleanNode', $children['foo']);

        $childChildren = $children['child']->getChildren();

        $this->assertInstanceOf('Symfony\Component\Config\Definition\BooleanNode', $childChildren['foo']);
    }

    public function testDefinitionInfoGetsTransferredToNode()
    {
        $builder = new TreeBuilder();

        $builder->root('test')->info('root info')
            ->children()
                ->node('child', 'variable')->info('child info')->defaultValue('default')
            ->end()
        ->end();

        $tree = $builder->buildTree();
        $children = $tree->getChildren();

        $this->assertEquals('root info', $tree->getInfo());
        $this->assertEquals('child info', $children['child']->getInfo());
    }

    public function testDefinitionExampleGetsTransferredToNode()
    {
        $builder = new TreeBuilder();

        $builder->root('test')
            ->example(['key' => 'value'])
            ->children()
                ->node('child', 'variable')->info('child info')->defaultValue('default')->example('example')
            ->end()
        ->end();

        $tree = $builder->buildTree();
        $children = $tree->getChildren();

        $this->assertIsArray($tree->getExample());
        $this->assertEquals('example', $children['child']->getExample());
    }
}
