<?php

namespace App\Factories;

use App\Models\User;
use App\Models\Role;
use InvalidArgumentException;

class UserAccountFactory
{
    public static function create(string $roleType, array $data): User
    {
        $role = Role::where('name', $roleType)->first();

        if (!$role) {
            throw new InvalidArgumentException("Role '{$roleType}' does not exist in the database.");
        }

        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'], // Do not use Hash::make() here!
            'role_id' => $role->id,
            'organisation_id' => $data['organisation_id'] ?? null,
        ];

        return User::create($userData);
    }
}