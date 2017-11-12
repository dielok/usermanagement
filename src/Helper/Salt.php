<?php
namespace Helper;

/**
 * Description of Salt
 *
 * @author martinleue
 */
class Salt {
    public static function back($email,$password) {
        $timestamp = time();
        $salt = base_convert(base_convert(bin2hex($email), 16, 10) * $timestamp * base_convert(bin2hex($password), 16, 10) * pow(13,143), 10, 26);
        return $salt;
    }
}
