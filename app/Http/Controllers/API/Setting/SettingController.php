<?php

namespace App\Http\Controllers\API\Setting;

use App\Http\Controllers\API\apiResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{

    use apiResponseTrait;

    public function userProfile(Request $request)
    {
        $user = Auth::user();
        return $this->apiResponse($user, 'User Send successfully', 200);

    }

    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|String|min:4',
            'image' => 'nullable|mimes:png,jpg,jpeg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse($validator->errors(), "fails", 422);
        }
        $user = $request->user();
        if ($request->hasFile('image')) {
            if ($user->image) {
                $old_path = public_path($user->image);

                if (File::exists($old_path)) {

                    File::delete($old_path);
                }
            }
            $logo = $request->image;
            $fileName = date('Y-m-d') . $user->name . '-' . $logo->getClientOriginalName();

            $pathImage = $request->image->storeAs('users_image', $fileName, 'public');
            $path = 'storage/' . $pathImage;
        } else {
            $path = $user->image;
        }
//        $this->editAddress();
        $user->update([
            'name' => $request->name,
            'image' => $path,
        ]);
        return $this->apiResponse($user, 'User update successfully', 200);


    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'Required',
            'password' => 'Required|min:6|max:100',
            'confirm_password' => 'required|same:password',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse($validator->errors(), 'Fails', 422);
        }
        $user = $request->user();
        if (Hash::check($request->old_password, $user->password)) {
            $user->update([
                'password' => bcrypt($request->Password),
            ]);
            return $this->apiResponse([], 'Password successfully updated', 422);

        } else {
            return $this->apiResponse([], 'Old password does not matched', 422);
        }

    }

    public function myOrder(Request $request)
    {
        $myorder = Order::with('orderDetails')->where('user_id', Auth::id())->get();

        return $this->apiResponse($myorder, 'User Order Send successfully', 200);

    }


}

