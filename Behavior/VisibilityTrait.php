<?php

namespace steevanb\CodeGenerator\Behavior;

use steevanb\CodeGenerator\AbstractGenerator;

/**
 * Add visibility property and accessors
 */
trait VisibilityTrait
{
	/** @var int */
	protected $visibility = AbstractGenerator::VISIBILITY_PUBLIC;

	/**
	 * @param int $visibility
	 * @return $this
	 */
	public function setVisibility($visibility)
	{
		$this->visibility = $visibility;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getVisibility()
	{
		return $this->visibility;
	}
}
