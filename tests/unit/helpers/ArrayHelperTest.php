<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 12.07.21
 * Time: 15:12
 */

use somov\common\helpers\ArrayHelper;

class ArrayHelperTest extends \Codeception\Test\Unit
{

    public function testMix()
    {
        $array = [1,2,3,4];

        //$mix = ArrayHelper::mix($array);
        $combintation = ArrayHelper::combination($array, 2);


    }
}
