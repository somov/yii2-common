<?php
/**
 *
 * User: develop
 * Date: 14.09.2018
 */

namespace somov\common\helpers;


use yii\helpers\BaseArrayHelper;
use yii\helpers\StringHelper;

/**
 * Class ArrayHelper
 * @package somov\common\helpers
 */
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
                        $v = trim((string)$v);
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

    /**
     * @param array $array
     * @param null|string|array|callable $key
     * @param null|string|array|callable $group
     * @return array
     */
    public static function reIndex(array $array, $key = null, $group = null)
    {
        $result = [];
        foreach ($array as $item) {
            $result = array_merge($result, array_values($item));
        }
        if (isset($key)) {
            return self::index($result, $key, $group);
        }
        return $result;
    }

    /**
     * @param array $array
     * @param array $names
     * @param bool $keepKeys
     * @return array
     */
    public static function getColumns(array $array, array $names, $keepKeys = false)
    {

        return array_map(function ($array) use ($names) {
            $result = [];
            foreach ($names as $name => $columnName) {
                $name = is_numeric($name) ? $columnName : $name;

                $result[$name] = static::getValue($array, $columnName);
            }
            return $result;
        }, $keepKeys ? $array : array_values($array));
    }


    /**
     * @param array $array
     * @param bool $keepZeroValues
     */
    public static function unsetNotEmptyRecursive(array &$array, $keepZeroValues = true)
    {
        foreach ($array as $key => &$item) {
            if (is_array($item)) {
                static::unsetNotEmptyRecursive($item);
            }
            if (empty($item)) {
                if ($keepZeroValues && $item == '0') {
                    continue;
                }
                unset($array[$key]);
            }
        }
    }

    /**
     * @param array $data
     * @param int $length
     * @return array
     */
    public static function mix(array $data, $length = null)
    {
        $result = [];
        $n = isset($length) ? $length : count($data);

        $f = 1;

        for ($i = 1; $i <= $n; $i++) $f = $f * $i;

        if ($f === 1) {
            return [$data];
        }

        $first = null;
        for ($i = 0; $i < $f; $i++) {
            $pos = $i % ($n - 1);
            if ($pos == 0) {
                $first = array_shift($data);
            }
            $result[$i] = [];
            for ($j = 0; $j < $n - 1; $j++) {
                if ($j == $pos) {
                    $result[$i][] = $first;
                }
                $result[$i][] = $data[$j];
            }
            if ($pos == ($n - 2)) {
                $data[] = $first;
            }
        }

        return $result;
    }

    /**
     * @param $array
     * @param $length
     * @return array
     */
    public static function combination($array, $length = null)
    {
        $r = array();
        $n = count($array);
        $length = isset($length) ? $length : $n;

        if ($length <= 0 || $length > $n) {
            return $r;
        }

        for ($i = 0; $i < $n; $i++) {
            $t = array($array[$i]);
            if ($length == 1) {
                $r[] = $t;
            } else {
                $b = array_slice($array, $i + 1);
                $c = static::combination($b, $length - 1);
                foreach ($c as $v) {
                    $r[] = array_merge($t, $v);
                }
            }
        }

        return $r;
    }

}