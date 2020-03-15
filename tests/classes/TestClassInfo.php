<?php

namespace mtest\classes;


use phpDocumentor\Reflection\Types\Integer;

/**
 * Class TestComponent
 * @package mtest\classes
 *
 * @property string $annotatedClassProperty
 * @property string $annotatedClassProperty2
 * @property-read string $annotatedReadOnly
 * 
 * @method string doTestJob(string $arg1 = '2344', $arg2 = 222)
 * @method string doTestJob2($arg4,string $arg1='klk', $arg2 = 222, boolean $eee= false, $arg5="fdsfds")
 * 
 */
class TestClassInfo extends TestComponent
{
    /**
     * test
     */
    CONST TEST = 'test';

    /**
     * @var string
     */
    public $testProperty = 'test';

    /**
     * @var integer
     */
    public $testIntegerProperty;

    /**
     * @var boolean
     */
    protected $testBooleanProperty = true;


    /**
     * @param string $param1
     * @test data
     * @return string
     */
    public function testPublicMethod($param1 = 'test')
    {

    }

    protected function testProtectedMethod()
    {

    }

    private function testPrivateMethod()
    {

    }


    public static function testStatic()
    {

    }


    /**
     * @return string
     */
    public function getAnnotatedClassProperty()
    {
        return 'test';
    }

    public function __call($name, $params)
    {
        if ($name === 'doTestJob'){
            return 'test';
        }
    }

}