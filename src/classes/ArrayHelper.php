<?php
/**
 *
 * User: develop
 * Date: 14.09.2018
 */

namespace somov\common\classes;


use yii\helpers\BaseArrayHelper;
use yii\helpers\StringHelper;

class ArrayHelper extends BaseArrayHelper
{

    /**
     * @param $array
     * @param string $afterItem
     * @param array $items
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


    /** Преобразует строку с переменными в квадратных скобках в значение из параметров $params
     *
     * @param string $text
     * @param array $params
     * @param int $count
     * @param bool $isStripTags
     * @return string|string[]|null
     */
    public static function compileText($text, $params, &$count = 0, $isStripTags = true)
    {

        $text = preg_replace_callback('/\{(.*?)\}/m', function ($m)
        use ($text, $params, $isStripTags) {
            $tmp = self::compileText($m[1], $params, $count, $isStripTags);
            return ($count > 0) ? $tmp : '';
        }, $text);

        $value = preg_replace('/\s+/m', ' ', preg_replace_callback(
            '/\[\s*(\s*[\w.]+)(\s*\,\s*(\s*[\w]+\s*)(|(?:\,\s*)([\d]+)\s*)\s*|)\]/m',
            function ($m) use ($params, $isStripTags, &$count) {
                $key[] = $m[1];

                if (isset($key[3])) {
                    $key[] = $key[3];
                }

                $v = self::getValue($params, $key, '');

                if (!is_string($v)) {
                    try {
                        $v = (string)$v;
                    } catch (\Exception $exception) {
                        $v = '';
                    }
                }

                if (!empty($v)) {
                    if ($isStripTags) {
                        $v = strip_tags($v);
                    }
                    $count++;
                }

                if (!empty($m[3])) {
                    $v = StringHelper::truncate($v, $m[3], '...', null, !$isStripTags);
                }

                return $v;
            }, $text), -1);

        return $value;
    }

}