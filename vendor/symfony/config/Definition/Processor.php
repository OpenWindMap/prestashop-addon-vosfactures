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

/**
 * This class is the entry point for config normalization/merging/finalization.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class Processor
{
    /**
     * Processes an array of configurations.
     *
     * @param NodeInterface $configTree The node tree describing the configuration
     * @param array         $configs    An array of configuration items to process
     *
     * @return array The processed configuration
     */
    public function process(NodeInterface $configTree, array $configs)
    {
        $currentConfig = [];
        foreach ($configs as $config) {
            $config = $configTree->normalize($config);
            $currentConfig = $configTree->merge($currentConfig, $config);
        }

        return $configTree->finalize($currentConfig);
    }

    /**
     * Processes an array of configurations.
     *
     * @param ConfigurationInterface $configuration The configuration class
     * @param array                  $configs       An array of configuration items to process
     *
     * @return array The processed configuration
     */
    public function processConfiguration(ConfigurationInterface $configuration, array $configs)
    {
        return $this->process($configuration->getConfigTreeBuilder()->buildTree(), $configs);
    }

    /**
     * Normalizes a configuration entry.
     *
     * This method returns a normalize configuration array for a given key
     * to remove the differences due to the original format (YAML and XML mainly).
     *
     * Here is an example.
     *
     * The configuration in XML:
     *
     * <twig:extension>twig.extension.foo</twig:extension>
     * <twig:extension>twig.extension.bar</twig:extension>
     *
     * And the same configuration in YAML:
     *
     * extensions: ['twig.extension.foo', 'twig.extension.bar']
     *
     * @param array  $config A config array
     * @param string $key    The key to normalize
     * @param string $plural The plural form of the key if it is irregular
     *
     * @return array
     */
    public static function normalizeConfig($config, $key, $plural = null)
    {
        if (null === $plural) {
            $plural = $key.'s';
        }

        if (isset($config[$plural])) {
            return $config[$plural];
        }

        if (isset($config[$key])) {
            if (\is_string($config[$key]) || !\is_int(key($config[$key]))) {
                // only one
                return  [$config[$key]];
            }

            return  $config[$key];
        }

        return [];
    }
}
