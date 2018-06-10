<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Validator;

use JWTAuth;
use App\User;
use JWTAuthException;

class AuthController extends Controller
{
    use AuthenticatesUsers, RegistersUsers {
        AuthenticatesUsers::redirectPath insteadof RegistersUsers;
        AuthenticatesUsers::guard insteadof RegistersUsers;
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()]);
        }

        try {
            // attempt to verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['success' => false, 'error' => 'We cant find an account with this credentials. Please make sure you entered the right information and you have verified your email address.'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['success' => false, 'error' => 'Failed to login, please try again.'], 500);
        }
        // all good so return the token
        return response()->json(['success' => true])->cookie('Authorization', $token);
    }

    public function register(Request $request)
    {
        $credentials = $request->only('name', 'email', 'password');
        $rules = [
            'name' => 'required|max:255|',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6',
        ];
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'error' => $validator->messages()]);
        }
        $name = $request->name;
        $email = $request->email;
        $password = bcrypt($request->password);
        User::create(['name' => $name, 'email' => $email, 'password' => $password]);
        return response()->json(['success' => true, 'email' => $email, 'password' => $password]);
    }

    public function logout(Request $request)
    {
        return response()->json(['success' => true, 'message' => "You have successfully logged out."])->cookie('Authorization', '');
    }
}
