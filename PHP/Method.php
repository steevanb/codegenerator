<?php

namespace steevanb\CodeGenerator\PHP;

use steevanb\CodeGenerator\Model;

class Method
{
	use Model\Comments;
    use Model\IsStatic;
	use Model\Name;
    use Model\Visibility;

	/** @var string */
	protected $return = null;

	/** @var MethodParameter[] */
	protected $parameters = array();

	/** @var array */
	protected $exceptions = array();

	/** @var array */
	protected $lines = array();

	/**
	 * Constructor
	 *
	 * @param string $name
	 */
	public function __construct($name = null)
	{
		$this->setName($name);
	}

	/**
	 * Define return type
	 *
	 * @param string $return
	 * @return $this
	 */
	public function setReturn($return)
	{
		$this->return = $return;

		return $this;
	}

	/**
	 * Get return type
	 *
	 * @return string
	 */
	public function getReturn()
	{
		return $this->return;
	}

	/**
	 * Add parameter
	 *
	 * @param MethodParameter $parameter
	 * @return $this
	 */
	public function addParameter(MethodParameter $parameter)
	{
		$this->parameters[] = $parameter;

		return $this;
	}

	/**
	 * Define all parameters
	 *
	 * @param MethodParameter[] $parameters
	 * @return $this
	 */
	public function setParameters(array $parameters)
	{
		$this->parameters = $parameters;

		return $this;
	}

	/**
	 * Get parameters
	 *
	 * @return MethodParameter[]
	 */
	public function getParameters()
	{
		return $this->parameters;
	}

	/**
	 * Add throwned exception
	 *
	 * @param string $name
	 * @param string $comment
	 * @return $this
	 */
	public function addThrownedException($name, $comment)
	{
		$this->exceptions[$name] = $comment;

		return $this;
	}

	/**
	 * Get throwned exceptions
	 *
	 * @return array
	 */
	public function getThrownedExceptions()
	{
		return $this->exceptions;
	}

	/**
	 * Get php doc parts, to use with PHPDoc::generate
	 *
	 * @return array
	 */
	public function getPHPDocParts()
	{
		$return = array(
			'comments' => array(),
			'@param' => array(),
			'@throws' => array()
		);

		// comments
		if (count($this->getComments()) > 0) {
			$return['comments'] = $this->getComments();
		}

		// parameters
		foreach ($this->getParameters() as $parameter) {
			$return['@param'][] = array(
				'name' => $parameter->getName(),
				'type' => $parameter->getType(),
				'comment' => $parameter->getComments()
			);
		}

		// exceptions
		foreach ($this->getThrownedExceptions() as $type => $comments) {
			$return['@throws'][] = array(
				'type' => $type,
				'comment' => $comments
			);
		}

		// return
		if ($this->getReturn() !== null) {
			$return['@return'] = array(
				'type' => $this->getReturn(),
				'comment' => null
			);
		}

		return $return;
	}

	/**
	 * Add line
	 *
	 * @param string $line
	 * @return $this
	 */
	public function addLine($line)
	{
		$this->lines[] = $line;

		return $this;
	}

	/**
	 * Get lines
	 *
	 * @return array
	 */
	public function getLines()
	{
		return $this->lines;
	}
}
