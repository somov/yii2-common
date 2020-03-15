<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 25.02.20
 * Time: 22:56
 */

namespace somov\common\classInfo;


use Common\ModelReflection\DocBlock;
use somov\common\helpers\ArrayHelper;
use somov\common\traits\ContainerCompositions;
use yii\base\BaseObject;

/**
 * Class AnnotatedProperty
 * @package somov\common\classInfo
 */
class AnnotatedProperty extends BaseObject implements ClassInfoDataPropertyInterface
{

    use ContainerCompositions;

    /**
     * @var string
     */
    private $_type;

    /**
     * @var string
     */
    private $_name;

    /**
     * @var ClassInfo
     */
    private $_classInfo;

    /**
     * @return string
     */
    public function getDataType()
    {
        return 'annotateProperty';
    }

    /**
     * @return string
     */
    public function getVisibility()
    {
        return self::VISIBILITY_PUBLIC;
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

    /**
     * @return mixed
     */
    public function getValue()
    {
        if ($object = $this->getClassInfo()->getObject()) {
            if ($object->{$this->getName()}) {
                return $object->{$this->getName()};
            }
        }
        return null;
    }

    /**
     * @return ParameterType
     */
    public function getType()
    {
        return $this->getComposition(ParameterType::class, ['type' => $this->_type]);
    }

    /**
     * @param ClassInfo $classInfo
     * @return ClassInfoDataPropertyInterface[]
     */
    public static function parse(ClassInfo $classInfo)
    {

        $properties = [];

        foreach (ArrayHelper::filter($classInfo->getAnnotationsClassDocBlock()->getAnnotations(), [
            'property', 'property-read', 'property-write'
        ]) as $items) {
            $properties = array_merge($properties, $items);
        }

        return array_filter(array_map(function ($string) use ($classInfo) {

            $parts = explode(' ', $string);
            if (count($parts) > 1) {
                $instance = new self();
                $instance->_type = trim($parts[0]);
                $instance->_name = ltrim(end($parts), '$');
                $instance->_classInfo = $classInfo;
                return $instance;
            }
            return false;

        }, $properties));

    }

    /**
     * @return DocBlock
     */
    public function getDocBlock()
    {
        return $this->getComposition(DocBlock::class);
    }
}