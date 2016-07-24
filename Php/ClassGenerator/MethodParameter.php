<?php

namespace steevanb\CodeGenerator\Php\ClassGenerator;

use steevanb\CodeGenerator\Behavior;

class MethodParameter
{
	use Behavior\CommentsTrait;
    use Behavior\DefaultValueTrait;
    use Behavior\NameTrait;
    use Behavior\TypeTrait;
    use Behavior\VisibilityTrait;

	/** @var boolean */
	protected $forceType = false;

	/**
	 * @param string $name
	 */
	public function __construct($name = null)
	{
		$this->setName($name);
	}

	/**
	 * @param boolean $force
	 * @return $this
	 */
	public function setForceType($force)
	{
		$this->forceType = $force;

		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getForceType()
	{
		return $this->forceType;
	}
}
