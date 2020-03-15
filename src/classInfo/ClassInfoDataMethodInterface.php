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
interface ClassInfoDataMethodInterface extends ClassInfoDataInterface
{
    /**
     * @return MethodParameterType[]
     */
    public function getParameters();


    /**
     * @return ParameterType
     */
    public function getReturnType();

  
}