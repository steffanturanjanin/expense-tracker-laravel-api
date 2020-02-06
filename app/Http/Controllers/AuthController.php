<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\User as UserResource;

class AuthController extends Controller
{
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails())
        {
            return response()->json($validator->errors(), 409);
        }

        $user = new User([
           'email' => $request->email,
           'password' => bcrypt($request->password)
        ]);

        $user->save();

        $token = $user->createToken('expense-tracker-api')->accessToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token
        ]);

        //return response()->json(['email' => $user->email], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        if ($validator->fails())
        {
            return response()->json($validator->errors(), 409);
        }

        $credentials = request(['email', 'password']);

        if (!Auth::attempt($credentials))
        {
            return response()->json(['authentication' => 'Incorrect email or password'], 401);
        }

        $user = $request->user();

        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        $token->save();

        $user = User::where('email', $request->email)->first();


        return response()->json([
            'user' => new UserResource($user),
            'token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString()
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function user(Request $request)
    {
        return $request->user();
    }
}
