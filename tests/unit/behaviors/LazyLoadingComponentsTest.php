<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 08.02.19
 * Time: 15:08
 */

class LazyLoadingComponentsTest extends Codeception\TestCase\Test
{


    protected function getApp()
    {

        $app = Yii::$app;
        if (!$app->getBehavior('lazy')) {
            $app->attachBehavior('lazy', [
                'class' => \somov\common\behaviors\LazyLoadingComponents::class,
                'configDirectory' => '@mtest/files/lazyConfigs'
            ]);
        }
        return $app;
    }

    public function testLoad()
    {
        $app = $this->getApp();

        /** @var \mtest\classes\TestComponent $component */
        $component = $app->testComponent;

        $this->assertInstanceOf(\mtest\classes\TestComponent::class, $component);
        $this->assertSame('Hello', $component->testProperty);

    }

}