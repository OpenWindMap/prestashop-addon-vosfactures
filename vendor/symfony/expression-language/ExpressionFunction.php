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

namespace Symfony\Component\ExpressionLanguage;

/**
 * Represents a function that can be used in an expression.
 *
 * A function is defined by two PHP callables. The callables are used
 * by the language to compile and/or evaluate the function.
 *
 * The "compiler" function is used at compilation time and must return a
 * PHP representation of the function call (it receives the function
 * arguments as arguments).
 *
 * The "evaluator" function is used for expression evaluation and must return
 * the value of the function call based on the values defined for the
 * expression (it receives the values as a first argument and the function
 * arguments as remaining arguments).
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ExpressionFunction
{
    private $name;
    private $compiler;
    private $evaluator;

    /**
     * @param string   $name      The function name
     * @param callable $compiler  A callable able to compile the function
     * @param callable $evaluator A callable able to evaluate the function
     */
    public function __construct($name, callable $compiler, callable $evaluator)
    {
        $this->name = $name;
        $this->compiler = $compiler;
        $this->evaluator = $evaluator;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getCompiler()
    {
        return $this->compiler;
    }

    public function getEvaluator()
    {
        return $this->evaluator;
    }

    /**
     * Creates an ExpressionFunction from a PHP function name.
     *
     * @param string      $phpFunctionName        The PHP function name
     * @param string|null $expressionFunctionName The expression function name (default: same than the PHP function name)
     *
     * @return self
     *
     * @throws \InvalidArgumentException if given PHP function name does not exist
     * @throws \InvalidArgumentException if given PHP function name is in namespace
     *                                   and expression function name is not defined
     */
    public static function fromPhp($phpFunctionName, $expressionFunctionName = null)
    {
        $phpFunctionName = ltrim($phpFunctionName, '\\');
        if (!\function_exists($phpFunctionName)) {
            throw new \InvalidArgumentException(sprintf('PHP function "%s" does not exist.', $phpFunctionName));
        }

        $parts = explode('\\', $phpFunctionName);
        if (!$expressionFunctionName && \count($parts) > 1) {
            throw new \InvalidArgumentException(sprintf('An expression function name must be defined when PHP function "%s" is namespaced.', $phpFunctionName));
        }

        $compiler = function () use ($phpFunctionName) {
            return sprintf('\%s(%s)', $phpFunctionName, implode(', ', \func_get_args()));
        };

        $evaluator = function () use ($phpFunctionName) {
            $args = \func_get_args();

            return \call_user_func_array($phpFunctionName, array_splice($args, 1));
        };

        return new self($expressionFunctionName ?: end($parts), $compiler, $evaluator);
    }
}
