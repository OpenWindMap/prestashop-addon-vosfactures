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

namespace Symfony\Component\Cache\Adapter;

use Symfony\Component\Cache\Exception\CacheException;
use Symfony\Component\Cache\PruneableInterface;
use Symfony\Component\Cache\Traits\PhpFilesTrait;

class PhpFilesAdapter extends AbstractAdapter implements PruneableInterface
{
    use PhpFilesTrait;

    /**
     * @param string      $namespace
     * @param int         $defaultLifetime
     * @param string|null $directory
     *
     * @throws CacheException if OPcache is not enabled
     */
    public function __construct($namespace = '', $defaultLifetime = 0, $directory = null)
    {
        if (!static::isSupported()) {
            throw new CacheException('OPcache is not enabled.');
        }
        parent::__construct('', $defaultLifetime);
        $this->init($namespace, $directory);

        $e = new \Exception();
        $this->includeHandler = function () use ($e) { throw $e; };
        $this->zendDetectUnicode = filter_var(ini_get('zend.detect_unicode'), \FILTER_VALIDATE_BOOLEAN);
    }
}
