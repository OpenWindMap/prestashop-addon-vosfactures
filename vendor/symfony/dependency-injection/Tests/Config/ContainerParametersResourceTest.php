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

namespace Symfony\Component\DependencyInjection\Tests\Config;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Config\ContainerParametersResource;

class ContainerParametersResourceTest extends TestCase
{
    /** @var ContainerParametersResource */
    private $resource;

    protected function setUp()
    {
        $this->resource = new ContainerParametersResource(['locales' => ['fr', 'en'], 'default_locale' => 'fr']);
    }

    public function testToString()
    {
        $this->assertSame('container_parameters_9893d3133814ab03cac3490f36dece77', (string) $this->resource);
    }

    public function testSerializeUnserialize()
    {
        $unserialized = unserialize(serialize($this->resource));

        $this->assertEquals($this->resource, $unserialized);
    }

    public function testGetParameters()
    {
        $this->assertSame(['locales' => ['fr', 'en'], 'default_locale' => 'fr'], $this->resource->getParameters());
    }
}
