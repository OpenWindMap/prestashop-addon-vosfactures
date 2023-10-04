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

namespace Symfony\Component\Config\DependencyInjection;

@trigger_error(sprintf('The %s class is deprecated since Symfony 3.4 and will be removed in 4.0. Use tagged iterator arguments instead.', ConfigCachePass::class), \E_USER_DEPRECATED);

use Symfony\Component\DependencyInjection\Argument\IteratorArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Adds services tagged config_cache.resource_checker to the config_cache_factory service, ordering them by priority.
 *
 * @author Matthias Pigulla <mp@webfactory.de>
 * @author Benjamin Klotz <bk@webfactory.de>
 *
 * @deprecated since version 3.4, to be removed in 4.0. Use tagged iterator arguments instead.
 */
class ConfigCachePass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    private $factoryServiceId;
    private $resourceCheckerTag;

    public function __construct($factoryServiceId = 'config_cache_factory', $resourceCheckerTag = 'config_cache.resource_checker')
    {
        $this->factoryServiceId = $factoryServiceId;
        $this->resourceCheckerTag = $resourceCheckerTag;
    }

    public function process(ContainerBuilder $container)
    {
        $resourceCheckers = $this->findAndSortTaggedServices($this->resourceCheckerTag, $container);

        if (empty($resourceCheckers)) {
            return;
        }

        $container->getDefinition($this->factoryServiceId)->replaceArgument(0, new IteratorArgument($resourceCheckers));
    }
}
