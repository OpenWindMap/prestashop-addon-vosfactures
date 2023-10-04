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

namespace Symfony\Component\Config;

use Symfony\Component\Config\Resource\ResourceInterface;

/**
 * Interface for ResourceCheckers.
 *
 * When a ResourceCheckerConfigCache instance is checked for freshness, all its associated
 * metadata resources are passed to ResourceCheckers. The ResourceCheckers
 * can then inspect the resources and decide whether the cache can be considered
 * fresh or not.
 *
 * @author Matthias Pigulla <mp@webfactory.de>
 * @author Benjamin Klotz <bk@webfactory.de>
 */
interface ResourceCheckerInterface
{
    /**
     * Queries the ResourceChecker whether it can validate a given
     * resource or not.
     *
     * @param ResourceInterface $metadata The resource to be checked for freshness
     *
     * @return bool True if the ResourceChecker can handle this resource type, false if not
     */
    public function supports(ResourceInterface $metadata);

    /**
     * Validates the resource.
     *
     * @param ResourceInterface $resource  The resource to be validated
     * @param int               $timestamp The timestamp at which the cache associated with this resource was created
     *
     * @return bool True if the resource has not changed since the given timestamp, false otherwise
     */
    public function isFresh(ResourceInterface $resource, $timestamp);
}
