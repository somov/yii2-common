<?php

namespace mtest\classes;


/**
 * Class TestComponent
 * @package mtest\classes
 *
 * @testSub test222
 *
 * @property string testSubProperty 
 */
class TestComponent extends \yii\base\Component
{

    CONST TEST = 'test_const';

    /**
     * @var string
     */
    public $testProperty = 'test';

    /**
     * @var null
     */
    public $testVariable;


    /**
     */
    public function testPublicMethod()
    {

    }

    protected function testProtectedMethod()
    {

    }

    private function testPrivateMethod()
    {

    }


}