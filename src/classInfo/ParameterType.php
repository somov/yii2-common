<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 27.02.20
 * Time: 14:16
 */

namespace somov\common\classInfo;


use somov\common\helpers\ArrayHelper;
use yii\base\BaseObject;
use yii\helpers\StringHelper;

/**
 * Class Parameter
 * @package somov\common\classInfo
 */
class ParameterType extends BaseObject
{
    const MIXED_TYPE = 'mixed';

    /**
     * @var string
     */
    protected $_type;

    /**
     * Parameter constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        if (empty($this->_type)) {
            $this->_type = trim(ArrayHelper::remove($config, 'type'));
        }

        if (empty($this->_type)) {
            $this->_type = self::MIXED_TYPE;
        }

        parent::__construct($config);
    }


    /**
     * @return mixed|string
     */
    public function __toString()
    {
        return $this->getType();
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->_type;
    }


    /**
     * @return boolean
     */
    public function isArray()
    {
        if ($this->_type === 'array') {
            return true;
        }
        if ($this->isSimple()) {
            return false;
        }

        return StringHelper::endsWith($this->_type, '[]');
    }

    /**
     * @return bool
     */
    public function isSimple()
    {
        return array_key_exists($this->_type, $this->simpleTypes());
    }

    /**
     * @return bool|string
     */
    public function isObjectType()
    {
        if ($this->_type === 'object') {
            return true;
        }

        if ($this->isSimple()) {
            return false;
        }

        $class = $this->normalizeType();

        return class_exists($class);
    }

    /**
     * @return bool
     */
    public function isArrayOfObject()
    {
        return $this->isArray() && $this->isObjectType();
    }

    /**
     * @return string
     */
    public function normalizeType()
    {
        return rtrim($this->_type, '[]');
    }

    /**
     * @param string $type
     * @return bool
     */
    public function isInstanceOf($type)
    {
        if ($this->isObjectType()) {
            $class = $this->normalizeType();

            if (!class_exists($class)) {
                return false;
            }

            return in_array($type, class_implements($class)) || in_array($type, class_parents($class));
        }
        return false;
    }


    /**
     * @return array
     */
    protected function simpleTypes()
    {

        return [
            'array' => true,
            'boolean' => true,
            'bool' => true,
            'double' => true,
            'float' => true,
            'integer' => true,
            'int' => true,
            'null' => true,
            'numeric' => true,
            'object' => true,
            'real' => true,
            'resource' => true,
            'string' => true,
            'scalar' => true,
            'callable' => true
        ];
    }


}