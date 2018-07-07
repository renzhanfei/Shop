<?php
/**
 * Created by PhpStorm.
 * User: minhao
 * Date: 2015-12-04
 * Time: 18:03
 */

namespace Oasis\Mlib\Utils;

use voku\helper\UTF8;

class StringUtils
{
    /**
     * Chops down a string according to given max length, all characters beyond $maxLength is removed.
     *
     * This function is UTF8 compatible
     *
     * @param      $str
     * @param      $maxLength
     * @param bool $lengthUnitInByte
     *
     * @return string
     *
     */
    public static function stringChopdown($str, $maxLength, $lengthUnitInByte = false)
    {
        if ($lengthUnitInByte) {
            return substr($str, 0, $maxLength);
        }

        $str = UTF8::to_utf8($str);
        $len = UTF8::strlen($str);
        if ($len <= $maxLength) {
            return $str;
        }

        return UTF8::substr($str, 0, $maxLength);
    }

    public static function stringStartsWith($haystack, $needle)
    {
        // search backwards starting from haystack length characters from the end
        return
            $needle === ""
            || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }

    public static function stringEndsWith($haystack, $needle)
    {
        // search forward starting from end minus needle length characters
        return
            $needle === ""
            || (
                ($temp = strlen($haystack) - strlen($needle)) >= 0
                && strpos($haystack, $needle, $temp) !== false
            );
    }
}
