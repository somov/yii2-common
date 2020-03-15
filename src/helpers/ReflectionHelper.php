<?php
/**
 *
 * User: develop
 * Date: 30.08.2018
 */

namespace somov\common\helpers;

use Common\ModelReflection\DocBlock;

/**
 * Class ReflectionHelper
 * @package somov\common\helpers
 *
 */
class ReflectionHelper
{
    /**
     * Парсет класс, извлекает namespace и class  
     * @param string $file
     * @param array $info
     * @return array
     */
    public static function initClassInfo($file, &$info)
    {
        $info = [];

        $file = \Yii::getAlias($file);

        if (!file_exists($file)) {
            return $info;
        }

        if (!preg_match('/namespace\s+(.+?);.+?class\s(\w+)\s/s', file_get_contents($file), $m)) {
            return $info;
        }

        $info['namespace'] = $m[1];
        $info['class'] = $m[1] . '\\' . $m[2];
        
        return $info;
    }

    /**
     * @param string $file
     * @param array|null $info
     * @return array
     * @throws \ReflectionException
     */
    public static function classInfo($file, array $info = null)
    {
        if (!isset($info)) {
            self::initClassInfo($file, $info);
        }

        $reflection = new \ReflectionClass($info['class']);

        $info['constants'] = $reflection->getConstants();
        $info['methods'] = $reflection->getMethods();
        $info['annotations'] = (new DocBlock($reflection->getDocComment()))->getAnnotations();
        foreach ($info['methods'] as &$method) {

            $method = [
                'name' => $method->name,
                'annotations' => (new DocBlock($method->getDocComment()))->getAnnotations(),
                'params' => array_map(function ($p) {
                    return [$p->name => $p->getType()];
                }, $method->getParameters())
            ];
        }
        return $info;
    }

}