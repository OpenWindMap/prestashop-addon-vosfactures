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

namespace Symfony\Component\DependencyInjection\Config;

@trigger_error('The '.__NAMESPACE__.'\AutowireServiceResource class is deprecated since Symfony 3.3 and will be removed in 4.0. Use ContainerBuilder::getReflectionClass() instead.', \E_USER_DEPRECATED);

use Symfony\Component\Config\Resource\SelfCheckingResourceInterface;
use Symfony\Component\DependencyInjection\Compiler\AutowirePass;

/**
 * @deprecated since version 3.3, to be removed in 4.0. Use ContainerBuilder::getReflectionClass() instead.
 */
class AutowireServiceResource implements SelfCheckingResourceInterface, \Serializable
{
    private $class;
    private $filePath;
    private $autowiringMetadata = [];

    public function __construct($class, $path, array $autowiringMetadata)
    {
        $this->class = $class;
        $this->filePath = $path;
        $this->autowiringMetadata = $autowiringMetadata;
    }

    public function isFresh($timestamp)
    {
        if (!file_exists($this->filePath)) {
            return false;
        }

        // has the file *not* been modified? Definitely fresh
        if (@filemtime($this->filePath) <= $timestamp) {
            return true;
        }

        try {
            $reflectionClass = new \ReflectionClass($this->class);
        } catch (\ReflectionException $e) {
            // the class does not exist anymore!
            return false;
        }

        return (array) $this === (array) AutowirePass::createResourceForClass($reflectionClass);
    }

    public function __toString()
    {
        return 'service.autowire.'.$this->class;
    }

    /**
     * @internal
     */
    public function serialize()
    {
        return serialize([$this->class, $this->filePath, $this->autowiringMetadata]);
    }

    /**
     * @internal
     */
    public function unserialize($serialized)
    {
        if (\PHP_VERSION_ID >= 70000) {
            list($this->class, $this->filePath, $this->autowiringMetadata) = unserialize($serialized, ['allowed_classes' => false]);
        } else {
            list($this->class, $this->filePath, $this->autowiringMetadata) = unserialize($serialized);
        }
    }

    /**
     * @deprecated Implemented for compatibility with Symfony 2.8
     */
    public function getResource()
    {
        return $this->filePath;
    }
}
