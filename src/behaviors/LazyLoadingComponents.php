<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 08.02.19
 * Time: 15:07
 */

namespace somov\common\behaviors;


use yii\base\Behavior;

/**
 * Отложено загружает компоненты
 * если найден файл конфигурации в каталоге из свойства $configDirectory
 * Имя файла должно быть идентично с id компонента
 *
 * Class LazyLoadingComponents
 * @package somov\common\behaviors
 */
class LazyLoadingComponents extends Behavior
{
    /**
     * @var \yii\base\Application
     */
    public $owner;

    /** Альяс каталога конфигураций
     * @var string
     */
    public $configDirectory = '@app/configs/components';

    /**
     * @param string $name
     * @param bool $checkVars
     * @return bool
     */
    public function canGetProperty($name, $checkVars = true)
    {
        return $this->load($name);
    }

    /**
     * Загрузка конфигураций компонента
     * @param string $name
     * @return bool
     */
    private function load($name)
    {
        $fileName = \Yii::getAlias($this->configDirectory . "/$name.php");

        if (!file_exists($fileName)) {
            return false;
        }

        $config = require $fileName;
        $this->owner->set($name, $config);

        return true;
    }

    /**
     * Перенаправляет __get на экземпляр приложения
     * @param string $name
     * @return mixed|object|null
     * @throws \yii\base\InvalidConfigException
     */
    public function __get($name)
    {
        return $this->owner->get($name);
    }
}