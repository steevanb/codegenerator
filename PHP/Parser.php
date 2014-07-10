<?php

namespace steevanb\CodeGenerator\PHP;

/**
 * Parse a PPH file
 */
class Parser
{

	use Manipulator;

	/**
	 * Return fully qualified class from tokens
	 *
	 * @return string
	 */
	protected function _getFullQyalifiedClassName()
	{
		$token = $this->next();
		$return = null;
		while (is_string($token) === false) {
			$return .= $token[1];
			$token = $this->next();
		}
		return $return;
	}

	public function getClassGeneratorFromFile($fileName)
	{
		if (is_readable($fileName) === false) {
			throw new \Exception('File "' . $fileName . '" does not exists or is not readable.');
		}

		$return = new ClassGenerator();
		$return->setConcatUses(false);

		// search uses, reflection can't give it, and class name
		$this->setCode(token_get_all(file_get_contents($fileName)));
		$isInClass = false;
		while ($token = $this->next()) {
			// namespace
			if ($token[0] == T_NAMESPACE) {
				$return->setNamespace($this->_getFullQyalifiedClassName());
			}

			// uses
			if ($isInClass === false && $token[0] == T_USE) {
				$return->addUse($this->_getFullQyalifiedClassName());
			}

			// class name
			if ($token[0] == T_CLASS) {
				$isInClass = true;
				$token = $this->next();
				$return->setClassName($token[1]);
			}
		}

		require_once($fileName);
		$fullyQualifiedClassName = $return->getFullyQualifiedClassName();
		$reflection = new \ReflectionClass($fullyQualifiedClassName);
		foreach ($reflection->getProperties() as $reflectionProperty) {
			$reflectionProperty->setAccessible(true);
			if ($reflectionProperty->getDeclaringClass()->getName() == $fullyQualifiedClassName) {
				$property = ClassProperty::getFromReflection($reflectionProperty);
				$return->addProperty($property);
			}
		}

		d($return->getcode());

		return $return;
	}

}