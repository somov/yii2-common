<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 27.02.20
 * Time: 14:01
 */

use somov\common\classInfo\MethodParameterType;

class MethodParameterTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        
        new MethodParameterType([
            'type'=> 'string',
            'name' => 'test',
            'position' => 0
        ]);
    }

}
