<?php
/**
 *
 * User: develop
 * Date: 04.07.2018
 */

namespace mtest\traits;

use Codeception\TestCase\Test;
use somov\common\components\ProcessRunner;
use somov\common\process\LsProcess;
use somov\common\traits\ContainerCompositions;
use yii\base\Model;


class ContainerCompositionsTest extends Test
{
    use ContainerCompositions;

    public function testComposition()
    {
        $test = $this->getComposition(Model::class, [
            'scenario' => 'test'
        ]);
        $this->assertInstanceOf(Model::class, $test);
        $this->assertEquals('test', $test->scenario);
        $this->assertSame($this->getComposition(Model::class), $test);
    }

    public function testCompositionFromFactory()
    {
        $test1 = $this->getCompositionFromFactory([ProcessRunner::class, 'exec'],
            LsProcess::class,
            ['detail' => false]
        );

        $test2 = $this->getCompositionFromFactory([ProcessRunner::class, 'exec'], LsProcess::class);

        $this->assertSame($test1, $test2);

    }
}