<?php

namespace steevanb\CodeGenerator\PHP;

use steevanb\CodeGenerator\Model;

/**
 * Method parameter definition
 */
class MethodParameter
{
	use Model\Comments;
    use Model\DefaultValue;
    use Model\Name;
    use Model\Type;
    use Model\Visibility;

	/** @var boolean */
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