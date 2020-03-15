<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 25.02.20
 * Time: 18:08
 */

namespace somov\common\classInfo;

/**
 * Class Constant
 * @package somov\common\classInfo
 */
class Constant extends ReflectionData implements ClassInfoDataInterface
{
    /**
     * @var string
     */
    private $_value;
    

    /**
     * @param \ReflectionClassConstant $reflection
     */
    protected function apply($reflection)
    {
        parent::apply($reflection);
        $this->_value = $reflection->getValue();
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return gettype($this->getValue());
    }

}