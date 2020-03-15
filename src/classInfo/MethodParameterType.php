<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 26.02.20
 * Time: 0:09
 */

namespace somov\common\classInfo;


/**
 * Class MethodParameter
 * @package somov\common\classInfo
 *
 * @property-read mixed $type
 * @property-read mixed $default;
 * @property-read integer $position
 * @property-read string $name
 */
class MethodParameterType extends ParameterType
{

    /**
     * @var mixed
     */
    private $_default;

    /**
     * @var integer
     */
    private $_position;

    /**
     * @var string
     */
    private $_name;

    /**
     * MethodParameter constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        foreach ($config as $item => $value) {
            $this->{'_' . $item} = $value;
            unset($config[$item]);
        }

        if (empty($this->_name) || $this->_position === null) {
            throw new \RuntimeException('Name and position required properties');
        }

        parent::__construct($config);
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $this->_default;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->_position;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }


}