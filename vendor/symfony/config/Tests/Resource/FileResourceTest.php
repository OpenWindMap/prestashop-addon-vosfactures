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

namespace Symfony\Component\Config\Tests\Resource;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Resource\FileResource;

class FileResourceTest extends TestCase
{
    protected $resource;
    protected $file;
    protected $time;

    protected function setUp()
    {
        $this->file = sys_get_temp_dir().'/tmp.xml';
        $this->time = time();
        touch($this->file, $this->time);
        $this->resource = new FileResource($this->file);
    }

    protected function tearDown()
    {
        if (file_exists($this->file)) {
            @unlink($this->file);
        }
    }

    public function testGetResource()
    {
        $this->assertSame(realpath($this->file), $this->resource->getResource(), '->getResource() returns the path to the resource');
    }

    public function testGetResourceWithScheme()
    {
        $resource = new FileResource('file://'.$this->file);
        $this->assertSame('file://'.$this->file, $resource->getResource(), '->getResource() returns the path to the schemed resource');
    }

    public function testToString()
    {
        $this->assertSame(realpath($this->file), (string) $this->resource);
    }

    public function testResourceDoesNotExist()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessageMatches('/The file ".*" does not exist./');
        new FileResource('/____foo/foobar'.mt_rand(1, 999999));
    }

    public function testIsFresh()
    {
        $this->assertTrue($this->resource->isFresh($this->time), '->isFresh() returns true if the resource has not changed in same second');
        $this->assertTrue($this->resource->isFresh($this->time + 10), '->isFresh() returns true if the resource has not changed');
        $this->assertFalse($this->resource->isFresh($this->time - 86400), '->isFresh() returns false if the resource has been updated');
    }

    public function testIsFreshForDeletedResources()
    {
        unlink($this->file);

        $this->assertFalse($this->resource->isFresh($this->time), '->isFresh() returns false if the resource does not exist');
    }

    public function testSerializeUnserialize()
    {
        unserialize(serialize($this->resource));

        $this->assertSame(realpath($this->file), $this->resource->getResource());
    }
}
