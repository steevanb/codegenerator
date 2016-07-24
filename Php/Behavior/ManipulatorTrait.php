<?php
/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace steevanb\CodeGenerator\Php\Behavior;

/**
 * Changes the PHP code of a Kernel.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
trait ManipulatorTrait
{
	protected static $tokens;
	protected static $line;

	/**
	 * Sets the code to manipulate.
	 *
	 * @param array   $tokens An array of PHP tokens
	 * @param integer $line   The start line of the code
	 */
	protected static function setCode(array $tokens, $line = 0)
	{
		self::$tokens = $tokens;
		self::$line = $line;
	}

	/**
	 * Gets the next token
     *
     * @return mixed
	 */
	protected static function next()
	{
		while ($token = array_shift(static::$tokens)) {
			static::$line += substr_count(static::value($token), "\n");

			if (is_array($token) && in_array($token[0], array(T_WHITESPACE, T_COMMENT, T_DOC_COMMENT))) {
				continue;
			}

			return $token;
		}
	}

	/**
	 * Peeks the next token.
	 *
	 * @param mixed $nb A PHP token
     * @return mixed
	 */
	protected static function peek($nb = 1)
	{
		$i = 0;
		$tokens = static::$tokens;
		while ($token = array_shift($tokens)) {
			if (is_array($token) && in_array($token[0], array(T_WHITESPACE, T_COMMENT, T_DOC_COMMENT))) {
				continue;
			}

			++$i;
			if ($i == $nb) {
				return $token;
			}
		}
	}

	/**
	 * Gets the value of a token.
	 *
	 * @param string The token value
	 */
	protected static function value($token)
	{
		return is_array($token) ? $token[1] : $token;
	}
}
