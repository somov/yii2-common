<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 24.02.20
 * Time: 14:20
 */

namespace somov\common\classInfo;


use Common\ModelReflection\DocBlock;
use somov\common\helpers\ArrayHelper;
use yii\base\BaseObject;

/**
 * Class ClassInfo
 * @package somov\common\classes
 */
class ClassInfo extends BaseObject
{

    /**
     * @var bool
     */
    public $includeAnnotatedProperties = false;

    /**
     * @var bool
     */
    public $includeAnnotatedMethods = false;

    /**
     * @var bool
     */
    public $processParents = false;

    /**
     * @var string
     */
    private $_fileName;

    /**
     * @var string
     */
    private $_class;

    /**
     * @var string
     */
    private $_nameSpace;

    /**
     * @var \ReflectionClass
     */
    private $_reflectionClass;

    /**
     * @var object
     */
    private $_object;


    /**
     * ClassInfo constructor.
     * @param string|object $class
     * @param string $fileName
     * @param array $config
     */
    public function __construct($class = null, $fileName = null, array $config = [])
    {
        if (isset($fileName)) {
            $this->_fileName = \Yii::getAlias($fileName, false);
            if (!file_exists($this->_fileName)) {
                throw  new  \RuntimeException("File $fileName not found");
            }
            $this->parseContent();
        } else if (is_object($class)) {
            $this->_object = $class;
            $this->_class = get_class($class);
        } else {
            if (empty($class)) {
                throw new \RuntimeException('Class or file name required ');
            }
            $this->_class = $class;
        }


        parent::__construct($config);
    }

    /**
     * Initialization
     */
    public function init()
    {
        if ($class = $this->getClass()) {
            $this->_reflectionClass = new \ReflectionClass($class);
            $this->_nameSpace = $this->_reflectionClass->getNamespaceName();
            $this->_class = $this->_reflectionClass->getShortName();
        }
    }


    /**
     * @return bool
     */
    public function hasClass()
    {
        return !(empty($this->_class));
    }

    /**
     * @return string
     */
    public function getFileName()
    {
        if ($this->hasClass()) {
            return $this->reflectionClass()->getFileName();
        }
        return $this->_fileName;
    }

    /**
     * @return string|bool
     */
    public function getClass()
    {
        if (!$this->hasClass()) {
            return false;
        }
        return "$this->_nameSpace\\$this->_class";
    }

    /**
     * @return mixed
     */
    public function getNameSpace()
    {
        return $this->_nameSpace;
    }

    /**
     * @return DocBlock
     *
     */
    public function getAnnotationsClassDocBlock()
    {
        return $this->getOrSetInfo('annotations', function () {
            return (new DocBlock($this->reflectionClass()->getDocComment()));
        });
    }

    /**
     * @param $name
     * @param bool $first
     * @param mixed $default
     * @param bool $asType
     * @param int $maxParentLevel
     * @return bool|array|string|ParameterType|ParameterType[]
     */
    public function getClassAnnotations($name, $first = false, $default = null, $asType = false, &$maxParentLevel = 0)
    {
        if ($block = $this->getAnnotationsClassDocBlock()) {
            $value = $this->getAnnotationsFromDocBlock($block,
                $name, $default, $first, $asType);

            if (empty($value) && $maxParentLevel > 0) {
                --$maxParentLevel;
                $value = (new static($this->reflectionClass()->getParentClass()->name))
                    ->getClassAnnotations($name, $first, $default, $asType, $maxParentLevel);
            }

            return $value;
        }

        return $default;
    }

    /**
     * @param string|null $visibility
     * @return ClassInfoDataInterface[]
     */
    public function getConstants($visibility = null)
    {
        return $this->sectionItems('reflectionConstants', $visibility);
    }

    /**
     * @param string $name
     * @return Constant|boolean
     */
    public function getConstant($name)
    {
        return ArrayHelper::getValue($this->getConstants(), $name, false);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isConstantExists($name)
    {
        return $this->getConstant($name) == false;
    }


    /**
     * @param string |null $visibility
     * @return ClassInfoDataInterface[]
     */
    public function getProperties($visibility = null)
    {
        return $this->sectionItems('properties', $visibility);
    }

    /**
     * @param $name
     * @return ClassInfoDataPropertyInterface
     */
    public function getProperty($name)
    {
        return ArrayHelper::getValue($this->getProperties(), $name, false);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isPropertyExists($name)
    {
        return $this->getProperty($name) !== false;
    }

    /**
     * @param string $propertyName
     * @param string $annotationName
     * @param bool $first
     * @param mixed $default
     * @param bool $asType
     * @return bool|array|string|ParameterType|ParameterType[]
     */
    public function getPropertyAnnotations($propertyName, $annotationName, $first = false, $default = null, $asType = false)
    {
        if ($this->isPropertyExists($propertyName)) {
            return $this->getAnnotationsFromDocBlock($this->getProperty($propertyName)->getDocBlock(),
                $annotationName, $default, $first, $asType);
        }
        return $default;
    }


    /**
     * @param string|null $visibility
     * @return ClassInfoDataMethodInterface[]
     */
    public function getMethods($visibility = null)
    {
        return $this->sectionItems('methods', $visibility);
    }

    /**
     * @param string $name
     * @return Method|bool
     */
    public function getMethod($name)
    {
        return ArrayHelper::getValue($this->getMethods(), $name, false);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isMethodExists($name)
    {
        return $this->getMethod($name) !== false;
    }

    /**
     * @param string $methodName
     * @param string $annotationName
     * @param bool $first
     * @param mixed $default
     * @return array|bool|string|null
     */
    public function getMethodAnnotations($methodName, $annotationName, $first = false, $default = null)
    {
        if ($this->isPropertyExists($methodName)) {
            return $this->getAnnotationsFromDocBlock($this->getMethod($methodName)->getDocBlock(), $annotationName, $default, $first);
        }
        return $default;
    }

    /**
     * @param string $method
     * @param string $parameter
     * @return bool
     */
    public function isMethodHasParameter($method, $parameter)
    {
        if ($method = $this->getMethod($method)) {
            return ArrayHelper::getValue($method->getParameters(), $parameter, false) instanceof MethodParameterType;
        }
        return false;
    }


    /**
     * @return object
     */
    public function getObject()
    {
        return $this->_object;
    }

    /**
     * @param string $sectionName
     * @param $visibility
     * @return array
     */
    private function sectionItems($sectionName, $visibility)
    {

        $items = $this->getOrSetInfo($sectionName, function () use ($sectionName) {
            $sectionName = ucfirst($sectionName);
            $raw = call_user_func([$this->reflectionClass(), 'get' . $sectionName]);

            if (!$this->processParents) {
                $raw = ArrayHelper::index($raw, 'name', ['class']);
                $raw = ArrayHelper::getValue($raw, rtrim($this->getClass(), '\\'), []);
            }

            $items = ArrayHelper::map($raw, 'name', function ($r) {
                return ReflectionData::getData($this, $r);
            });

            $method = 'annotated' . $sectionName;

            if ($this->hasMethod($method)) {
                $items += ArrayHelper::index(call_user_func([$this, $method]), 'name');
            }

            return $items;
        });

        if (isset($visibility)) {
            $items = ArrayHelper::index($items, 'name', 'visibility');
            return ArrayHelper::getValue($items, $visibility, []);
        }

        return $items;
    }


    /**
     * @return ClassInfoDataPropertyInterface[]
     */
    protected function annotatedProperties()
    {
        if ($this->includeAnnotatedProperties) {
            return AnnotatedProperty::parse($this);
        }
        return [];
    }


    /**
     * @return ClassInfoDataMethodInterface[]
     */
    protected function annotatedMethods()
    {
        if ($this->includeAnnotatedMethods) {
            return AnnotatedMethod::parse($this);
        }
        return [];
    }

    /**
     * @var array
     */
    private static $_info = [];

    /**
     * @param string $section
     * @param callable $setter
     * @return mixed
     */
    protected function getOrSetInfo($section, $setter)
    {
        if (!$this->hasClass()) {
            return [];
        }

        $section = $this->getClass() . '.' . $section;

        if ($info = ArrayHelper::getValue(self::$_info, $section, false)) {
            return $info;
        }

        $value = call_user_func($setter);
        ArrayHelper::setValue(self::$_info, $section, $value);

        return $value;
    }

    /**
     * @return \ReflectionClass
     */
    protected function reflectionClass()
    {
        return $this->_reflectionClass;
    }

    /**
     * Parse file content
     */
    private function parseContent()
    {
        if (!preg_match("/(namespace\s+(?'ns'.+?)|);.+?class\s(?'c'\w+)\s/s", file_get_contents($this->getFileName()), $m)) {
            return;
        }

        $this->_nameSpace = $m['ns'];
        $this->_class = $m['c'];

    }

    /**
     * @param DocBlock $docBlock
     * @param string $name
     * @param mixed $default
     * @param bool $first
     * @param bool $asType
     * @return array|bool|string
     */
    protected function getAnnotationsFromDocBlock(DocBlock $docBlock, $name, $default, $first = false, $asType = false)
    {
        if (null === $default) {
            $default = $first ? false : [];
        }

        $value = $default;

        if ($docBlock->hasAnnotation($name)) {
            $value = $first ? $docBlock->getFirstAnnotation($name) : $docBlock->getAnnotation($name);;
        }

        if ($asType) {
            return $this->getOrSetInfo('types.' . md5(serialize([(array)$value, $name])), function () use ($value) {
                if (is_array($value)) {
                    return array_map(function ($v) {
                        return new ParameterType(['type' => $v]);
                    }, $value);
                }
                return new ParameterType(['type' => $value]);
            });
        }

        return $value;
    }
}