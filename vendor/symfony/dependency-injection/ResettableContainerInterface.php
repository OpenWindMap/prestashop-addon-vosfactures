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

namespace Symfony\Component\DependencyInjection;

/**
 * ResettableContainerInterface defines additional resetting functionality
 * for containers, allowing to release shared services when the container is
 * not needed anymore.
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
interface ResettableContainerInterface extends ContainerInterface
{
    /**
     * Resets shared services from the container.
     *
     * The container is not intended to be used again after being reset in a normal workflow. This method is
     * meant as a way to release references for ref-counting.
     * A subsequent call to ContainerInterface::get will recreate a new instance of the shared service.
     */
    public function reset();
}
