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

class Alias
{
    private $id;
    private $public;
    private $private;

    /**
     * @param string $id     Alias identifier
     * @param bool   $public If this alias is public
     */
    public function __construct($id, $public = true)
    {
        $this->id = (string) $id;
        $this->public = $public;
        $this->private = 2 > \func_num_args();
    }

    /**
     * Checks if this DI Alias should be public or not.
     *
     * @return bool
     */
    public function isPublic()
    {
        return $this->public;
    }

    /**
     * Sets if this Alias is public.
     *
     * @param bool $boolean If this Alias should be public
     *
     * @return $this
     */
    public function setPublic($boolean)
    {
        $this->public = (bool) $boolean;
        $this->private = false;

        return $this;
    }

    /**
     * Sets if this Alias is private.
     *
     * When set, the "private" state has a higher precedence than "public".
     * In version 3.4, a "private" alias always remains publicly accessible,
     * but triggers a deprecation notice when accessed from the container,
     * so that the alias can be made really private in 4.0.
     *
     * @param bool $boolean
     *
     * @return $this
     */
    public function setPrivate($boolean)
    {
        $this->private = (bool) $boolean;

        return $this;
    }

    /**
     * Whether this alias is private.
     *
     * @return bool
     */
    public function isPrivate()
    {
        return $this->private;
    }

    /**
     * Returns the Id of this alias.
     *
     * @return string The alias id
     */
    public function __toString()
    {
        return $this->id;
    }
}
