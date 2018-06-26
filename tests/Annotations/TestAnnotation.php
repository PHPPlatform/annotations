<?php

namespace PhpPlatform\Tests\Annotations;

use PhpPlatform\Annotations\Annotation;

/**
 * Class SampleClass
 * @package icircle\annotations
 * @required
 * @tableName SAMPLE
 * @createAccess (group1,group2)
 */
class SampleClass{
    /**
     * @columnName PEROPERT1
     * @required
     * @get true
     * @set false
     * @reference
     */
    private $property1 = null;

    /**
     * @columnName PEROPERT2
     * @required
     * @get true
     * @set false
     * @reference
     */
    private $property2 = null;

    /**
     * @columnName PEROPERT3
     * @required
     * @get true
     * @set false
     * @foreignField "PhpPlatform\\annotations\\Annotation->property1"
     */
    private $property3 = null;

    /**
     * @version 0
     * @constantValue
     */
    const constant1 = 'constant 1 value';

    /**
     * @version 1
     */
    public function setProperty1(){

    }

    /**
     * @version 2
     */
    public function setProperty2(){

    }
    
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
    public function testDifferrentFormatsOfAnnoptations(){
        
    }

}

class TestAnnotation extends \PHPUnit_Framework_TestCase{

    public function setUp(){
        
    }

    public function testGetAnnotations(){

        $annotations = Annotation::getAnnotations('PhpPlatform\Tests\Annotations\SampleClass');

        $expected = array(
            "class" => array(
                "package" => 'icircle\annotations',
                "required" => true,
                "tableName" => "SAMPLE",
                "createAccess" => array("group1","group2")
            ),

            "properties" => array(
                "property1" => array(
                    "columnName" => "PEROPERT1",
                    "required" => true,
                    "get" => true,
                    "set" => false,
                    "reference" => true,
                ),
                "property2" => array(
                    "columnName" => "PEROPERT2",
                    "required" => true,
                    "get" => true,
                    "set" => false,
                    "reference" => true
                ),
                "property3" => array(
                    "columnName" => "PEROPERT3",
                    "required" => true,
                    "get" => true,
                    "set" => false,
                    "foreignField" => 'PhpPlatform\annotations\Annotation->property1'
                )
            ),
            "constants" => array(
                "constant1" => array(
                    "version" => 0,
                    "constantValue" => true
                )
            ),
            "methods" => array(
                "setProperty1" => array(
                    "version" => 1
                ),
                "setProperty2" => array(
                    "version" => 2
                ),
                "testDifferrentFormatsOfAnnoptations" =>array(
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
                )
            )
        );

        $this->assertArraySubset($annotations,$expected);
        $this->assertArraySubset($expected,$annotations);

    }


}