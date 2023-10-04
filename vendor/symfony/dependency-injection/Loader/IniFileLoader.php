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

namespace Symfony\Component\DependencyInjection\Loader;

use Symfony\Component\Config\Util\XmlUtils;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

/**
 * IniFileLoader loads parameters from INI files.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class IniFileLoader extends FileLoader
{
    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        $path = $this->locator->locate($resource);

        $this->container->fileExists($path);

        // first pass to catch parsing errors
        $result = parse_ini_file($path, true);
        if (false === $result || [] === $result) {
            throw new InvalidArgumentException(sprintf('The "%s" file is not valid.', $resource));
        }

        // real raw parsing
        $result = parse_ini_file($path, true, \INI_SCANNER_RAW);

        if (isset($result['parameters']) && \is_array($result['parameters'])) {
            foreach ($result['parameters'] as $key => $value) {
                $this->container->setParameter($key, $this->phpize($value));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        if (!\is_string($resource)) {
            return false;
        }

        if (null === $type && 'ini' === pathinfo($resource, \PATHINFO_EXTENSION)) {
            return true;
        }

        return 'ini' === $type;
    }

    /**
     * Note that the following features are not supported:
     *  * strings with escaped quotes are not supported "foo\"bar";
     *  * string concatenation ("foo" "bar").
     */
    private function phpize($value)
    {
        // trim on the right as comments removal keep whitespaces
        if ($value !== $v = rtrim($value)) {
            $value = '""' === substr_replace($v, '', 1, -1) ? substr($v, 1, -1) : $v;
        }
        $lowercaseValue = strtolower($value);

        switch (true) {
            case \defined($value):
                return \constant($value);
            case 'yes' === $lowercaseValue || 'on' === $lowercaseValue:
                return true;
            case 'no' === $lowercaseValue || 'off' === $lowercaseValue || 'none' === $lowercaseValue:
                return false;
            case isset($value[1]) && (
                ("'" === $value[0] && "'" === $value[\strlen($value) - 1]) ||
                ('"' === $value[0] && '"' === $value[\strlen($value) - 1])
            ):
                // quoted string
                return substr($value, 1, -1);
            default:
                return XmlUtils::phpize($value);
        }
    }
}
