# PHP Platform Annotations
This package provides APIs to access annotations in PHP

[![Build Status](https://travis-ci.org/PHPPlatform/annotations.svg?branch=master)](https://travis-ci.org/PHPPlatform/annotations)


## Usage

### Getting the Annotations 
``` PHP
PhpPlatform\Annotations\Annotation::getAnnotations($className, $propertyName="*", $constantName="*", $methodName="*");
```
where 
 - `$className` is complete name of the class for which annotations are needed
 - `$propertyName` is a *string* of a property or *array* of properties for which annotations are needed
 - `$constantName` is a *string* of a constant or *array* of constants for which annotations are needed 
 - `$methodName` is a *string* of a method name or *array* of method names for which annotations are needed
 
### Declaring Annotations

This library supports annotations in DocComments 
Annotation declaration has the format of 
```
 * @KEY VALUE(S) 
```
Where `KEY` may contain subkeys seperated by `.`
and `VALUES` are `space` or `comma` seperated strings


## Example
This Example shows the different forms of annotations and their expected values 
```php
/**
 * @key1
 *  @key2
 * @key3.subKey1
 * @key3.subKey2.subkey21 success
 * @key4 v1
 * @key5 (v2)
 * @key6 v3 v4
 * @key7 "v5\"With Space and Quotes\""
 * @key8 ("v6\"With Space and Quotes\"", v7) 
 * @key9 ("v8\"With Space and Quotes\"", v9) description1
 * @key10 ("v10 With \\", v11) description2
 * @key11 ("123", v12) description3
 * @key12 ("true", "false")
 * @key13 v13
 * @key13 1234
 * @key14 (v14)
 * @key14 v15
 * @ notKey
 * @wrongKey(v16) desc
 *  
 */
public $testDifferrentFormatsOfAnnoptations;
```
The array of annotations returned from 
```PHP
PhpPlatform\Annotations\Annotation::getAnnotations($className, 'testDifferrentFormatsOfAnnoptations');
```
Will be
```php
[
    "testDifferrentFormatsOfAnnoptations" => [
	    "key1" => true,
	    "key2" => true,
	    "key3" => [
	        "subKey1" => true,
	        "subKey2" => [
	            "subkey21" => "success"
	        ]
	    ],
	    "key4" => "v1",
	    "key5" => ["v2"],
	    "key6" => ["v3", "v4"],
	    "key7" => 'v5"With Space and Quotes"',
	    "key8" => ['v6"With Space and Quotes"', "v7"],
	    "key9" => ['v8"With Space and Quotes"', "v9"],
	    "key10" => ["v10 With \\", "v11"],
	    "key11" => [123, "v12"],
	    "key12" => [true, false],
	    "key13" => ['v13', 1234],
	    "key14" => ["v14", "v15"]
	]
]
```