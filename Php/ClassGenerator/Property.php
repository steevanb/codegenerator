<?php

namespace steevanb\CodeGenerator\Php\ClassGenerator;

use steevanb\CodeGenerator\Behavior;

class Property
{
	use Behavior\NameTrait;
    use Behavior\VisibilityTrait;
    use Behavior\IsStaticTrait;
    use Behavior\CommentsTrait;
    use Behavior\TypeTrait;
    use Behavior\DefaultValueTrait;

	/**
	 * @param string|null $name
	 */
	public function __construct($name = null)
	{
		$this->setName($name);
	}
}
