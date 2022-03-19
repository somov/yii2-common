<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 12.07.21
 * Time: 15:12
 */

use somov\common\helpers\ArrayHelper;

class ArrayHelperMixTest extends \Codeception\Test\Unit
{

    public function testMix()
    {
        $array = [1, 2, 3, 4];

        //$mix = ArrayHelper::mix($array);
        $combinations = ArrayHelper::combination($array, 2);

        $this->assertSame([
            0 =>
                [
                    0 => 1,
                    1 => 2,
                ],
            1 =>
                [
                    0 => 1,
                    1 => 3,
                ],
            2 =>
                [
                    0 => 1,
                    1 => 4,
                ],
            3 =>
                [
                    0 => 2,
                    1 => 3,
                ],
            4 =>
                [
                    0 => 2,
                    1 => 4,
                ],
            5 =>
                [
                    0 => 3,
                    1 => 4,
                ],
        ], $combinations);

    }
}
