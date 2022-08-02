<?php

namespace App\Http\Controllers\API\Passwords;

use App\Http\Controllers\API\apiResponseTrait;
use App\Http\Controllers\Controller;
use App\Mail\SendCodeResetPassword;
use App\Models\ResetCodePassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    use apiResponseTrait;

    public function __invoke(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse($validator->errors(), "fails", 422);
        }
        // Delete all old code that user send before.
        ResetCodePassword::where('email', $request->email)->delete();

        // Generate random code
        $data['code'] = mt_rand(100000, 999999);

        // Create a new code
        $codeData = ResetCodePassword::create($data);

        // Send email to user
        Mail::to($request->email)->send(new SendCodeResetPassword($codeData->code));

        return response(['message' => trans('passwords.sent')], 200);

    }
}
