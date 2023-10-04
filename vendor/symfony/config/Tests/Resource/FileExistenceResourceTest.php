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
use Symfony\Component\Config\Resource\FileExistenceResource;

class FileExistenceResourceTest extends TestCase
{
    protected $resource;
    protected $file;
    protected $time;

    protected function setUp()
    {
        $this->file = realpath(sys_get_temp_dir()).'/tmp.xml';
        $this->time = time();
        $this->resource = new FileExistenceResource($this->file);
    }

    protected function tearDown()
    {
        if (file_exists($this->file)) {
            @unlink($this->file);
        }
    }

    public function testToString()
    {
        $this->assertSame($this->file, (string) $this->resource);
    }

    public function testGetResource()
    {
        $this->assertSame($this->file, $this->resource->getResource(), '->getResource() returns the path to the resource');
    }

    public function testIsFreshWithExistingResource()
    {
        touch($this->file, $this->time);
        $serialized = serialize(new FileExistenceResource($this->file));

        $resource = unserialize($serialized);
        $this->assertTrue($resource->isFresh($this->time), '->isFresh() returns true if the resource is still present');

        unlink($this->file);
        $resource = unserialize($serialized);
        $this->assertFalse($resource->isFresh($this->time), '->isFresh() returns false if the resource has been deleted');
    }

    public function testIsFreshWithAbsentResource()
    {
        $serialized = serialize(new FileExistenceResource($this->file));

        $resource = unserialize($serialized);
        $this->assertTrue($resource->isFresh($this->time), '->isFresh() returns true if the resource is still absent');

        touch($this->file, $this->time);
        $resource = unserialize($serialized);
        $this->assertFalse($resource->isFresh($this->time), '->isFresh() returns false if the resource has been created');
    }
}
