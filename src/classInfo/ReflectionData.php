<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 25.02.20
 * Time: 18:01
 */

namespace somov\common\classInfo;


use Common\ModelReflection\DocBlock;
use somov\common\helpers\ArrayHelper;
use somov\common\traits\ContainerCompositions;
use yii\base\BaseObject;
use yii\helpers\StringHelper;

/**
 * Class ReflectionData
 * @package somov\common\classInfo
 */
abstract class ReflectionData extends BaseObject implements ClassInfoDataInterface
{

    use ContainerCompositions;


    /**
     * @var string
     */
    private $_visibility;

    /**
     * @var string
     */
    private $_name;

    /**
     * @var string
     */
    private $_docComment;


    /**
     * @var ClassInfo
     */
    private $_classInfo;

    /**
     * @return array
     */
    private static function classMap()
    {
        return [
            'ReflectionClassConstant' => Constant::class,
            'ReflectionProperty' => Property::class,
            'ReflectionMethod' => Method::class
        ];
    }

    /**
     * @param \ReflectionMethod|\ReflectionProperty|\ReflectionClassConstant $reflection
     */
    protected function apply($reflection)
    {
        $this->_visibility = $this->reflectionVisible($reflection);
        $this->_name = $reflection->getName();
        $this->_docComment = $reflection->getDocComment();
    }

    /**
     * @param ClassInfo $info
     * @param \ReflectionMethod|\ReflectionProperty|\ReflectionClassConstant $reflection
     * @return self
     */
    public static function getData(ClassInfo $info, $reflection)
    {
        $class = ArrayHelper::getValue(self::classMap(), get_class($reflection));
        /** @var self $instance */
        $instance = new $class();
        $instance->_classInfo = $info;
        $instance->apply($reflection);
        return $instance;
    }

    /**
     * @param \ReflectionMethod|\ReflectionProperty|\ReflectionClassConstant $reflection
     * @return string
     */
    private function reflectionVisible($reflection)
    {
        /** @var \ReflectionMethod $e */
        if ($reflection->isPrivate()) {
            return ClassInfoDataInterface::VISIBILITY_PRIVATE;
        }

        if ($reflection->isProtected()) {
            return ClassInfoDataInterface::VISIBILITY_PROTECTED;
        }

        return ClassInfoDataInterface::VISIBILITY_PUBLIC;

    }

    /**
     * @return string
     */
    public function getDataType()
    {
        return lcfirst(StringHelper::basename(self::class));
    }

    /**
     * @return DocBlock
     */
    public function getDocBlock()
    {
        return $this->getCompositionFromFactory(function () {
            $block = new DocBlock($this->_docComment);
            unset($this->_docComment);
            return $block;
        }, DocBlock::class);
    }

    /**
     * @param string $typeKey
     * @return null
     */
    protected function getTypeFromAnnotations($typeKey)
    {
        if ($type = ArrayHelper::getValue($this->getDocBlock()->getAnnotations(), $typeKey, false)) {
            $type = explode(' ', $type);
            return trim($type[0]);
        }
        return null;
    }

    /**
     * @return string
     */
    public function getVisibility()
    {
        return $this->_visibility;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @return ClassInfo
     */
    public function getClassInfo()
    {
        return $this->_classInfo;
    }

}