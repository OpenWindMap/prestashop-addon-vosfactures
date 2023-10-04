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

namespace Symfony\Component\Config\Definition;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * Node which only allows a finite set of values.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class EnumNode extends ScalarNode
{
    private $values;

    public function __construct($name, NodeInterface $parent = null, array $values = [])
    {
        $values = array_unique($values);
        if (empty($values)) {
            throw new \InvalidArgumentException('$values must contain at least one element.');
        }

        parent::__construct($name, $parent);
        $this->values = $values;
    }

    public function getValues()
    {
        return $this->values;
    }

    protected function finalizeValue($value)
    {
        $value = parent::finalizeValue($value);

        if (!\in_array($value, $this->values, true)) {
            $ex = new InvalidConfigurationException(sprintf('The value %s is not allowed for path "%s". Permissible values: %s', json_encode($value), $this->getPath(), implode(', ', array_map('json_encode', $this->values))));
            $ex->setPath($this->getPath());

            throw $ex;
        }

        return $value;
    }
}
