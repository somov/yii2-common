<?php
/**
 *
 * User: develop
 * Date: 14.09.2018
 */

namespace somov\common\classes;


use yii\helpers\BaseArrayHelper;

class ArrayHelper extends BaseArrayHelper
{

    /**
     * @param $array
     * @param string $afterItem
     * @param array$items
     */
    public static function insertAfter(&$array, $afterItem, $items)
    {
        $pos = (int)array_search($afterItem, array_keys($array)) + 1;
        $array = array_merge(
            array_slice($array, 0, $pos),
            $items,
            array_slice($array, $pos)
        );
    }

    /** Поиск по много мерному массиву
     * возвращает  ссылку на найденный  элемент
     * @param $needle
     * @param $haystack
     * @return mixed
     */
    public static function &recursiveArrayReferenceSearch($needle, &$haystack)
    {
        foreach ($haystack as $key => &$value) {
            if ($value === $needle) {
                return $haystack;
            }
            if (is_array($value)) {
                $r = &self::recursiveArrayReferenceSearch($needle, $value);
                if (!empty($r)) {
                    return $r;
                }
            }
        }
        $empty = '';

        return $empty;

    }

    /**
     * Сравнивает 2 многомерных ассоциативных массивов и возвращает отличие
     * @param $a
     * @param $b
     * @return array
     */
    public static function multiArrayDiff($a, $b)
    {
        $return = array_diff_key($b, $a);

        foreach (array_diff_key($a, $b) as $k => $c) {
            unset($a[$k]);
        }

        foreach ($a as $k => $c) {
            if ($r = array_merge(array_diff($b[$k], $a[$k]))) {
                $return[$k] = $r;
            }
        }

        return $return;
    }



}