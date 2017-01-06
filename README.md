# PHP Platform Annotations
This package provides APIs to access annotations in PHP

[![Build Status](https://travis-ci.org/PHPPlatform/annotations.svg?branch=master)](https://travis-ci.org/PHPPlatform/annotations)


## Usage

``` PHP
PhpPlatform\Annotations\Annotation::getAnnotations($className, $propertyName="*", $constantName="*", $methodName="*");
```
where 
 - `$className` is complete name of the class for which annotations are needed
 - `$propertyName` is a *string* of a property or *array* of properties for which annotations are needed
 - `$constantName` is a *string* of a constant or *array* of constants for which annotations are needed 
 - `$methodName` is a *string* of a method name or *array* of method names for which annotations are needed

## Example

For example , please see the test case [TestAnnotation][TestAnnotation]

[TestAnnotation]:https://github.com/PHPPlatform/annotations/blob/master/tests/Annotations/TestAnnotation.php