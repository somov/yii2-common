<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 21.05.20
 * Time: 18:26
 */

namespace somov\common\helpers;

/**
 * Class StringHelper
 * @package somov\common\helpers
 */
class StringHelper extends \yii\helpers\BaseStringHelper
{

    /**
     * @param string $source
     * @param string $string
     * @param string $search
     * @param bool $after
     * @return string|false
     */
    public static function insertTo($source, $string, $search, $after = false)
    {
        if ($pos = strpos($source, $search)) {

            if ($after) {
                $pos += strlen($search);
            }

            return substr($source, 0, $pos) . $string . substr($source, $pos);
        }

        return false;
    }


    /**
     * @param $string
     * @param int $offset
     * @param string $encoding
     * @return string
     */
    public static function mbUcChar($string, $offset = 0, $encoding = 'UTF-8')
    {
        $offset = $offset === 0 ? 1 : $offset;

        if ($offset === 1) {
            return static::mb_ucfirst($string, $encoding);
        }
        $len = mb_strlen($string, $encoding);
        $first = mb_substr($string, 0, $offset, $encoding);
        $then = mb_substr($string, $offset, $len, $encoding);

        return $first . static::mb_ucfirst($then);
    }

    /**
     * Разбивает строка на части через разделитель
     * возвращает уникальный массив
     *
     * @param $string
     * @param int $minWordLength
     * @param string $delimiter
     * @param bool $trim
     * @param bool $ucfirst
     * @return array
     */
    public static function explodeWithMinLength($string, $minWordLength = 5, $delimiter = ' ', $trim = false, $ucfirst = false)
    {
        $string = mb_strtolower($string);

        if ($data = parent::explode($string, $delimiter, $trim, true)) {
            $data = array_unique(array_filter($data, function ($s) use ($minWordLength) {
                return mb_strlen($s) >= $minWordLength;
            }));
            if ($ucfirst) {
                return array_map('self::mb_ucfirst', $data);
            }
            return $data;
        }
        return [];
    }

    /**
     * @param $str
     * @return bool|int
     */
    public static function firstCharOFWordOffset($str)
    {
        if (preg_match('/[a-zA-Zа-яА-Я]/i', $str, $m, PREG_OFFSET_CAPTURE)) {
            return (int)$m[0][1];
        }
        return false;
    }

    /**
     * @param $str
     * @return bool|int
     */
    public static function isStartsWithUpper($str)
    {
        $offset = self::firstCharOFWordOffset($str);
        if ($offset !== false) {
            $chr = mb_substr($str, $offset, 1, "UTF-8");
            return mb_strtolower($chr, "UTF-8") != $chr;
        }

        return false;
    }

    /**
     * Разбивает строка на части через разделитель
     * строка формируется из объекта на основании указанных  атрибутов
     * возвращает уникальный массив
     * @param $object
     * @param array $attributes
     * @param int $minWordLength
     * @param array $options
     * @return array
     */
    public static function explodeWithMinLengthObject($object, array $attributes, $minWordLength = 5, $options = array())
    {
        $delimiter = ArrayHelper::getValue($options, 'delimiter', ' ');
        $trim = ArrayHelper::getValue($options, 'trim', false);
        $ucfirst = ArrayHelper::getValue($options, 'ucfirst', false);

        $string = implode($delimiter, array_map(function ($a) use ($delimiter, $object) {
            return ArrayHelper::getValue($object, $a);
        }, $attributes));

        return self::explodeWithMinLength($string, $minWordLength, $delimiter, $trim, $ucfirst);

    }
}