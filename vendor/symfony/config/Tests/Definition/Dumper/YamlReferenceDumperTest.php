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

namespace Symfony\Component\Config\Tests\Definition\Dumper;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Dumper\YamlReferenceDumper;
use Symfony\Component\Config\Tests\Fixtures\Configuration\ExampleConfiguration;

class YamlReferenceDumperTest extends TestCase
{
    public function testDumper()
    {
        $configuration = new ExampleConfiguration();

        $dumper = new YamlReferenceDumper();

        $this->assertEquals($this->getConfigurationAsString(), $dumper->dump($configuration));
    }

    public function provideDumpAtPath()
    {
        return [
            'Regular node' => ['scalar_true', <<<EOL
scalar_true:          true
EOL
            ],
            'Array node' => ['array', <<<EOL
# some info
array:
    child1:               ~
    child2:               ~

    # this is a long
    # multi-line info text
    # which should be indented
    child3:               ~ # Example: example setting
EOL
            ],
            'Regular nested' => ['array.child2', <<<EOL
child2:               ~
EOL
            ],
            'Prototype' => ['cms_pages.page', <<<EOL
# Prototype
page:

    # Prototype
    locale:
        title:                ~ # Required
        path:                 ~ # Required
EOL
            ],
            'Nested prototype' => ['cms_pages.page.locale', <<<EOL
# Prototype
locale:
    title:                ~ # Required
    path:                 ~ # Required
EOL
            ],
        ];
    }

    /**
     * @dataProvider provideDumpAtPath
     */
    public function testDumpAtPath($path, $expected)
    {
        $configuration = new ExampleConfiguration();

        $dumper = new YamlReferenceDumper();

        $this->assertSame(trim($expected), trim($dumper->dumpAtPath($configuration, $path)));
    }

    private function getConfigurationAsString()
    {
        return <<<'EOL'
acme_root:
    boolean:              true
    scalar_empty:         ~
    scalar_null:          null
    scalar_true:          true
    scalar_false:         false
    scalar_default:       default
    scalar_array_empty:   []
    scalar_array_defaults:

        # Defaults:
        - elem1
        - elem2
    scalar_required:      ~ # Required
    scalar_deprecated:    ~ # Deprecated (The child node "scalar_deprecated" at path "acme_root" is deprecated.)
    scalar_deprecated_with_message: ~ # Deprecated (Deprecation custom message for "scalar_deprecated_with_message" at "acme_root")
    node_with_a_looong_name: ~
    enum_with_default:    this # One of "this"; "that"
    enum:                 ~ # One of "this"; "that"

    # some info
    array:
        child1:               ~
        child2:               ~

        # this is a long
        # multi-line info text
        # which should be indented
        child3:               ~ # Example: example setting
    scalar_prototyped:    []
    parameters:

        # Prototype: Parameter name
        name:                 ~
    connections:

        # Prototype
        -
            user:                 ~
            pass:                 ~
    cms_pages:

        # Prototype
        page:

            # Prototype
            locale:
                title:                ~ # Required
                path:                 ~ # Required
    pipou:

        # Prototype
        name:                 []

EOL;
    }
}
