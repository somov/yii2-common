<?php
/**
 *
 * User: develop
 * Date: 09.11.2018
 */

namespace somov\common\helpers;


use yii\helpers\Json;

/**
 * Class JsonHelper
 * @package somov\common\helpers
 *
 */
class JsonHelper
{
    /** Преобразует в JavaScript массив объектов
     * @param array $data
     * @deprecated use Json::encode(array_values($data))
     * @return string
     */
    public static function toArrayObjects(array $data)
    {
        $items = [];
        foreach ($data as $array) {
            $items[] = Json::encode($array);
        };
        return '[' . implode(',', $items) . ']';
    }
}