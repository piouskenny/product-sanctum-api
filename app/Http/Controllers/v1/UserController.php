<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function register(Request $request) {
        $request->validate([
            'name' => 'required',
            'email' => 'email|required|unique:users',
            'password' => 'required|string'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);


        $token = $user->createToken('userToken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response()->json($response);
    }

    public function login(Request $request) {
        $request->validate([
            'email' => 'email|required',
            'password' => 'required|string'
        ]);


        $user =  User::where('email', $request->email)->first();


        if(!$user || Hash::check($request->password, $user->password)) {
            return response([
                'message' => "Invalid user details"
            ], 401);
        }

        $token = $user->createToken('userToken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response()->json($response);
    }



    public function logout() {
        auth()->user()->tokens()->delete();

        return  [
            'message' => 'User Logged Out'
        ];
    }
}
