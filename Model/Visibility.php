<?php

namespace steevanb\CodeGenerator\Model;

use steevanb\CodeGenerator\Core\Generator;

/**
 * Add visibility property and accessors
 */
trait Visibility
{
	/**
	 * Visibility
	 *
	 * @var int
	 */
	protected $visibility = Generator::VISIBILITY_PUBLIC;

	/**
	 * Set visibility
	 *
	 * @param int $visibility
	 * @return $this
	 */
	public function setVisibility($visibility)
	{
		$this->visibility = $visibility;
		return $this;
	}

	/**
	 * Get visibility
	 *
	 * @return int
	 */
	public function getVisibility()
	{
		return $this->visibility;
	}

}