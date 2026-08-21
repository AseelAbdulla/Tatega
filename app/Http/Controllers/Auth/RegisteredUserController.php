<?php

namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules;


class RegisteredUserController extends Controller
{

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:' . User::class
            ],
            'password' => [
                'required',
                'confirmed',
                Rules\Password::defaults()
            ],
            'phone' => [
                'required',
                'string',
                'max:50',
                'unique:users,phone'
            ],
            'role' => [
                'required',
                'in:admin,local-client,international-client'
            ],
        ]);

        $roleName = match ($request->role) {
            'admin' => 'admin',
            'local-client' => 'customer',
            'international-client' => 'importer',
        };

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => 'active',
            'password' => Hash::make($request->string('password')),
        ]);

        $role = \App\Models\Role::where('name', $roleName)->firstOrFail();

        $user->roles()->attach($role->id);

        event(new Registered($user));
 
        return response()->noContent();
    }
 }
