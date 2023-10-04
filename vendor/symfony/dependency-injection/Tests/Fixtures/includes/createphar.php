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

$file = __DIR__.'/ProjectWithXsdExtensionInPhar.phar';
if (is_file($file)) {
    @unlink($file);
}

$phar = new Phar($file, 0, 'ProjectWithXsdExtensionInPhar.phar');
$phar->addFromString('ProjectWithXsdExtensionInPhar.php', <<<'EOT'
<?php

class ProjectWithXsdExtensionInPhar extends ProjectExtension
{
    public function getXsdValidationBasePath()
    {
        return __DIR__.'/schema';
    }

    public function getNamespace()
    {
        return 'http://www.example.com/schema/projectwithxsdinphar';
    }

    public function getAlias()
    {
        return 'projectwithxsdinphar';
    }
}
EOT
);
$phar->addFromString('schema/project-1.0.xsd', <<<'EOT'
<?xml version="1.0" encoding="UTF-8" ?>

<xsd:schema xmlns="http://www.example.com/schema/projectwithxsdinphar"
    xmlns:xsd="http://www.w3.org/2001/XMLSchema"
    targetNamespace="http://www.example.com/schema/projectwithxsdinphar"
    elementFormDefault="qualified">

  <xsd:element name="bar" type="bar" />

  <xsd:complexType name="bar">
    <xsd:attribute name="foo" type="xsd:string" />
  </xsd:complexType>
</xsd:schema>
EOT
);
$phar->setStub('<?php Phar::mapPhar("ProjectWithXsdExtensionInPhar.phar"); require_once "phar://ProjectWithXsdExtensionInPhar.phar/ProjectWithXsdExtensionInPhar.php"; __HALT_COMPILER(); ?>');
