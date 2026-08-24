<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    /**
     * Get all users
     */
    public function getAllUsers()
    {
        return User::with('roles')
            ->with('addresses')
            ->select(
                'id',
                'name',
                'email',
                'phone',
                'status',
                'created_at',
                'updated_at'
            )
            ->latest()
            ->get();
    }

    /**
 * Get all local customers
 */
public function getLocalCustomers()
{
    return User::with([
        'roles',
        'addresses',
        'orders',
    ])
        ->whereHas('roles', function ($query) {
            $query->where('name', 'local-client')
                  ->where('guard_name', 'sanctum');
        })
        ->select(
            'id',
            'name',
            'email',
            'phone',
            'status',
            'customer_type',
            'created_at',
            'updated_at'
        )
        ->latest()
        ->get();
}

    /**
     * Create new user
     */
    public function createUser(array $data)
    {
        /*
         * Create user
         */
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'status' => $data['status'] ?? 'active',
        ]);

        /*
         * Assign Spatie role
         */
        if (!empty($data['role'])) {
            $user->assignRole($data['role']);
        }

        /*
         * Return user with role
         */
        return $user->load([
            'roles',
            'addresses',
        ]);
    }

    /**
     * Get user by ID
     */
    public function getUserById($id)
    {
        return User::with([
            'roles',
            'addresses',
            'orders',
        ])->find($id);
    }

    /**
     * Update user
     */
    public function updateUser($id, array $data)
    {
        $user = User::find($id);

        if (!$user) {
            return null;
        }

        /*
         * Update basic information
         */
        $user->update([
            'name' => $data['name'] ?? $user->name,

            'email' => $data['email'] ?? $user->email,

            'phone' => $data['phone'] ?? $user->phone,

            'status' => $data['status'] ?? $user->status,
        ]);

        /*
         * Update password
         */
        if (
            isset($data['password']) &&
            !empty($data['password'])
        ) {
            $user->password = Hash::make(
                $data['password']
            );

            $user->save();
        }

        /*
         * Update Spatie role
         */
        if (
            isset($data['role']) &&
            !empty($data['role'])
        ) {
            $user->syncRoles([
                $data['role'],
            ]);
        }

        /*
         * Return updated user
         */
        return $user->load([
            'roles',
            'addresses',
            'orders',
        ]);
    }

    /**
     * Delete user
     */
    public function deleteUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return false;
        }

        /*
         * Remove Spatie roles
         */
        $user->syncRoles([]);

        /*
         * Delete user
         */
        $user->delete();

        return true;
    }
}
