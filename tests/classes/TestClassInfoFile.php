<?php

namespace mtest\classes;


/**
 * Class TestComponent
 * @package mtest\classes
 * @method string doTestJob(string $arg1 = '2344', $arg2 = 222)
 * @method string doTestJob2(\stdClass[] $object, $arg4,string $arg1='klk', $arg2 = 222, boolean $eee= false, $arg5="fdsfds")
 *
 * @test string
 * @test integer
 * @test boolean
 * 
 */
class TestClassInfoFile 
{
    /**
     * @var \yii\base\BaseObject[]
     */
    public $test1;

    /**
     * @var boolean
     */
    public $booleanProp = '1';

}