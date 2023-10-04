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

namespace Symfony\Component\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\EnvParameterException;

/**
 * This class is used to remove circular dependencies between individual passes.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class Compiler
{
    private $passConfig;
    private $log = [];
    private $loggingFormatter;
    private $serviceReferenceGraph;

    public function __construct()
    {
        $this->passConfig = new PassConfig();
        $this->serviceReferenceGraph = new ServiceReferenceGraph();
    }

    /**
     * Returns the PassConfig.
     *
     * @return PassConfig The PassConfig instance
     */
    public function getPassConfig()
    {
        return $this->passConfig;
    }

    /**
     * Returns the ServiceReferenceGraph.
     *
     * @return ServiceReferenceGraph The ServiceReferenceGraph instance
     */
    public function getServiceReferenceGraph()
    {
        return $this->serviceReferenceGraph;
    }

    /**
     * Returns the logging formatter which can be used by compilation passes.
     *
     * @return LoggingFormatter
     *
     * @deprecated since version 3.3, to be removed in 4.0. Use the ContainerBuilder::log() method instead.
     */
    public function getLoggingFormatter()
    {
        if (null === $this->loggingFormatter) {
            @trigger_error(sprintf('The %s() method is deprecated since Symfony 3.3 and will be removed in 4.0. Use the ContainerBuilder::log() method instead.', __METHOD__), \E_USER_DEPRECATED);

            $this->loggingFormatter = new LoggingFormatter();
        }

        return $this->loggingFormatter;
    }

    /**
     * Adds a pass to the PassConfig.
     *
     * @param CompilerPassInterface $pass A compiler pass
     * @param string                $type The type of the pass
     */
    public function addPass(CompilerPassInterface $pass, $type = PassConfig::TYPE_BEFORE_OPTIMIZATION/*, int $priority = 0*/)
    {
        if (\func_num_args() >= 3) {
            $priority = func_get_arg(2);
        } else {
            if (__CLASS__ !== static::class) {
                $r = new \ReflectionMethod($this, __FUNCTION__);
                if (__CLASS__ !== $r->getDeclaringClass()->getName()) {
                    @trigger_error(sprintf('Method %s() will have a third `int $priority = 0` argument in version 4.0. Not defining it is deprecated since Symfony 3.2.', __METHOD__), \E_USER_DEPRECATED);
                }
            }

            $priority = 0;
        }

        $this->passConfig->addPass($pass, $type, $priority);
    }

    /**
     * Adds a log message.
     *
     * @param string $string The log message
     *
     * @deprecated since version 3.3, to be removed in 4.0. Use the ContainerBuilder::log() method instead.
     */
    public function addLogMessage($string)
    {
        @trigger_error(sprintf('The %s() method is deprecated since Symfony 3.3 and will be removed in 4.0. Use the ContainerBuilder::log() method instead.', __METHOD__), \E_USER_DEPRECATED);

        $this->log[] = $string;
    }

    /**
     * @final
     */
    public function log(CompilerPassInterface $pass, $message)
    {
        if (false !== strpos($message, "\n")) {
            $message = str_replace("\n", "\n".\get_class($pass).': ', trim($message));
        }

        $this->log[] = \get_class($pass).': '.$message;
    }

    /**
     * Returns the log.
     *
     * @return array Log array
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * Run the Compiler and process all Passes.
     */
    public function compile(ContainerBuilder $container)
    {
        try {
            foreach ($this->passConfig->getPasses() as $pass) {
                $pass->process($container);
            }
        } catch (\Exception $e) {
            $usedEnvs = [];
            $prev = $e;

            do {
                $msg = $prev->getMessage();

                if ($msg !== $resolvedMsg = $container->resolveEnvPlaceholders($msg, null, $usedEnvs)) {
                    $r = new \ReflectionProperty($prev, 'message');
                    $r->setAccessible(true);
                    $r->setValue($prev, $resolvedMsg);
                }
            } while ($prev = $prev->getPrevious());

            if ($usedEnvs) {
                $e = new EnvParameterException($usedEnvs, $e);
            }

            throw $e;
        } finally {
            $this->getServiceReferenceGraph()->clear();
        }
    }
}
