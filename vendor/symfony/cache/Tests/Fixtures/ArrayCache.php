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

namespace Symfony\Component\Cache\Tests\Fixtures;

use Doctrine\Common\Cache\CacheProvider;

class ArrayCache extends CacheProvider
{
    private $data = [];

    protected function doFetch($id)
    {
        return $this->doContains($id) ? $this->data[$id][0] : false;
    }

    protected function doContains($id)
    {
        if (!isset($this->data[$id])) {
            return false;
        }

        $expiry = $this->data[$id][1];

        return !$expiry || time() < $expiry || !$this->doDelete($id);
    }

    protected function doSave($id, $data, $lifeTime = 0)
    {
        $this->data[$id] = [$data, $lifeTime ? time() + $lifeTime : false];

        return true;
    }

    protected function doDelete($id)
    {
        unset($this->data[$id]);

        return true;
    }

    protected function doFlush()
    {
        $this->data = [];

        return true;
    }

    protected function doGetStats()
    {
        return null;
    }
}
