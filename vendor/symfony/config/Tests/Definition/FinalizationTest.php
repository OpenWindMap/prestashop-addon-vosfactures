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
use Symfony\Component\Config\Definition\NodeInterface;
use Symfony\Component\Config\Definition\Processor;

class FinalizationTest extends TestCase
{
    public function testUnsetKeyWithDeepHierarchy()
    {
        $tb = new TreeBuilder();
        $tree = $tb
            ->root('config', 'array')
                ->children()
                    ->node('level1', 'array')
                        ->canBeUnset()
                        ->children()
                            ->node('level2', 'array')
                                ->canBeUnset()
                                ->children()
                                    ->node('somevalue', 'scalar')->end()
                                    ->node('anothervalue', 'scalar')->end()
                                ->end()
                            ->end()
                            ->node('level1_scalar', 'scalar')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
            ->buildTree()
        ;

        $a = [
            'level1' => [
                'level2' => [
                    'somevalue' => 'foo',
                    'anothervalue' => 'bar',
                ],
                'level1_scalar' => 'foo',
            ],
        ];

        $b = [
            'level1' => [
                'level2' => false,
            ],
        ];

        $this->assertEquals([
            'level1' => [
                'level1_scalar' => 'foo',
            ],
        ], $this->process($tree, [$a, $b]));
    }

    protected function process(NodeInterface $tree, array $configs)
    {
        $processor = new Processor();

        return $processor->process($tree, $configs);
    }
}
