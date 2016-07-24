<?php

namespace steevanb\CodeGenerator\Php\ClassGenerator;

use steevanb\CodeGenerator\Exception\ClassNameExistsException;
use steevanb\CodeGenerator\Php\Behavior\ManipulatorTrait;

class ClassGeneratorFactory
{
	use ManipulatorTrait;

    /**
     * @param string $fileName
     * @return ClassGenerator
     * @throws \Exception
     * @throws ClassNameExistsException
     */
	public static function createFromFile($fileName)
	{
		if (is_readable($fileName) === false) {
			throw new \Exception('File "' . $fileName . '" does not exists or is not readable.');
		}

		$generator = new ClassGenerator();
		$generator
            ->setConcatUses(false)
            ->setExtractUses(false);

        static::setCode(token_get_all(file_get_contents($fileName)));
		$isInClass = false;
        $isInMethodSignature = false;
        $isPublic = true;
        $isProtected = false;
        $isPrivate = false;
        $isStatic = false;
        $method = null;
        $countCloseBrace = 0;
		while ($token = static::next()) {
            if (is_array($token)) {
                if ($isInClass === false) {
                    switch ($token[0]) {
                        case T_NAMESPACE:
                            static::defineNamespace($generator);
                            break;
                        case T_USE:
                            static::addUse($generator);
                            break;
                        case T_CLASS:
                            $isInClass = true;
                            static::defineClassName($generator);
                            break;
                    }
                } else {
                    switch ($token[0]) {
                        case T_EXTENDS:
                            static::defineExtends($generator);
                            break;
                        case T_IMPLEMENTS:
                            static::defineImplements($generator);
                            break;
                        case T_STATIC:
                            $isStatic = true;
                            break;
                        case T_PUBLIC:
                            $isPublic = true;
                            break;
                        case T_PROTECTED:
                            $isProtected = true;
                            break;
                        case T_PRIVATE:
                            $isPrivate = true;
                            break;
                        case T_FUNCTION:
                            $isInMethodSignature = true;
                            $method = static::createMethod($generator, $isPublic, $isProtected, $isPrivate, $isStatic);
                            break;
                    }
                }
            } elseif ($isInMethodSignature && $token === '{') {
                $isInMethodSignature = false;
                $isInMethod = true;
                $countCloseBrace = 1;
            } elseif ($isInMethodSignature === false && $isInClass && $token === '{') {
                $countCloseBrace++;
            } elseif ($isInMethodSignature && $token === '}') {
                $countCloseBrace--;
                if ($countCloseBrace <= 0) {
                    $isInMethod = false;
                }
            }
		}

		return $generator;
	}

    /**
     * @return string
     */
    protected static function getFullyQualifiedClassName()
    {
        $token = static::next();
        $return = null;
        while (is_array($token) && ($token[0] === T_STRING || $token[0] === T_NS_SEPARATOR)) {
            $return .= $token[1];
            $token = static::next();
        }

        return $return;
    }

    /**
     * @param ClassGenerator $generator
     */
    protected static function defineNamespace(ClassGenerator $generator)
    {
        $generator->setNamespace(static::getFullyQualifiedClassName());
    }

    /**
     * @param ClassGenerator $generator
     */
    protected static function addUse(ClassGenerator $generator)
    {
        $generator->addUse(static::getFullyQualifiedClassName());
    }

    /**
     * @param ClassGenerator $generator
     */
    protected static function defineClassName(ClassGenerator $generator)
    {
        $generator->setClassName(static::getFullyQualifiedClassName());
    }

    /**
     * @param ClassGenerator $generator
     */
    protected static function defineExtends(ClassGenerator $generator)
    {
        $generator->setExtends(static::getFullyQualifiedClassName());
    }

    /**
     * @param ClassGenerator $generator
     */
    protected static function defineImplements(ClassGenerator $generator)
    {
        $interface = static::getFullyQualifiedClassName();
        while ($interface !== null) {
            $generator->addInterface($interface);
            $interface = static::getFullyQualifiedClassName();
        };
    }

    /**
     * @param ClassGenerator $generator
     * @param bool $isPublic
     * @param bool $isProtected
     * @param bool $isPrivate
     * @param bool $isStatic
     * @return Method
     */
    protected static function createMethod(ClassGenerator $generator, $isPublic, $isProtected, $isPrivate, $isStatic)
    {
        dd(static::$tokens);
        $token = static::next();
        $method = new Method($token[1]);
        $generator->addMethod($method);

        return $method;
    }
}
