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

namespace Symfony\Component\Cache\Tests\Adapter;

use Predis\Connection\StreamConnection;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class PredisAdapterTest extends AbstractRedisAdapterTest
{
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$redis = new \Predis\Client(['host' => getenv('REDIS_HOST')]);
    }

    public function testCreateConnection()
    {
        $redisHost = getenv('REDIS_HOST');

        $redis = RedisAdapter::createConnection('redis://'.$redisHost.'/1', ['class' => \Predis\Client::class, 'timeout' => 3]);
        $this->assertInstanceOf(\Predis\Client::class, $redis);

        $connection = $redis->getConnection();
        $this->assertInstanceOf(StreamConnection::class, $connection);

        $params = [
            'scheme' => 'tcp',
            'host' => $redisHost,
            'path' => '',
            'dbindex' => '1',
            'port' => 6379,
            'class' => 'Predis\Client',
            'timeout' => 3,
            'persistent' => 0,
            'persistent_id' => null,
            'read_timeout' => 0,
            'retry_interval' => 0,
            'lazy' => false,
            'database' => '1',
            'password' => null,
        ];
        $this->assertSame($params, $connection->getParameters()->toArray());
    }
}
