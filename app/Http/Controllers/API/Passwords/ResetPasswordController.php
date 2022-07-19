<?php

namespace App\Http\Controllers\API\Passwords;

use App\Http\Controllers\API\apiResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\ResetCodePassword;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ResetPasswordController extends Controller
{
    use apiResponseTrait;
    public function __invoke(Request $request){

        $validator = Validator::make($request->all(), [
            'code' => 'required|string|exists:reset_code_passwords',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse($validator->errors(),"fails",422);
        }

        //find the code
        $passwordReset = ResetCodePassword::firstWhere('code',$request->code);

        //check if it does not expired ==> the time 1 hour
        if ($passwordReset->created_at > now()->addHour()){
            $passwordReset->delete();
            return response(['message' => trans('passwords.code_is_expire')], 422);

        }
        //find user
        $user = User::firstWhere('email',$passwordReset->email);

        // update Password
        $user->update($request->only('password'));

        //delete current code
        ResetCodePassword::where('code',$request->code)->delete();

        return response(['message' =>'password has been successfully reset'], 200);

    }
}
