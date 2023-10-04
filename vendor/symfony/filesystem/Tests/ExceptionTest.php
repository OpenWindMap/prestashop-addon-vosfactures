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

namespace Symfony\Component\Filesystem\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * Test class for Filesystem.
 */
class ExceptionTest extends TestCase
{
    public function testGetPath()
    {
        $e = new IOException('', 0, null, '/foo');
        $this->assertEquals('/foo', $e->getPath(), 'The pass should be returned.');
    }

    public function testGeneratedMessage()
    {
        $e = new FileNotFoundException(null, 0, null, '/foo');
        $this->assertEquals('/foo', $e->getPath());
        $this->assertEquals('File "/foo" could not be found.', $e->getMessage(), 'A message should be generated.');
    }

    public function testGeneratedMessageWithoutPath()
    {
        $e = new FileNotFoundException();
        $this->assertEquals('File could not be found.', $e->getMessage(), 'A message should be generated.');
    }

    public function testCustomMessage()
    {
        $e = new FileNotFoundException('bar', 0, null, '/foo');
        $this->assertEquals('bar', $e->getMessage(), 'A custom message should be possible still.');
    }
}
