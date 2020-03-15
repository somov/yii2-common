<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 25.02.20
 * Time: 22:09
 */

namespace somov\common\classInfo;


use Common\ModelReflection\DocBlock;
use yii\base\Configurable;

/**
 * Interface ReflectionDataInterface
 * @package somov\common\classInfo
 * @property string name
 * @property string visibility
 */
interface ClassInfoDataInterface extends Configurable
{
    const VISIBILITY_PUBLIC = 'public';
    const VISIBILITY_PRIVATE = 'private';
    const VISIBILITY_PROTECTED = 'protected';

    /**
     * @return string
     */
    public function getDataType();

    /**
     * @return string
     */
    public function getVisibility();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return ClassInfo
     */
    public function getClassInfo();

    /**
     * @return DocBlock
     */
    public function getDocBlock();

}