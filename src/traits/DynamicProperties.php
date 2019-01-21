<?php
/**
 *
 * User: develop
 * Date: 18.01.2019
 */

namespace somov\common\traits;

/**
 * Trait DynamicProperties
 * @package somov\common\traits
 * динамические свойства объекта из массива $this->data
 */
trait DynamicProperties
{
    /**
     * {@inheritdoc}
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }

        return parent::__get($name);
    }

    /**
     * {@inheritdoc}
     */
    public function __set($name, $value)
    {
        if (array_key_exists($name, $this->data)) {
            $this->data[$name] = $value;
        } else {
            parent::__set($name, $value);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function __isset($name)
    {
        if (array_key_exists($name, $this->data)) {
            return isset($this->data[$name]);
        }

        return parent::__isset($name);
    }

    /**
     * {@inheritdoc}
     */
    public function __unset($name)
    {
        if (array_key_exists($name, $this->data)) {
            unset($this->data[$name]);
        } else {
            parent::__unset($name);
        }
    }
}