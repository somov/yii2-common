<?php
/**
 *
 * User: develop
 * Date: 04.07.2018
 */

namespace somov\common\traits;


use yii\helpers\ArrayHelper;

trait ContainerCompositions
{
    private $_cHolder = [];


    /**
     * @param string $class
     * @param array $config проверяется зависимости конструктора создаваемого экземпляра из массива конфигурации
     * если объект ранее создавался возвращается экземпляр из массива {$_cHolder}
     * @return object|mixed
     */
    protected function getComposition($class, $config = [])
    {
        if (isset($this->_cHolder[$class])) {
            return $this->_cHolder[$class];
        }

        $reflection = new \ReflectionClass($class);
        $params = $reflection->getConstructor()->getParameters();

        $constructorParams = [];
        foreach ($params as $param) {
            if ($param->getClass() !== null) {
                if ($dependency = ArrayHelper::remove($config, $param->getClass()->name)) {
                    $constructorParams[$param->name] = $dependency;
                }
            } else {
                if ($dependency = ArrayHelper::remove($config, $param->name)) {
                    $constructorParams[$param->name] = $dependency;
                } else {
                    $constructorParams[$param->name] = null;
                }
            }
        }

        if (array_key_exists('config', $constructorParams)) {
            $constructorParams['config'] = $config;
        }

        $this->_cHolder[$class] = $reflection->newInstanceArgs($constructorParams);

        return $this->_cHolder[$class];
    }


    /**
     * @param callable $factory
     * @param $class
     * @param array $config
     */
    protected function getCompositionFromFactory($factory, $class, $config = [])
    {
        if (isset($this->_cHolder[$class])) {
            return $this->_cHolder[$class];
        }

        $argument[0] = $class;

        if (!empty($config)) {
            $argument[0] = $config + ['class' => $class];
        }

        $this->_cHolder[$class] = call_user_func_array($factory, $argument);

        return $this->_cHolder[$class];

    }


    /**
     * @param string|array $config
     * @return object
     */
    protected function getCompositionYii($config)
    {

        if (is_array($config)) {
            $class = ArrayHelper::getValue($config, 'class');
        } else {
            $class = $config;
            $config = [
                'class' => $config
            ];
        }

        if (isset($this->_cHolder[$class])) {
            return $this->_cHolder[$class];
        }

        $this->_cHolder[$class] = \Yii::createObject($config);
        return $this->_cHolder[$class];

    }

}