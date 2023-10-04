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

namespace Symfony\Component\Cache\Tests\Traits;

trait PdoPruneableTrait
{
    protected function isPruned($cache, $name)
    {
        $o = new \ReflectionObject($cache);

        if (!$o->hasMethod('getConnection')) {
            self::fail('Cache does not have "getConnection()" method.');
        }

        $getPdoConn = $o->getMethod('getConnection');
        $getPdoConn->setAccessible(true);

        /** @var \Doctrine\DBAL\Statement|\PDOStatement $select */
        $select = $getPdoConn->invoke($cache)->prepare('SELECT 1 FROM cache_items WHERE item_id LIKE :id');
        $select->bindValue(':id', sprintf('%%%s', $name));
        $result = $select->execute();

        return 1 !== (int) (\is_object($result) ? $result->fetchOne() : $select->fetch(\PDO::FETCH_COLUMN));
    }
}
