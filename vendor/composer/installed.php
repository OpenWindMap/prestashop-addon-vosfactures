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

return array(
    'root' => array(
        'pretty_version' => 'dev-master',
        'version' => 'dev-master',
        'type' => 'library',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'reference' => '56fd1e178cf4ebeb94d6190126709fc0db732498',
        'name' => 'vosfactures/vosfactures',
        'dev' => true,
    ),
    'versions' => array(
        'guzzlehttp/guzzle' => array(
            'pretty_version' => '6.5.8',
            'version' => '6.5.8.0',
            'type' => 'library',
            'install_path' => __DIR__ . '/../guzzlehttp/guzzle',
            'aliases' => array(),
            'reference' => 'a52f0440530b54fa079ce76e8c5d196a42cad981',
            'dev_requirement' => false,
        ),
        'guzzlehttp/promises' => array(
            'pretty_version' => '1.5.2',
            'version' => '1.5.2.0',
            'type' => 'library',
            'install_path' => __DIR__ . '/../guzzlehttp/promises',
            'aliases' => array(),
            'reference' => 'b94b2807d85443f9719887892882d0329d1e2598',
            'dev_requirement' => false,
        ),
        'guzzlehttp/psr7' => array(
            'pretty_version' => '1.9.0',
            'version' => '1.9.0.0',
            'type' => 'library',
            'install_path' => __DIR__ . '/../guzzlehttp/psr7',
            'aliases' => array(),
            'reference' => 'e98e3e6d4f86621a9b75f623996e6bbdeb4b9318',
            'dev_requirement' => false,
        ),
        'paragonie/random_compat' => array(
            'pretty_version' => 'v2.0.21',
            'version' => '2.0.21.0',
            'type' => 'library',
            'install_path' => __DIR__ . '/../paragonie/random_compat',
            'aliases' => array(),
            'reference' => '96c132c7f2f7bc3230723b66e89f8f150b29d5ae',
            'dev_requirement' => false,
        ),
        'prestashop/module-lib-cache-directory-provider' => array(
            'pretty_version' => 'v1.0.0',
            'version' => '1.0.0.0',
            'type' => 'project',
            'install_path' => __DIR__ . '/../prestashop/module-lib-cache-directory-provider',
            'aliases' => array(),
            'reference' => '34a577b66a7e52ae16d6f40efd1db17290bad453',
            'dev_requirement' => false,
        ),
        'prestashop/module-lib-service-container' => array(
            'pretty_version' => '1.4.0',
            'version' => '1.4.0.0',
            'type' => 'library',
            'install_path' => __DIR__ . '/../prestashop/module-lib-service-container',
            'aliases' => array(),
            'reference' => '96f4f551b96cffb1f78462cd4722f0d2b057abda',
            'dev_requirement' => false,
        ),
        'prestashop/prestashop-accounts-installer' => array(
            'pretty_version' => 'v1.0.1',
            'version' => '1.0.1.0',
            'type' => 'library',
            'install_path' => __DIR__ . '/../prestashop/prestashop-accounts-installer',
            'aliases' => array(),
            'reference' => 'f038af2408968d1045330b32aa1fed65fcaf4c9b',
            'dev_requirement' => false,
        ),
        'prestashopcorp/module-lib-billing' => array(
            'pretty_version' => '1.3.2',
            'version' => '1.3.2.0',
            'type' => 'library',
            'install_path' => __DIR__ . '/../prestashopcorp/module-lib-billing',
            'aliases' => array(),
            'reference' => 'e6584b9c89917615b76352ec3dc36f89180351e7',
            'dev_requirement' => false,
        ),
        'psr/cache' => array(
            'pretty_version' => '1.0.1',
            'version' => '1.0.1.0',
            'type' => 'library',
            'install_path' => __DIR__ . '/../psr/cache',
            'aliases' => array(),
            'reference' => 'd11b50ad223250cf17b86e38383413f5a6764bf8',
            'dev_requirement' => false,
        ),
        'psr/cache-implementation' => array(
            'dev_requirement' => false,
            'provided' => array(
                0 => '1.0',
            ),
        ),
        'psr/container' => array(
            'pretty_version' => '1.0.0',
            'version' => '1.0.0.0',
            'type' => 'library',
            'install_path' => __DIR__ . '/../psr/container',
            'aliases' => array(),
            'reference' => 'b7ce3b176482dbbc1245ebf52b181af44c2cf55f',
            'dev_requirement' => false,
        ),
        'psr/container-implementation' => array(
            'dev_requirement' => false,
            'provided' => array(
                0 => '1.0',
            ),
        ),
        'psr/http-message' => array(
            'pretty_version' => '1.0.1',
            'version' => '1.0.1.0',
            'type' => 'library',
            'install_path' => __DIR__ . '/../psr/http-message',
            'aliases' => array(),
            'reference' => 'f6561bf28d520154e4b0ec72be95418abe6d9363',
            'dev_requirement' => false,
        ),
        'psr/http-message-implementation' => array(
            'dev_requirement' => false,
            'provided' => array(
                0 => '1.0',
            ),
        ),
        'psr/log' => array(
            'pretty_version' => '1.1.4',
            'version' => '1.1.4.0',
            'type' => 'library',
            'install_path' => __DIR__ . '/../psr/log',
            'aliases' => array(),
            'reference' => 'd49695b909c3b7628b6289db5479a1c204601f11',
            'dev_requirement' => false,
        ),
        'psr/simple-cache' => array(
            'pretty_version' => '1.0.1',
            'version' => '1.0.1.0',
            'type' => 'library',
            'install_path' => __DIR__ . '/../psr/simple-cache',
            'aliases' => array(),
            'reference' => '408d5eafb83c57f6365a3ca330ff23aa4a5fa39b',
            'dev_requirement' => false,
        ),
        'psr/simple-cache-implementation' => array(
            'dev_requirement' => false,
            'provided' => array(
                0 => '1.0',
            ),
        ),
        'ralouphie/getallheaders' => array(
            'pretty_version' => '3.0.3',
            'version' => '3.0.3.0',
            'type' => 'library',
            'install_path' => __DIR__ . '/../ralouphie/getallheaders',
            'aliases' => array(),
            'reference' => '120b605dfeb996808c31b6477290a714d356e822',
            'dev_requirement' => false,
        ),
        'symfony/cache' => array(
            'pretty_version' => 'v3.4.47',
            'version' => '3.4.47.0',
            'type' => 'library',
            'install_path' => __DIR__ . '/../symfony/cache',
            'aliases' => array(),
            'reference' => 'a7a14c4832760bd1fbd31be2859ffedc9b6ff813',
            'dev_requirement' => false,
        ),
        'symfony/config' => array(
            'pretty_version' => 'v3.4.47',
            'version' => '3.4.47.0',
            'type' => 'library',
            'install_path' => __DIR__ . '/../symfony/config',
            'aliases' => array(),
            'reference' => 'bc6b3fd3930d4b53a60b42fe2ed6fc466b75f03f',
            'dev_requirement' => false,
        ),
        'symfony/dependency-injection' => array(
            'pretty_version' => 'v3.4.47',
            'version' => '3.4.47.0',
            'type' => 'library',
            'install_path' => __DIR__ . '/../symfony/dependency-injection',
            'aliases' => array(),
            'reference' => '51d2a2708c6ceadad84393f8581df1dcf9e5e84b',
            'dev_requirement' => false,
        ),
        'symfony/expression-language' => array(
            'pretty_version' => 'v3.4.47',
            'version' => '3.4.47.0',
            'type' => 'library',
            'install_path' => __DIR__ . '/../symfony/expression-language',
            'aliases' => array(),
            'reference' => 'de38e66398fca1fcb9c48e80279910e6889cb28f',
            'dev_requirement' => false,
        ),
        'symfony/filesystem' => array(
            'pretty_version' => 'v3.4.47',
            'version' => '3.4.47.0',
            'type' => 'library',
            'install_path' => __DIR__ . '/../symfony/filesystem',
            'aliases' => array(),
            'reference' => 'e58d7841cddfed6e846829040dca2cca0ebbbbb3',
            'dev_requirement' => false,
        ),
        'symfony/polyfill-apcu' => array(
            'pretty_version' => 'v1.19.0',
            'version' => '1.19.0.0',
            'type' => 'library',
            'install_path' => __DIR__ . '/../symfony/polyfill-apcu',
            'aliases' => array(),
            'reference' => 'b44b51e7814c23bfbd793a16ead5d7ce43ed23c5',
            'dev_requirement' => false,
        ),
        'symfony/polyfill-ctype' => array(
            'pretty_version' => 'v1.19.0',
            'version' => '1.19.0.0',
            'type' => 'library',
            'install_path' => __DIR__ . '/../symfony/polyfill-ctype',
            'aliases' => array(),
            'reference' => 'aed596913b70fae57be53d86faa2e9ef85a2297b',
            'dev_requirement' => false,
        ),
        'symfony/polyfill-intl-idn' => array(
            'pretty_version' => 'v1.19.0',
            'version' => '1.19.0.0',
            'type' => 'library',
            'install_path' => __DIR__ . '/../symfony/polyfill-intl-idn',
            'aliases' => array(),
            'reference' => '4ad5115c0f5d5172a9fe8147675ec6de266d8826',
            'dev_requirement' => false,
        ),
        'symfony/polyfill-intl-normalizer' => array(
            'pretty_version' => 'v1.19.0',
            'version' => '1.19.0.0',
            'type' => 'library',
            'install_path' => __DIR__ . '/../symfony/polyfill-intl-normalizer',
            'aliases' => array(),
            'reference' => '8db0ae7936b42feb370840cf24de1a144fb0ef27',
            'dev_requirement' => false,
        ),
        'symfony/polyfill-php70' => array(
            'pretty_version' => 'v1.19.0',
            'version' => '1.19.0.0',
            'type' => 'library',
            'install_path' => __DIR__ . '/../symfony/polyfill-php70',
            'aliases' => array(),
            'reference' => '3fe414077251a81a1b15b1c709faf5c2fbae3d4e',
            'dev_requirement' => false,
        ),
        'symfony/polyfill-php72' => array(
            'pretty_version' => 'v1.19.0',
            'version' => '1.19.0.0',
            'type' => 'library',
            'install_path' => __DIR__ . '/../symfony/polyfill-php72',
            'aliases' => array(),
            'reference' => 'beecef6b463b06954638f02378f52496cb84bacc',
            'dev_requirement' => false,
        ),
        'symfony/yaml' => array(
            'pretty_version' => 'v3.4.47',
            'version' => '3.4.47.0',
            'type' => 'library',
            'install_path' => __DIR__ . '/../symfony/yaml',
            'aliases' => array(),
            'reference' => '88289caa3c166321883f67fe5130188ebbb47094',
            'dev_requirement' => false,
        ),
        'vosfactures/vosfactures' => array(
            'pretty_version' => 'dev-master',
            'version' => 'dev-master',
            'type' => 'library',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'reference' => '56fd1e178cf4ebeb94d6190126709fc0db732498',
            'dev_requirement' => false,
        ),
    ),
);
