<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 24.08.2019, 06:58
 */

namespace Core\Utility;
class Utils
{
    public static function friendlify(string $str): string
    {
        $str = preg_replace('~[^\\pL0-9_]+~u', '-', $str);
        $str = trim($str, '-');
        $str = iconv('utf-8', 'us-ascii//TRANSLIT', $str);
        $str = strtolower($str);
        $str = preg_replace('~[^-a-z0-9_]+~', '', $str);
        return $str;
    }
}
