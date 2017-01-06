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

}

class TestAnnotation extends \PHPUnit_Framework_TestCase{

    public function setUp(){
        
    }

    public function testGetAnnotations(){

        $annotations = Annotation::getAnnotations('PhpPlatform\Tests\Annotations\SampleClass');

        $expected = array(
            "class" => array(
                "package" => "icircle",
                "required" => null,
                "tableName" => "SAMPLE",
                "createAccess" => array("group1","group2")
            ),

            "properties" => array(
                "property1" => array(
                    "columnName" => "PEROPERT1",
                    "required" => null,
                    "get" => true,
                    "set" => false,
                    "reference" => null,
                ),
                "property2" => array(
                    "columnName" => "PEROPERT2",
                    "required" => null,
                    "get" => true,
                    "set" => false,
                    "reference" => null
                ),
                "property3" => array(
                    "columnName" => "PEROPERT3",
                    "required" => null,
                    "get" => true,
                    "set" => false,
                    "foreignField" => 'PhpPlatform\annotations\Annotation->property1'
                )
            ),
            "constants" => array(
                "constant1" => array(
                    "version" => 0,
                    "constantValue" => null
                )
            ),
            "methods" => array(
                "setProperty1" => array(
                    "version" => 1
                ),
                "setProperty2" => array(
                    "version" => 2
                )
            )
        );

        $this->assertArraySubset($annotations,$expected);
        $this->assertArraySubset($expected,$annotations);

    }


}