<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 25.02.20
 * Time: 18:08
 */

namespace somov\common\classInfo;

use somov\common\helpers\ArrayHelper;

/**
 * Class Method
 * @package somov\common\classInfo
 */
class Method extends ReflectionData implements ClassInfoDataMethodInterface
{
    /**
     * @var
     */
    private $_static;

    /**
     * @var \ReflectionParameter[]
     */
    private $_reflectionParameter;

    /**
     * @var mixed
     */
    private $_returnType;

    /**
     * @param \ReflectionMethod $reflection
     */
    protected function apply($reflection)
    {
        $this->_static = $reflection->isStatic();
        $this->_reflectionParameter = $reflection->getParameters();
        $this->_returnType = $reflection->getReturnType();

        parent::apply($reflection);
    }

    /**
     * @return MethodParameterType[]
     */
    public function getParameters()
    {
        return $this->getCompositionFromFactory(function () {
            $data = ArrayHelper::map($this->_reflectionParameter, 'name', function ($p) {
                /** @var $p \ReflectionParameter */
                return new MethodParameterType([
                    'type' => $this->getParameterType($p),
                    'default' => $p->getDefaultValue(),
                    'position' => $p->getPosition(),
                    'name' => $p->getName()
                ]);
            });
            unset($this->_reflectionParameter);
            return $data;
        }, MethodParameterType::class);
    }

    /**
     * @param \ReflectionParameter $parameter
     * @return mixed
     */
    private function getParameterType(\ReflectionParameter $parameter)
    {
        $type = $parameter->getType();

        if (isset($type)) {
            return $type;
        }
        return $this->getTypeFromAnnotations('param.' . $parameter->getPosition());
    }

    /**
     * @return ParameterType
     */
    public function getReturnType()
    {

        $type = (isset($this->_returnType))  ? $this->_returnType : $this->getTypeFromAnnotations('return.0');

        return $this->getComposition(ParameterType::class, ['type'=>$type]);
    }

    /**
     * @param string $name
     * @return boolean
     */
    public function hasParameter($name)
    {
        return ArrayHelper::getValue($this->getParameters(), $name, false) instanceof MethodParameterType;
    }
}