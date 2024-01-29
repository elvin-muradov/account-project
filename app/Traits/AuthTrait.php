<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

trait AuthTrait
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    protected function getUser(string $phone, string $password): bool|User
    {
        if ($phone && $password) {
            $user = $this->user->wherePhone($phone)->first();
            if ($user) {
                return Hash::check($password, $user->password) ? $user : false;
            }
        }

        return false;
    }

    protected function checkUser(string $phone, string $password): User|bool
    {
        if ($user = $this->getUser($phone, $password)) {
            return $user;
        }

        return false;
    }
}
