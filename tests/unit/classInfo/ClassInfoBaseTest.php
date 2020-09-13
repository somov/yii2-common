<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 24.02.20
 * Time: 14:23
 */

use somov\common\classInfo\ClassInfo;


class ClassInfoBaseTest extends PHPUnit_Framework_TestCase
{


    public function testInfoWithParents()
    {


        $info = new ClassInfo('mtest\classes\TestClassInfo');
        $parentLevel = 2;
        $test1 =  $info->getClassAnnotations('testSub', true, null, false, $parentLevel);

        self::assertSame('test222', $test1);

    }

    public function testInfo()
    {
        $test = new \mtest\classes\TestClassInfo();
        $test->doTestJob('stre', 222);

        $info = new ClassInfo($test, null, [
            'includeAnnotatedProperties' => true,
            'includeAnnotatedMethods' => true
        ]);


        $a = $info->getConstants();
        $this->assertArrayHasKey('TEST', $a);

        $file = $info->getFileName();
        $this->assertContains('/tests/classes/TestClassInfo.php', $file);

        $a = $info->getProperties();

        $a = $info->getProperties(\somov\common\classInfo\Property::VISIBILITY_PROTECTED);
        $this->assertCount(1, $a);

        $property = $info->getProperty('annotatedClassProperty');

        $this->assertEquals('test', $property->getValue());
        $this->assertEquals('string', $property->getType());

        $this->assertTrue($property->getType()->isSimple());

        $r = $property->getDocBlock()->hasAnnotation('test');
        $this->assertFalse($r);

        $property = $info->getProperty('testBooleanProperty');
        $this->assertEquals('boolean', $property->getType());

        $method = $info->getMethod('testPublicMethod');
        $this->assertTrue($method->hasParameter('param1'));
        $this->assertEquals('string', $method->getReturnType());
        $this->assertTrue($info->isMethodHasParameter($method->name,'param1'));

        $r = $method->getReturnType()->normalizeType();

        $method = $info->getMethod('doTestJob');
        $params = $method->getParameters();
        $this->assertTrue($info->isMethodHasParameter($method->name,'arg1'));
        $this->assertEquals('string', $method->getReturnType());


        $method = $info->getMethod('doTestJob2');
        $params = $method->getParameters();
                

        $a = $info->getAnnotationsClassDocBlock();
        $this->assertArrayHasKey('package', $a->getAnnotations());

        $this->assertTrue($info->isMethodExists('testPrivateMethod'));

    }



}
