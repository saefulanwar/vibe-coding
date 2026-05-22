<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;

class CustomLogin extends BaseLogin
{
    protected string $view = 'filament.pages.auth.custom-login';

    protected static string $layout = 'filament-panels::components.layout.base';

    public function hasLogo(): bool
    {
        return false;
    }
}
