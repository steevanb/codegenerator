<?php

namespace steevanb\CodeGenerator\PHP;

use steevanb\CodeGenerator\Model\Comments;
use steevanb\CodeGenerator\Model\IsStatic;
use steevanb\CodeGenerator\Model\Name;
use steevanb\CodeGenerator\Model\Visibility;

class ClassMethod
{

	use Comments,
	 IsStatic,
	 Name,
	 Visibility;
	/**
	 * Mrethod return type
	 *
	 * @var string
	 */
	protected $return = null;

	/**
	 * Constructor
	 *
	 * @param string $name
	 */
	public function __construct($name = null)
	{
		$this->setName($name);
	}

	public function setReturn($return)
	{
		$this->return = $return;
		return $this;
	}

	public function getReturn()
	{
		return $return;
	}

}