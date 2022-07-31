<?php

namespace App\Http\Controllers\API\Setting;

use App\Http\Controllers\API\apiResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AddressController extends Controller
{
    use apiResponseTrait;

    public function index()
    {
        $addresses = UserAddress::where('user_id', Auth::id())->get();
        if ($addresses->isEmpty()) {
            return $this->apiResponse([], 'Nothing to show', 200);

        }
        return $this->apiResponse($addresses, 'User Order Send successfully', 200);
    }

    public function store(Request $request)
    {
        $user = Auth::id();
        $validator = Validator::make($request->all(), [
            'address' => 'required|String|min:4',
            'latitude' => 'required|numeric|max:90|min:-90',
            'longitude' => 'required|numeric|max:180|min:-180',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse($validator->errors(), "fails", 422);
        }
        $is_found = UserAddress::where([
            ['user_id', '=', $user],
            ['latitude', '=', $request->latitude],
            ['longitude', '=', $request->longitude],
        ])->get();
        if (!$is_found->isEmpty()) {
            return $this->apiResponse([], 'This address already exist', 200);
        }
        $address = UserAddress::create(array_merge(
            [
                'user_id' => Auth::id(),
                'address' => $request->address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
            ]
        ));
        return $this->apiResponse($address, 'Address successfully added', 200);

    }
}
