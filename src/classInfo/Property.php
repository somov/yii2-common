<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 25.02.20
 * Time: 18:08
 */

namespace somov\common\classInfo;

/**
 * Class Property
 * @package somov\common\classInfo
 */
class Property extends ReflectionData implements ClassInfoDataPropertyInterface
{
    /**
     * @var $_value ;
     */
    private $_value;

    /**
     * @param \ReflectionProperty $reflection
     */
    protected function apply($reflection)
    {

        if ($object = $this->getClassInfo()->getObject()) {
            $reflection->setAccessible(true);
            $this->_value = $reflection->getValue($object);
        }

        parent::apply($reflection);
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * @return ParameterType
     */
    public function getType()
    {
        if ($v = $this->getValue()) {
            $type = gettype($v);

            if ($type === 'object') {
                $type = get_class($v);
            }

        } else {
            $type = $this->getTypeFromAnnotations('var.0');
        }

        return $this->getComposition(ParameterType::class, ['type' => $type]);
    }


}