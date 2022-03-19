<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 12.07.21
 * Time: 15:12
 */

use somov\common\helpers\ArrayHelper;

class ArrayHelperTestCompileTextTest extends \Codeception\Test\Unit
{

    /**
     *
     */
    public function testCompileString()
    {
        $alias  = new \yii\base\DynamicModel([
            'type' => new \yii\base\DynamicModel([
                'title' => 'testTitle'
            ]),
            'title' => 'testAliasTitle'
        ]);

        $result = ArrayHelper::compileText('Videos [alias.type.title] [alias.title] [order] {page [page]} [siteName]', [
           'alias' => $alias,
           'page' => 3,
        ]);

        $this->assertSame('Videos testTitle testAliasTitle page 3 ', $result);

    }
}
