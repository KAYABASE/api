<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class ContactSettings extends Settings
{
    public string|null $email;
    public string|null $phone_number;

    public static function group(): string
    {
        return 'contact';
    }
}
