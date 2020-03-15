<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 25.02.20
 * Time: 18:08
 */

namespace somov\common\classInfo;

use Common\ModelReflection\DocBlock;
use somov\common\helpers\ArrayHelper;
use somov\common\traits\ContainerCompositions;
use yii\base\BaseObject;

/**
 * Class Method
 * @package somov\common\classInfo
 */
class AnnotatedMethod extends BaseObject implements ClassInfoDataMethodInterface
{
    use ContainerCompositions;

    /**
     * @var mixed
     */
    private $_returnType;

    /**
     * @var string
     */
    private $_name;

    /**
     * @var string
     */
    private $_paramsRaw;

    /**
     * @var ClassInfo
     */
    private $_classInfo;

    /**
     * @param ClassInfo $classInfo
     * @return ClassInfoDataMethodInterface[]
     */
    public static function parse(ClassInfo $classInfo)
    {
        $methods = [];
        foreach ($classInfo->getAnnotationsClassDocBlock()->getAnnotation('method') as $docComment) {
            if (preg_match("/(?'return'\w+|)\s+(?'name'\w+)\s*\((?'argc'.*?)\)/", $docComment, $m)) {
                $instance = $methods[] = new self();
                $instance->_classInfo = $classInfo;
                $instance->_name = $m['name'];
                $instance->_paramsRaw = $m['argc'];
                $instance->_returnType = $m['return'];
            }
        }
        return $methods;
    }


    /**
     * @return ParameterType
     */
    public function getReturnType()
    {
        return $this->_returnType;

    }

    /**
     * @return string
     */
    public function getDataType()
    {
        return 'annotatedMethod';
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
     * @return DocBlock
     */
    public function getDocBlock()
    {
        return new DocBlock();
    }

    /**
     * @return MethodParameterType[]
     */
    public function getParameters()
    {
        return $this->getCompositionFromFactory(function () {
            $paramsRaw = $this->_paramsRaw;
            unset($this->_paramsRaw);
            /**
             * https://regex101.com/r/CoyDdB/5/tests
             */
            $pattern = <<<REGEXP
/(?'type'[\w|\[\]\/]+)\s*\\$(?'name'\w+)\s*(=\s*(?'default'[\w\'\\"]+)|)/
REGEXP;

            if (preg_match_all($pattern, $paramsRaw, $matches, PREG_SPLIT_DELIM_CAPTURE)) {
                $params = array_map(function ($match, $position) {
                    return new MethodParameterType([
                        'name' => $match['name'],
                        'type' => $match['type'],
                        'position' => $position,
                        'default' => ArrayHelper::getValue($match, 'default')
                    ]);

                }, $matches, array_keys($matches));

                return ArrayHelper::index($params, 'name');
            }

        }, MethodParameterType::class);
    }


}