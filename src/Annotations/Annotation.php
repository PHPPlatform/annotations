<?php

namespace PhpPlatform\Annotations;

class Annotation{

    /**
     * This method returns the annotations for the specified class or specified member of the class
     * @param string $className : Name of the class
     * @param string|array $propertyName [Optional]: Name of the property(s) in the specified class,
     *                            if null then no annotations is returned for properties
     *                            if "*" or omitted then annotations is returned for all properties
     * @param string|array $constantName [Optional]: Name of the constant(s) in the specified class,
     *                            if null then no annotations is returned for constants
     *                            if "*" or omitted then annotations is returned for all constants
     * @param string|array $methodName [Optional]: Name of the method(s) in the specified class,
     *                            if null then no annotations is returned for methods
     *                            if "*" or omitted then annotations is returned for all methods
     *
     * @throws \Exception if error
     *
     * @returns array of annotations
     *
     *         array("class"=>array("annotation1"=>"value1","annotation2"=>"value2",...),
     *               "properties"=>array("property1"=>array("annotation1"=>"value1","annotation2"=>"value2",...),
     *                                "property2"=>array("annotation1"=>"value1","annotation2"=>"value2",...),
     *                                ...),
     *               "constants"=>array("property1"=>array("annotation1"=>"value1","annotation2"=>"value2",...),
     *                                "property2"=>array("annotation1"=>"value1","annotation2"=>"value2",...),
     *                                ...),
     *               "methods"=>array("property1"=>array("annotation1"=>"value1","annotation2"=>"value2",...),
     *                                "property2"=>array("annotation1"=>"value1","annotation2"=>"value2",...),
     *                                ...)
     *               )
     */
    static public function getAnnotations($className,$propertyName="*",$constantName="*",$methodName="*"){

        if(!class_exists($className,true)){
            throw new \Exception("Unable to get annotataions , Class not defined : $className");
        }

        $reflectionClass = new \ReflectionClass($className);
        $propertyNames = array();
        $constantNames = array();
        $methodNames   = array();

        // Validate inputs
        if($propertyName !== null) {
            if($propertyName == "*"){
                $props = $reflectionClass->getProperties();
                foreach ($props as $prop) {
                    $propertyNames[] =  $prop->getName();
                }
            }else if(is_string($propertyName)){
                $propertyNames[] = $propertyName;
            }else if(is_array($propertyName)){
                $propertyNames = $propertyName;
            }else{
                throw new \Exception("Invalid Input");
            }
        }

        if($constantName !== null) {
            if($constantName == "*"){
                $props = $reflectionClass->getConstants();
                $constantNames = array_keys($props);
            }else if(is_string($constantName)){
                $constantNames[] = $constantName;
            }else if(is_array($constantName)){
                $constantNames = $constantName;
            }else{
                throw new \Exception("Invalid Input");
            }
        }

        if($methodName !== null) {
            if($methodName == "*"){
                $props = $reflectionClass->getMethods();
                foreach ($props as $prop) {
                    $methodNames[] =  $prop->getName();
                }
            }else if(is_string($methodName)){
                $methodNames[] = $methodName;
            }else if(is_array($methodName)){
                $methodNames = $methodName;
            }else{
                throw new \Exception("Invalid Input");
            }
        }

        $annotations = array();
        $classDocComment = $reflectionClass->getDocComment();
        $classAnnotations = self::getAnnotationsFromDocComment($classDocComment,"class");

        $annotations["class"] = $classAnnotations;

        //get annotations for properties
        $propertyAnnotations = array();
        $reflectionProperties = $reflectionClass->getProperties();
        foreach($reflectionProperties as $reflectionProperty){
            $_propertyName = $reflectionProperty->getName();
            if(in_array($_propertyName,$propertyNames)){
                $propertyDocComment = $reflectionProperty->getDocComment();
                $_propertyAnnotations = self::getAnnotationsFromDocComment($propertyDocComment,"property");
                if($_propertyAnnotations !== FALSE){
                    $propertyAnnotations[$_propertyName] = $_propertyAnnotations;
                }
            }
        }
        $annotations["properties"] = $propertyAnnotations;

        //get annotations for constants
        $constantAnnotations = array();
        $reflectionConstants = self::getConstDocComments($reflectionClass);
        foreach($reflectionConstants as $_constantName=>$constantDocComment){
            if(in_array($_constantName,$constantNames)){
                $_constantAnnotations = self::getAnnotationsFromDocComment($constantDocComment,"constant");
                if($_constantAnnotations !== FALSE){
                    $constantAnnotations[$_constantName] = $_constantAnnotations;
                }
            }
        }
        $annotations["constants"] = $constantAnnotations;

        //get annotations for methods
        $methodAnnotations = array();
        $reflectionMethods = $reflectionClass->getMethods();
        foreach($reflectionMethods as $reflectionMethod){
            $_methodName = $reflectionMethod->getName();
            if(in_array($_methodName,$methodNames)){
                $methodDocComment = $reflectionMethod->getDocComment();
                $_methodAnnotations = self::getAnnotationsFromDocComment($methodDocComment,"method");
                if($_methodAnnotations !== FALSE){
                    $methodAnnotations[$_methodName] = $_methodAnnotations;
                }
            }
        }
        $annotations["methods"] = $methodAnnotations;

        return $annotations;
    }

    /**
     * @param \ReflectionClass $clazz
     * @return array
     */
    private static function getConstDocComments($clazz){

        $constDocComments = array();

        $content = file_get_contents($clazz->getFileName());
        $tokens = token_get_all($content);

        $doc = null;
        $isConst = false;
        foreach($tokens as $token){

            if(!is_array($token)){
                $token = array($token,'');
            }
            list($tokenType, $tokenValue) = $token;

            switch ($tokenType){
                // ignored tokens
                case T_WHITESPACE:
                case T_COMMENT:
                    break;

                case T_DOC_COMMENT:
                    $doc = $tokenValue;
                    break;

                case T_CONST:
                    $isConst = true;
                    break;

                case T_STRING:
                    if ($isConst){
                        $constDocComments[$tokenValue] = $doc;
                    }
                    $doc = null;
                    $isConst = false;
                    break;

                // all other tokens reset the parser
                default:
                    $doc = null;
                    $isConst = false;
                    break;
            }
        }

        return $constDocComments;
    }


    /**
     * @param $docComment
     * @param $type
     * @return array
     *
     * " * @key"
     * " * @key value "
     * " *@key value  "
     * " * @key value Description"
     * " * @key (value1,...)"
     * " * @key "value with spaces" "
     * " * @key.subkey"
     * " * @key.subkey value "
     * " *@key.subkey value  "
     * " * @key.subkey value Description"
     * " * @key.subkey (value1,...)"
     * " * @key.subkey "value with spaces" "
     */

    static private function getAnnotationsFromDocComment($docComment,$type){

        $docCommentLines = preg_split("/[\r]*\n/",$docComment);

        $annotations = array();
        foreach($docCommentLines as $docCommentLine){
            try{
                $keysAndValues = self::getKeysAndValues($docCommentLine);
                $_annotations = $keysAndValues['values'];
                $keys = $keysAndValues['keys'];
                $keysCount = count($keys);
                for($i = $keysCount -1 ; $i >= 0 ; $i--){
                    $_annotations = [$keys[$i] => $_annotations];
                }
                $annotations = array_merge_recursive($annotations,$_annotations);
            }catch (\Exception $e){
                // dont do anything
            }
        }

        if(count($annotations) > 0){
            return $annotations;
        }else{
            return FALSE;
        }
    }
    
    /**
     * This method returns the keys and values in the doc comment line
     * @param string $docCommentLine
     * @return array
     * @throws \Exception if keys and/value can not be extracted
     */
    static private function getKeysAndValues($docCommentLine){
        
        if(strpos($docCommentLine,"@") === FALSE){
            throw new \Exception('Parse Error');
        }
        
        $posAmp = strpos($docCommentLine, '@');
        $strBeforeAmp = substr($docCommentLine, 0, $posAmp);
        if(trim($strBeforeAmp) != "*") throw new \Exception('Parse Error');
        
        $docCommentLine = substr($docCommentLine, $posAmp+1);
        
        $posFirstSpace = strpos($docCommentLine, ' ');
        $keyStr = $posFirstSpace !== FALSE ? substr($docCommentLine, 0, $posFirstSpace) : $docCommentLine;
        
        if(trim($keyStr) == '') throw new \Exception('Parse Error');
        
        if(strpos($keyStr, '(') !== FALSE) throw new \Exception('Parse Error');
        
        $keys = preg_split('/\./', trim($keyStr));
        
        $valueStr = trim(substr($docCommentLine, strlen($keyStr)));
        
        if(strlen($valueStr) == 0){
            // default value is true
            $valueStr = "true";
        }
        
        $valueIsInBracket = false;
        if(strpos($valueStr, '(') === 0){
            $valueIsInBracket = true;
            $valueStr = substr($valueStr, 1);
        }
        
        $values = [];
        $valueChars = str_split($valueStr);
        $valueStrLength = strlen($valueStr);
        $charStack = [];
        $stringInQuotes = false;
        $escapedChar = false;
        for($i = 0; $i < $valueStrLength; $i++){
            $char = $valueChars[$i];
            
            if($stringInQuotes){
                if($escapedChar){
                    $charStack[] = $char;
                    $escapedChar = false;
                }else if ($char == '"'){
                    // end the string
                    $values[] = join('', $charStack);
                    $charStack = [];
                    $stringInQuotes = false;
                }else if ($char ==  '\\'){
                    $escapedChar = true;
                }else{
                    $charStack[] = $char;
                }
            }else if ($char ==  '"'){
                if(count($charStack) > 0) throw new \Exception('Parse Error');
                $stringInQuotes = true;
            }else if ($char == ',' || $char == ' '){
                if(count($charStack) > 0){
                    $values[] = join('', $charStack);
                    $charStack = [];
                }
            }else if ($valueIsInBracket && $char == ')'){
                if(count($charStack) > 0){
                    $values[] = join('', $charStack);
                    $charStack = [];
                    break;
                }
            }else{
                $charStack[] = $char;
            }
        }
        if(count($charStack) > 0){
            $values[] = join('', $charStack);
            $charStack = [];
        }
        
        foreach ($values as $i => $value){
            if(is_numeric($value)){
                $values[$i] = 0 + $value;
            }
            
            if(is_string($value) && strtoupper($value) == "TRUE"){
                $values[$i] = true;
            }
            
            if(is_string($value) && strtoupper($value) == "FALSE"){
                $values[$i] = false;
            }
        }
        
        if(!$valueIsInBracket && count($values) ==  1){
            $values = $values[0];
        }
        
        return [
            "keys" => $keys,
            "values" => $values
        ];
        
    }

}


?>