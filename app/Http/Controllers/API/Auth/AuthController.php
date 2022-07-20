<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use apiResponseTrait;
    public function register(Request $request){
        $request->validate([
            'name' => 'required|string|between:2,100',
            'phone_number' => 'required|string|between:9,15',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = new user([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $user->save();
        return $this->apiResponse($user, 'User successfully registered',200);

    }

    public function login(Request $request){
        $validator = $request->validate([
            'email' => 'required',
            'password' => 'required|string',
        ]);
        $data =request(['email','password']);
        if (!Auth::attempt($data)){
            return $this->apiResponse(null, 'Unauthorized',401);
        }
        $user = $request->user;
        $tokenResult = $user->createToken();
        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addWeek(1);
        $token->save();
        $array_data = [
          'user' => Auth::user(),
          'access_token' => $tokenResult->accessToken,
          'token_type' => 'Bearer',
          'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
        ];
        return $this->apiResponse($array_data, '',201);
    }
}
