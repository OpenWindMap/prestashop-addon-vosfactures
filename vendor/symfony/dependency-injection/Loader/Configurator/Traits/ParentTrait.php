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

namespace Symfony\Component\DependencyInjection\Loader\Configurator\Traits;

use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * @method $this parent(string $parent)
 */
trait ParentTrait
{
    /**
     * Sets the Definition to inherit from.
     *
     * @param string $parent
     *
     * @return $this
     *
     * @throws InvalidArgumentException when parent cannot be set
     */
    final protected function setParent($parent)
    {
        if (!$this->allowParent) {
            throw new InvalidArgumentException(sprintf('A parent cannot be defined when either "_instanceof" or "_defaults" are also defined for service prototype "%s".', $this->id));
        }

        if ($this->definition instanceof ChildDefinition) {
            $this->definition->setParent($parent);
        } elseif ($this->definition->isAutoconfigured()) {
            throw new InvalidArgumentException(sprintf('The service "%s" cannot have a "parent" and also have "autoconfigure". Try disabling autoconfiguration for the service.', $this->id));
        } elseif ($this->definition->getBindings()) {
            throw new InvalidArgumentException(sprintf('The service "%s" cannot have a "parent" and also "bind" arguments.', $this->id));
        } else {
            // cast Definition to ChildDefinition
            $definition = serialize($this->definition);
            $definition = substr_replace($definition, '53', 2, 2);
            $definition = substr_replace($definition, 'Child', 44, 0);
            $definition = unserialize($definition);

            $this->definition = $definition->setParent($parent);
        }

        return $this;
    }
}
