master
------

- [SensioLabsInsight platinum medal](https://insight.sensiolabs.com/projects/331e1d3a-7bb3-4b52-be97-70c4a38bcbb4)
- Rename PHP/Parser::_getFullQyalifiedClassName() to getFullQyalifiedClassName()
- Rename CodeGenerator/Core::_createDir() to createDir()

1.2.0 (2014-07-15)
------------------

- Create and use some traits
- Add ClassGenerator::setEndPHPTag() and getEndPHPTag()
- Add ClassGenerator::setConcatUses() and getConcatUses()
- Add ClassGenerator::setConcatTraits() and getConcatTraits()
- Add ClassGenerator::getFullyQualifiedClassName()
- Add ClassProperty, and use it in ClassGenerator::addProperty()
- Add Method, and use it in ClassGenerator (removed startMethod(), addMethodParameter(), addMethodLine() and finishMethod(), add addMethod())

1.1.0 (2014-07-08)
------------------

- Add steevanb\CodeGenerator\Exception\ClassNameExists
- Rename steevanb\CodeGenerator\PHP\MethodNotStarted to steevanb\CodeGenerator\Exception\MethodNotStarted
- Replace tabulations by spaces

1.0.0 (2014-07-05)
------------------

- Creating repository
