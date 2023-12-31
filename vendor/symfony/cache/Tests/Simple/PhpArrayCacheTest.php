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

namespace Symfony\Component\Cache\Tests\Simple;

use Symfony\Component\Cache\Simple\NullCache;
use Symfony\Component\Cache\Simple\PhpArrayCache;
use Symfony\Component\Cache\Tests\Adapter\FilesystemAdapterTest;

/**
 * @group time-sensitive
 */
class PhpArrayCacheTest extends CacheTestCase
{
    protected $skippedTests = [
        'testBasicUsageWithLongKey' => 'PhpArrayCache does no writes',

        'testDelete' => 'PhpArrayCache does no writes',
        'testDeleteMultiple' => 'PhpArrayCache does no writes',
        'testDeleteMultipleGenerator' => 'PhpArrayCache does no writes',

        'testSetTtl' => 'PhpArrayCache does no expiration',
        'testSetMultipleTtl' => 'PhpArrayCache does no expiration',
        'testSetExpiredTtl' => 'PhpArrayCache does no expiration',
        'testSetMultipleExpiredTtl' => 'PhpArrayCache does no expiration',

        'testGetInvalidKeys' => 'PhpArrayCache does no validation',
        'testGetMultipleInvalidKeys' => 'PhpArrayCache does no validation',
        'testSetInvalidKeys' => 'PhpArrayCache does no validation',
        'testDeleteInvalidKeys' => 'PhpArrayCache does no validation',
        'testDeleteMultipleInvalidKeys' => 'PhpArrayCache does no validation',
        'testSetInvalidTtl' => 'PhpArrayCache does no validation',
        'testSetMultipleInvalidKeys' => 'PhpArrayCache does no validation',
        'testSetMultipleInvalidTtl' => 'PhpArrayCache does no validation',
        'testHasInvalidKeys' => 'PhpArrayCache does no validation',
        'testSetValidData' => 'PhpArrayCache does no validation',

        'testDefaultLifeTime' => 'PhpArrayCache does not allow configuring a default lifetime.',
        'testPrune' => 'PhpArrayCache just proxies',
    ];

    protected static $file;

    public static function setUpBeforeClass()
    {
        self::$file = sys_get_temp_dir().'/symfony-cache/php-array-adapter-test.php';
    }

    protected function tearDown()
    {
        $this->createSimpleCache()->clear();

        if (file_exists(sys_get_temp_dir().'/symfony-cache')) {
            FilesystemAdapterTest::rmdir(sys_get_temp_dir().'/symfony-cache');
        }
    }

    public function createSimpleCache()
    {
        return new PhpArrayCacheWrapper(self::$file, new NullCache());
    }

    public function testStore()
    {
        $arrayWithRefs = [];
        $arrayWithRefs[0] = 123;
        $arrayWithRefs[1] = &$arrayWithRefs[0];

        $object = (object) [
            'foo' => 'bar',
            'foo2' => 'bar2',
        ];

        $expected = [
            'null' => null,
            'serializedString' => serialize($object),
            'arrayWithRefs' => $arrayWithRefs,
            'object' => $object,
            'arrayWithObject' => ['bar' => $object],
        ];

        $cache = new PhpArrayCache(self::$file, new NullCache());
        $cache->warmUp($expected);

        foreach ($expected as $key => $value) {
            $this->assertSame(serialize($value), serialize($cache->get($key)), 'Warm up should create a PHP file that OPCache can load in memory');
        }
    }

    public function testStoredFile()
    {
        $expected = [
            'integer' => 42,
            'float' => 42.42,
            'boolean' => true,
            'array_simple' => ['foo', 'bar'],
            'array_associative' => ['foo' => 'bar', 'foo2' => 'bar2'],
        ];

        $cache = new PhpArrayCache(self::$file, new NullCache());
        $cache->warmUp($expected);

        $values = eval(substr(file_get_contents(self::$file), 6));

        $this->assertSame($expected, $values, 'Warm up should create a PHP file that OPCache can load in memory');
    }
}

class PhpArrayCacheWrapper extends PhpArrayCache
{
    public function set($key, $value, $ttl = null)
    {
        \call_user_func(\Closure::bind(function () use ($key, $value) {
            $this->values[$key] = $value;
            $this->warmUp($this->values);
            $this->values = eval(substr(file_get_contents($this->file), 6));
        }, $this, PhpArrayCache::class));

        return true;
    }

    public function setMultiple($values, $ttl = null)
    {
        if (!\is_array($values) && !$values instanceof \Traversable) {
            return parent::setMultiple($values, $ttl);
        }
        \call_user_func(\Closure::bind(function () use ($values) {
            foreach ($values as $key => $value) {
                $this->values[$key] = $value;
            }
            $this->warmUp($this->values);
            $this->values = eval(substr(file_get_contents($this->file), 6));
        }, $this, PhpArrayCache::class));

        return true;
    }
}
