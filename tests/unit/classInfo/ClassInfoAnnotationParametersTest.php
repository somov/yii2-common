<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 24.02.20
 * Time: 14:23
 */

use somov\common\classInfo\ClassInfo;
use somov\common\classInfo\ClassInfoDataInterface;


class ClassInfoAnnotationParametersTest extends PHPUnit_Framework_TestCase
{

    public function testInfo()
    {

        $info = new ClassInfo(null, '@mtest/classes/TestClassInfoFile.php', [
            'includeAnnotatedProperties' => true,
            'includeAnnotatedMethods' => true
        ]);

        $properties = $info->getProperties(ClassInfoDataInterface::VISIBILITY_PUBLIC);
        /** @var \somov\common\classInfo\ParameterType $type */
        $type = $properties['booleanProp']->getType();

        self::assertSame('boolean', $type->getType());

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
