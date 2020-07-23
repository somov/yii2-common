<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 23.07.20
 * Time: 17:46
 */

namespace mtest\traits;

use somov\common\traits\DynamicProperties;

/**
 * Class DynamicPropertiesTest
 * @package mtest\traits
 * 
 * @property  integer $pInData 
 * @property  integer $pInMethod
 */
class DynamicPropertiesTest extends \Codeception\Test\Unit
{
    use DynamicProperties;

    public $test = '';
    
    protected $data = [
        'pInData' => 1,
    ];

    /**
     * @return array
     */
    public function properties()
    {
        return [
            'pInMethod' => 3,
        ];
    }

    public function testProps()
    {
         
        $this->assertSame(1, $this->pInData);

        $this->pInData = 2;
        $this->assertSame(2, $this->pInData);

        $this->assertSame(3, $this->pInMethod);

        $this->pInMethod = 4;
        $this->assertSame(4, $this->pInMethod);

        $this->assertTrue(isset($this->pInData));
        $this->assertFalse(isset($this->pInData2));

        $this->assertTrue(isset($this->pInMethod));
        $this->assertFalse(isset($this->pInMethod2));

        unset($this->pInData);
        $this->assertNull($this->pInData);

        unset($this->pInMethod);
        $this->assertNull($this->pInMethod);
        


    }


}
