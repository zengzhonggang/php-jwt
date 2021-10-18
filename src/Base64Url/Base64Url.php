<?php
/**
 * Created by PhpStorm.
 * User: zzg
 * Date: 2020-07-20
 * Time: 01:59
 */

namespace ZZG\Base64Url;


class Base64Url
{
    public static function encode($array){
        if (is_array($array)) {
            $array = self::_jsonEncode($array);
        }
        return self::_urlSafeEncode(base64_encode($array));
    }

    public static function decode($string) {
        return self::_jsonDecode(base64_decode(self::_urlSafeDecode($string)));
    }

    private static function _jsonEncode($array) {
        return json_encode($array);
    }

    private static function _jsonDecode($string) {
        return json_decode($string,true);
    }

    private static function _urlSafeEncode($string) {
        return str_replace(array('+','/','='),array('-','_',''),$string);
    }

    private static function _urlSafeDecode($string) {
        $reString = str_replace(array('-','_'),array('+','/'),$string);
        $mod4 = strlen($reString) % 4;
        if ($mod4) {
            $reString .= substr('====', $mod4);
        }
        return $reString;
    }
}