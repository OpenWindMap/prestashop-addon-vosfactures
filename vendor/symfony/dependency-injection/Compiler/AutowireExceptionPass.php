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

@trigger_error('The '.__NAMESPACE__.'\AutowireExceptionPass class is deprecated since Symfony 3.4 and will be removed in 4.0. Use the DefinitionErrorExceptionPass class instead.', \E_USER_DEPRECATED);

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Throws autowire exceptions from AutowirePass for definitions that still exist.
 *
 * @deprecated since version 3.4, will be removed in 4.0.
 *
 * @author Ryan Weaver <ryan@knpuniversity.com>
 */
class AutowireExceptionPass implements CompilerPassInterface
{
    private $autowirePass;
    private $inlineServicePass;

    public function __construct(AutowirePass $autowirePass, InlineServiceDefinitionsPass $inlineServicePass)
    {
        $this->autowirePass = $autowirePass;
        $this->inlineServicePass = $inlineServicePass;
    }

    public function process(ContainerBuilder $container)
    {
        // the pass should only be run once
        if (null === $this->autowirePass || null === $this->inlineServicePass) {
            return;
        }

        $inlinedIds = $this->inlineServicePass->getInlinedServiceIds();
        $exceptions = $this->autowirePass->getAutowiringExceptions();

        // free up references
        $this->autowirePass = null;
        $this->inlineServicePass = null;

        foreach ($exceptions as $exception) {
            if ($this->doesServiceExistInTheContainer($exception->getServiceId(), $container, $inlinedIds)) {
                throw $exception;
            }
        }
    }

    private function doesServiceExistInTheContainer($serviceId, ContainerBuilder $container, array $inlinedIds)
    {
        if ($container->hasDefinition($serviceId)) {
            return true;
        }

        // was the service inlined? Of so, does its parent service exist?
        if (isset($inlinedIds[$serviceId])) {
            foreach ($inlinedIds[$serviceId] as $parentId) {
                if ($this->doesServiceExistInTheContainer($parentId, $container, $inlinedIds)) {
                    return true;
                }
            }
        }

        return false;
    }
}
