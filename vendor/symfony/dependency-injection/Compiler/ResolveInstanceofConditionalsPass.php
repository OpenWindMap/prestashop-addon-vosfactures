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

namespace Symfony\Component\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * Applies instanceof conditionals to definitions.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class ResolveInstanceofConditionalsPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        foreach ($container->getAutoconfiguredInstanceof() as $interface => $definition) {
            if ($definition->getArguments()) {
                throw new InvalidArgumentException(sprintf('Autoconfigured instanceof for type "%s" defines arguments but these are not supported and should be removed.', $interface));
            }
            if ($definition->getMethodCalls()) {
                throw new InvalidArgumentException(sprintf('Autoconfigured instanceof for type "%s" defines method calls but these are not supported and should be removed.', $interface));
            }
        }

        foreach ($container->getDefinitions() as $id => $definition) {
            if ($definition instanceof ChildDefinition) {
                // don't apply "instanceof" to children: it will be applied to their parent
                continue;
            }
            $container->setDefinition($id, $this->processDefinition($container, $id, $definition));
        }
    }

    private function processDefinition(ContainerBuilder $container, $id, Definition $definition)
    {
        $instanceofConditionals = $definition->getInstanceofConditionals();
        $autoconfiguredInstanceof = $definition->isAutoconfigured() ? $container->getAutoconfiguredInstanceof() : [];
        if (!$instanceofConditionals && !$autoconfiguredInstanceof) {
            return $definition;
        }

        if (!$class = $container->getParameterBag()->resolveValue($definition->getClass())) {
            return $definition;
        }

        $conditionals = $this->mergeConditionals($autoconfiguredInstanceof, $instanceofConditionals, $container);

        $definition->setInstanceofConditionals([]);
        $parent = $shared = null;
        $instanceofTags = [];
        $reflectionClass = null;

        foreach ($conditionals as $interface => $instanceofDefs) {
            if ($interface !== $class && !(null === $reflectionClass ? $reflectionClass = ($container->getReflectionClass($class, false) ?: false) : $reflectionClass)) {
                continue;
            }

            if ($interface !== $class && !is_subclass_of($class, $interface)) {
                continue;
            }

            foreach ($instanceofDefs as $key => $instanceofDef) {
                /** @var ChildDefinition $instanceofDef */
                $instanceofDef = clone $instanceofDef;
                $instanceofDef->setAbstract(true)->setParent($parent ?: 'abstract.instanceof.'.$id);
                $parent = 'instanceof.'.$interface.'.'.$key.'.'.$id;
                $container->setDefinition($parent, $instanceofDef);
                $instanceofTags[] = $instanceofDef->getTags();
                $instanceofDef->setTags([]);

                if (isset($instanceofDef->getChanges()['shared'])) {
                    $shared = $instanceofDef->isShared();
                }
            }
        }

        if ($parent) {
            $bindings = $definition->getBindings();
            $abstract = $container->setDefinition('abstract.instanceof.'.$id, $definition);

            // cast Definition to ChildDefinition
            $definition->setBindings([]);
            $definition = serialize($definition);
            $definition = substr_replace($definition, '53', 2, 2);
            $definition = substr_replace($definition, 'Child', 44, 0);
            $definition = unserialize($definition);
            $definition->setParent($parent);

            if (null !== $shared && !isset($definition->getChanges()['shared'])) {
                $definition->setShared($shared);
            }

            $i = \count($instanceofTags);
            while (0 <= --$i) {
                foreach ($instanceofTags[$i] as $k => $v) {
                    foreach ($v as $v) {
                        if ($definition->hasTag($k) && \in_array($v, $definition->getTag($k))) {
                            continue;
                        }
                        $definition->addTag($k, $v);
                    }
                }
            }

            $definition->setBindings($bindings);

            // reset fields with "merge" behavior
            $abstract
                ->setBindings([])
                ->setArguments([])
                ->setMethodCalls([])
                ->setDecoratedService(null)
                ->setTags([])
                ->setAbstract(true);
        }

        return $definition;
    }

    private function mergeConditionals(array $autoconfiguredInstanceof, array $instanceofConditionals, ContainerBuilder $container)
    {
        // make each value an array of ChildDefinition
        $conditionals = array_map(function ($childDef) { return [$childDef]; }, $autoconfiguredInstanceof);

        foreach ($instanceofConditionals as $interface => $instanceofDef) {
            // make sure the interface/class exists (but don't validate automaticInstanceofConditionals)
            if (!$container->getReflectionClass($interface)) {
                throw new RuntimeException(sprintf('"%s" is set as an "instanceof" conditional, but it does not exist.', $interface));
            }

            if (!isset($autoconfiguredInstanceof[$interface])) {
                $conditionals[$interface] = [];
            }

            $conditionals[$interface][] = $instanceofDef;
        }

        return $conditionals;
    }
}
