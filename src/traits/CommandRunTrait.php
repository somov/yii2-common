<?php
/**
 * Created by PhpStorm.
 * User: develop
 * Date: 17.01.2019
 * Time: 23:53
 */

namespace somov\common\traits;


use yii\base\Module;
use yii\console\Application;
use yii\console\Controller;

trait CommandRunTrait
{

    /** Запуск консольных команд  из web приложения
     * @param array $config
     * @param string $route
     * @param array|string $params
     * @param Module $module
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    protected function runCommand(array $config, $route = '', $params = '', $module = null)
    {
        $module = (isset($module)) ? $module : \Yii::$app;

        /** @var Controller $command */
        $command = \Yii::createObject($config, [
            'migrate',
            $module
        ]);

        $this->initConsole($module);

        ob_start();
        ob_implicit_flush(false);

        try {
            $command->run($route, (array) $params);
        } catch (\Exception $exception) {
            if (ob_get_level() > 0) {
                ob_end_clean();
            }
            throw $exception;
        }

        $out = ob_get_clean();
        return $out;

    }

    /**
     * @param Module $module
     * @throws \yii\base\InvalidConfigException
     */
    private function initConsole(Module $module)
    {
        if (defined('STDIN')) {
            return;
        }

        $beforeApp = \Yii::$app;

        (new Application([
            'id' => 'migrate',
            'basePath' => $module->basePath
        ]))->init();

        \Yii::$app = $beforeApp;

    }

}