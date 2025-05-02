<?php

namespace App\Filament\User\Pages\Auth;

use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Database\Eloquent\Model;
use BezhanSalleh\FilamentShield\Support\Utils;

class Register extends BaseRegister
{
    protected function handleRegistration(array $data): Model
    {
        $user = parent::handleRegistration($data);

        $roleModel = Utils::getRoleModel();
        $defaultRole = $roleModel::where('name', 'user')->first();

        if ($defaultRole) {
            $user->assignRole($defaultRole);
        } else {
            logger()->error('Default "user" role not found.');
        }

        return $user;
    }
}