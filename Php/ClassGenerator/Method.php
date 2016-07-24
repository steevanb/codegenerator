<?php

namespace steevanb\CodeGenerator\Php\ClassGenerator;

use steevanb\CodeGenerator\Behavior;

class Method
{
	use Behavior\CommentsTrait;
    use Behavior\IsStaticTrait;
	use Behavior\NameTrait;
    use Behavior\VisibilityTrait;

	/** @var string */
	protected $return = null;

	/** @var MethodParameter[] */
	protected $parameters = array();

	/** @var array */
	protected $exceptions = array();

	/** @var array */
	protected $lines = array();

	/**
	 * @param string $name
	 */
	public function __construct($name = null)
	{
		$this->setName($name);
	}

	/**
	 * @param string $return
	 * @return $this
	 */
	public function setReturn($return)
	{
		$this->return = $return;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getReturn()
	{
		return $this->return;
	}

	/**
	 * @param MethodParameter $parameter
	 * @return $this
	 */
	public function addParameter(MethodParameter $parameter)
	{
		$this->parameters[] = $parameter;

		return $this;
	}

	/**
	 * @param MethodParameter[] $parameters
	 * @return $this
	 */
	public function setParameters(array $parameters)
	{
		$this->parameters = $parameters;

		return $this;
	}

	/**
	 * @return MethodParameter[]
	 */
	public function getParameters()
	{
		return $this->parameters;
	}

	/**
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
	 * @return array
	 */
	public function getThrownedExceptions()
	{
		return $this->exceptions;
	}

	/**
	 * @return array
	 */
	public function getPhpDocParts()
	{
		$return = [];

		if (count($this->getComments()) > 0) {
			$return['comments'] = $this->getComments();
		}

		foreach ($this->getParameters() as $parameter) {
            if (isset($return['@param']) === false) {
                $return['@param'] = [];
            }
			$return['@param'][] = [
				'name' => $parameter->getName(),
				'type' => $parameter->getType(),
				'comment' => $parameter->getComments()
			];
		}

		foreach ($this->getThrownedExceptions() as $type => $comments) {
            if (isset($return['@throws']) === false) {
                $return['@throws'] = [];
            }
			$return['@throws'][] = [
				'type' => $type,
				'comment' => $comments
			];
		}

		if ($this->getReturn() !== null) {
			$return['@return'] = [
				'type' => $this->getReturn(),
				'comment' => null
			];
		}

		return $return;
	}

	/**
	 * @param string $line
	 * @return $this
	 */
	public function addLine($line)
	{
		$this->lines[] = $line;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getLines()
	{
		return $this->lines;
	}
}
