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
            ->select(
                'id',
                'name',
                'email',
                'phone',
                'status'
            )
            ->get();
    }



    /**
     * Create new user
     */
    public function createUser(array $data)
    {

        $user = User::create([

            'name' => $data['name'],

            'email' => $data['email'],

            'phone' => $data['phone'],

            'password' => Hash::make($data['password']),

            'status' => 'active'

        ]);


        // Assign role to user
        $user->roles()->attach($data['role_id']);


        return $user->load('roles');

    }





    /**
     * Get user by id
     */
    public function getUserById($id)
    {

        return User::with('roles')
            ->find($id);

    }





    /**
     * Update user
     */
    public function updateUser($id, array $data)
    {

        $user = User::find($id);


        if(!$user)
        {
            return null;
        }



        $user->update([

            'name' => $data['name'] ?? $user->name,

            'email' => $data['email'] ?? $user->email,

            'phone' => $data['phone'] ?? $user->phone,

            'status' => $data['status'] ?? $user->status,

        ]);



        if(isset($data['password']))
        {

            $user->password = Hash::make($data['password']);

            $user->save();

        }



        // Update role
        if(isset($data['role_id']))
        {

            $user->roles()->sync([
                $data['role_id']
            ]);

        }



        return $user->load('roles');

    }





    /**
     * Delete user
     */
    public function deleteUser($id)
    {

        $user = User::find($id);


        if(!$user)
        {
            return false;
        }



        // Remove roles relation
        $user->roles()->detach();


        // Delete user
        $user->delete();



        return true;

    }

}
