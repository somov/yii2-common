<?php
/**
 *
 * User: develop
 * Date: 18.01.2019
 */

namespace somov\common\traits;

use yii\base\BaseObject;

/**
 * Trait DynamicProperties
 * @package somov\common\traits
 * динамические свойства объекта из массива $this->data
 * и если есть значение по умолканию из метода properties
 *
 * @method array properties
 */
trait DynamicProperties
{

    /**
     * @return array
     */
    private function dynamicProperties()
    {
        $dataName = $this->dynamicDataPropertyName();
        if (method_exists($this, 'properties')) {
            return $this->{$dataName} + $this->properties();
        }
        return $this->{$dataName};
    }

    /**
     * @return string
     */
    protected function dynamicDataPropertyName()
    {
        return 'data';
    }


    /**
     * {@inheritdoc}
     */
    public function __get($name)
    {
        $data = $this->dynamicProperties();
        if (array_key_exists($name, $data)) {
            return $data[$name];
        } else if ($this instanceof BaseObject) {
            return parent::__get($name);
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function __set($name, $value)
    {
        $dataName = $this->dynamicDataPropertyName();
        if (array_key_exists($name, $this->dynamicProperties())) {
            $this->{$dataName}[$name] = $value;
            return;
        } else if ($this instanceof BaseObject) {
            parent::__set($name, $value);
            return;
        }
        $this->{$name} = $value;
    }


    /**
     * {@inheritdoc}
     */
    public function __isset($name)
    {
        $data = $this->dynamicProperties();
        if (isset($data[$name])) {
            return true;
        }

        if ($this instanceof BaseObject) {
            return parent::__isset($name);
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function __unset($name)
    {
        $dataName = $this->dynamicDataPropertyName();
        if (array_key_exists($name, $this->dynamicProperties())) {
            $this->{$dataName}[$name] = null;
        }

        if ($this instanceof BaseObject) {
            parent::__unset($name);
        }

    }
}