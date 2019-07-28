<?php
namespace Core\Security;

class PasswordUtils
{
    public static function Check(string $password, int $length = 10, bool $needs_numbers = true, bool $needs_capitals = true, bool $needs_special = true): ?array
    {
        $messages = [];

        // Check password length
        if ($length > 0 && strlen($password) < $length) {
            $messages[] = 'Password has to be at least 10 characters long.';
        }
        // Check password special chars
        if ($needs_special && !preg_match('/[_\W]/', $password)) {
            $messages[] = 'Password needs at least one special character.';
        }
        // Check password numbers
        if ($needs_numbers && !preg_match('/[_0-9]/', $password)) {
            $messages[] = 'Password needs at least one number.';
        }
        // Check password capital letters
        if ($needs_capitals && !preg_match('/[_A-Z]/', $password)) {
            $messages[] = 'Password needs at least one capital letter.';
        }

        return count($messages) > 0 ? $messages : null;
    }
}
