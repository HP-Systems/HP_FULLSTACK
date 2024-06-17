<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\User;

class UniqueEmailForUserableType implements Rule
{
    private $userableType;

    public function __construct($userableType)
    {
        $this->userableType = $userableType;
    }

    public function passes($attribute, $value)
    {
        return User::where('email', $value)
                   ->where('userable_type', $this->userableType)
                   ->count() == 0;
    }

    public function message()
    {
        return 'El correo ya está en uso para este tipo de usuario.';
    }
}