<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 25.02.20
 * Time: 22:39
 */

namespace somov\common\classInfo;


/**
 * Interface ReflectionDataPropertyInterface
 * @package somov\common\classInfo
 */
interface ClassInfoDataPropertyInterface extends ClassInfoDataInterface
{
    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @return ParameterType
     */
    public function getType();
}