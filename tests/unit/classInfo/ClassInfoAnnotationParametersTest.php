<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 24.02.20
 * Time: 14:23
 */

use somov\common\classInfo\ClassInfo;


class ClassInfoAnnotationParametersTest extends PHPUnit_Framework_TestCase
{

    public function testInfo()
    {

        $info = new ClassInfo(null, '@mtest/classes/TestClassInfoFile.php', [
            'includeAnnotatedProperties' => true,
            'includeAnnotatedMethods' => true
        ]);

        $parameters = $info->getClassAnnotations('test', false, [], true);
        $this->assertCount(3, $parameters);
        $this->assertInstanceOf(\somov\common\classInfo\ParameterType::class, $parameters[0]);

        $parameters  = $info->getMethod('doTestJob2')->getParameters();
        $this->assertTrue($parameters['object']->isArrayOfObject());

        $type = $info->getPropertyAnnotations('test1', 'var', true, false, true);
        $this->assertTrue($type->isArrayOfObject());

        $type = $info->getPropertyAnnotations('property', 'var', true, 'property-not-found', true);
        $this->assertSame('property-not-found', $type);


        
        
        




    }

}
