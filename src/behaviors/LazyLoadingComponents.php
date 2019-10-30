<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 08.02.19
 * Time: 15:07
 */

namespace somov\common\behaviors;


use somov\common\classes\ArrayHelper;
use somov\common\classes\ConfigurationArrayFile;
use yii\base\Behavior;
use yii\base\Module;

/**
 * Отложено загружает компоненты
 * если найден файл конфигурации в каталоге из свойства $configDirectory
 * Имя файла должно быть идентично с id компонента
 *
 * Class LazyLoadingComponents
 * @package somov\common\behaviors
 * @property array $configFileOptions
 */
class LazyLoadingComponents extends Behavior
{
    /**
     * @var Module
     */
    public $owner;

    /** Альяс каталога конфигураций
     * @var string
     */
    public $configDirectory = '@app/configs/components';

    /**
     * @var array
     */
    private $_configFileOptions = [
        'class' => ConfigurationArrayFile::class
    ];

    /**
     * @param $options
     */
    public function setConfigFileOptions($options)
    {
        $this->_configFileOptions = ArrayHelper::merge($this->_configFileOptions, $options);
    }

    /**
     * @return array
     */
    public function getConfigFileOptions()
    {
        return $this->_configFileOptions;
    }

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

        $config = \Yii::createObject($this->_configFileOptions, [$fileName]);


        $this->owner->set($name, $config->asArray());

        return true;
    }

    /**
     * Переотравляет __get на экземпляр $owner
     * @param string $name
     * @return mixed|object|null
     * @throws \yii\base\InvalidConfigException
     */
    public function __get($name)
    {
        if ($this->owner->has($name)) {
            return $this->owner->get($name);
        }
        return parent::__get($name);
    }
}