<?php
/**
 * Copyright © 2019 by Angius
 * Last modified: 19.08.2019, 05:28
 */

namespace Omnibus\Core\Security;

use Exception;
use RuntimeException;


class Token
{
    /**
     * @param int $length
     * @return string
     */
    public static function Get(int $length = 32): string
    {
        try {
            $bytes = random_bytes($length / 2);
        } catch (Exception $e) {
            throw new RuntimeException('Could not generate token, no source of randomness');
        }
        return bin2hex($bytes);
    }
}
