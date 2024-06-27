<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Models\User;

class UniqueEmailNotId implements Rule
{
    private $userableType;
    private $userId;

    public function __construct($userableType, $userId)
    {
        $this->userableType = $userableType;
        $this->userId = $userId;
    }

    public function passes($attribute, $value)
    {
        $query = User::where('email', $value)
                     ->where('userable_type', $this->userableType);

        if ($this->userId) {
            $query->where('userable_id', '!=', $this->userId);
        }

        return $query->count() == 0;
    }

    public function message()
    {
        return 'El correo ya está en uso para este tipo de usuario.';
    }
}