<?php

namespace App\Services;

use App\Exceptions\LoginInvalidException;
use App\Exceptions\UserHasBeenTakenException;
use App\Models\User;
use Illuminate\Support\Str;

class AuthService
{
    public function register(string $first_name, string $last_name, string $email, string $password)
    {
        $user = User::where('email', $email)->exists();

        if (!empty($user)) {
            throw new UserHasBeenTakenException();
        }

        $user_password = bcrypt($password ?? Str::random(10));

        $user = User::create([
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'password' => $user_password,
            'confirmation_token' => Str::random(60),
        ]);

        return $user;
    }

    public function login(string $email, string $password)
    {
        $login = [
            'email' => $email,
            'password' => $password,
        ];

        if(!$token = auth()->attempt($login)){
            throw new LoginInvalidException();
        }

        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }
}
