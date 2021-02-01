<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;

class PasswordService
{
    const PASSWORD_LENGTH = 6;

    public function encrypt(string $password)
    {
        return Crypt::encrypt($password);
    }

    public function decrypt(string $password)
    {
        return Crypt::decrypt($password);
    }

    public function generate()
    {
        $randomString = substr(
            str_shuffle('abcdefghjkmnpqrstuvwxyz23456789'),
            0,
            self::PASSWORD_LENGTH
        );
        return $this->encrypt($randomString);
    }
}
