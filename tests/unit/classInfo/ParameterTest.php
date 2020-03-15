<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 27.02.20
 * Time: 14:18
 */

namespace somov\common\classInfo;


class ParameterTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     */
    public function testParameter()
    {

        $type = new ParameterType(['type' => 'string']);
        $this->assertTrue($type->isSimple());
        $this->assertEquals('string', $type);

        $type = new ParameterType(['type' => 'array']);
        $this->assertTrue($type->isArray());


        $type = new ParameterType(['type' => 'stdClass']);
        $this->assertTrue($type->isObjectType());


        $type = new ParameterType(['type' => 'stdClass[]']);
        $this->assertTrue($type->isArray());
        $this->assertTrue($type->isArrayOfObject());

        $type = new ParameterType(['type' => 'stdClassUnknown[]']);
        $this->assertTrue($type->isArray());
        $this->assertFalse($type->isSimple());
        $this->assertFalse($type->isArrayOfObject());
        $this->assertSame('stdClassUnknown', $type->normalizeType());



    }

}
