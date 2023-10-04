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

namespace Symfony\Component\Config\Tests\Definition;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class MergeTest extends TestCase
{
    public function testForbiddenOverwrite()
    {
        $this->expectException('Symfony\Component\Config\Definition\Exception\ForbiddenOverwriteException');
        $tb = new TreeBuilder();
        $tree = $tb
            ->root('root', 'array')
                ->children()
                    ->node('foo', 'scalar')
                        ->cannotBeOverwritten()
                    ->end()
                ->end()
            ->end()
            ->buildTree()
        ;

        $a = [
            'foo' => 'bar',
        ];

        $b = [
            'foo' => 'moo',
        ];

        $tree->merge($a, $b);
    }

    public function testUnsetKey()
    {
        $tb = new TreeBuilder();
        $tree = $tb
            ->root('root', 'array')
                ->children()
                    ->node('foo', 'scalar')->end()
                    ->node('bar', 'scalar')->end()
                    ->node('unsettable', 'array')
                        ->canBeUnset()
                        ->children()
                            ->node('foo', 'scalar')->end()
                            ->node('bar', 'scalar')->end()
                        ->end()
                    ->end()
                    ->node('unsetted', 'array')
                        ->canBeUnset()
                        ->prototype('scalar')->end()
                    ->end()
                ->end()
            ->end()
            ->buildTree()
        ;

        $a = [
            'foo' => 'bar',
            'unsettable' => [
                'foo' => 'a',
                'bar' => 'b',
            ],
            'unsetted' => false,
        ];

        $b = [
            'foo' => 'moo',
            'bar' => 'b',
            'unsettable' => false,
            'unsetted' => ['a', 'b'],
        ];

        $this->assertEquals([
            'foo' => 'moo',
            'bar' => 'b',
            'unsettable' => false,
            'unsetted' => ['a', 'b'],
        ], $tree->merge($a, $b));
    }

    public function testDoesNotAllowNewKeysInSubsequentConfigs()
    {
        $this->expectException('Symfony\Component\Config\Definition\Exception\InvalidConfigurationException');
        $tb = new TreeBuilder();
        $tree = $tb
            ->root('config', 'array')
                ->children()
                    ->node('test', 'array')
                        ->disallowNewKeysInSubsequentConfigs()
                        ->useAttributeAsKey('key')
                        ->prototype('array')
                            ->children()
                                ->node('value', 'scalar')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->buildTree();

        $a = [
            'test' => [
                'a' => ['value' => 'foo'],
            ],
        ];

        $b = [
            'test' => [
                'b' => ['value' => 'foo'],
            ],
        ];

        $tree->merge($a, $b);
    }

    public function testPerformsNoDeepMerging()
    {
        $tb = new TreeBuilder();

        $tree = $tb
            ->root('config', 'array')
                ->children()
                    ->node('no_deep_merging', 'array')
                        ->performNoDeepMerging()
                        ->children()
                            ->node('foo', 'scalar')->end()
                            ->node('bar', 'scalar')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->buildTree()
        ;

        $a = [
            'no_deep_merging' => [
                'foo' => 'a',
                'bar' => 'b',
            ],
        ];

        $b = [
            'no_deep_merging' => [
                'c' => 'd',
            ],
        ];

        $this->assertEquals([
            'no_deep_merging' => [
                'c' => 'd',
            ],
        ], $tree->merge($a, $b));
    }

    public function testPrototypeWithoutAKeyAttribute()
    {
        $tb = new TreeBuilder();

        $tree = $tb
            ->root('config', 'array')
                ->children()
                    ->arrayNode('append_elements')
                        ->prototype('scalar')->end()
                    ->end()
                ->end()
            ->end()
            ->buildTree()
        ;

        $a = [
            'append_elements' => ['a', 'b'],
        ];

        $b = [
            'append_elements' => ['c', 'd'],
        ];

        $this->assertEquals(['append_elements' => ['a', 'b', 'c', 'd']], $tree->merge($a, $b));
    }
}
