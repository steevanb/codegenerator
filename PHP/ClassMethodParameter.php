<?php

namespace steevanb\CodeGenerator\PHP;

use steevanb\CodeGenerator\Model\Comments;
use steevanb\CodeGenerator\Model\DefaultValue;
use steevanb\CodeGenerator\Model\Name;
use steevanb\CodeGenerator\Model\Type;
use steevanb\CodeGenerator\Model\Visibility;

/**
 * Method parameter definition
 */
class ClassMethodParameter
{

	use Comments,
	 DefaultValue,
	 Name,
	 Type,
	 Visibility;
	/**
	 * Indicate if type will be forced in declaration
	 *
	 * @var boolean
	 */
	protected $forceType = false;

	/**
	 * Cosntructor
	 *
	 * @param string $name
	 */
	public function __construct($name = null)
	{
		$this->setName($name);
	}

	/**
	 * Define if type will be forced in declaration
	 *
	 * @param boolean $force
	 * @return $this
	 */
	public function setForceType($force)
	{
		$this->forceType = $force;
		return $this;
	}

	/**
	 * Return if type will be forced in declaration
	 *
	 * @return boolean
	 */
	public function getForceType()
	{
		return $this->forceType;
	}

}