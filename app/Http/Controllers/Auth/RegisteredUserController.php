<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\Rules;


class RegisteredUserController extends Controller
{

    public function store(Request $request): JsonResponse
    {

        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:255'
            ],

            'email' => [
                'required',
                'email',
                'unique:users,email'
            ],

            'phone' => [
                'required',
                'unique:users,phone'
            ],

            'password' => [
                'required',
                'confirmed',
                Rules\Password::defaults()
            ],

        ]);



        $user = User::create([

            'name'=>$validated['name'],

            'email'=>$validated['email'],

            'phone'=>$validated['phone'],

            'status'=>'active',

            'password'=>Hash::make($validated['password']),

        ]);



        // إرسال إيميل التحقق
        event(new Registered($user));



        return response()->json([

            'message'=>'Registration successful. Check your email for verification link.',

            'user'=>$user

        ],201);


    }

}
