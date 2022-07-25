<?php

namespace App\Http\Controllers\API\HomePage;

use App\Http\Controllers\API\apiResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DiscountController extends Controller
{
    use apiResponseTrait;

    public function index()
    {
        $data = Product::with('discount')->whereNotNull('discount_id')->get();
        if ($data->isEmpty()) {
            return $this->apiResponse(null, 'Nothing to view', 401);
        }
        return $this->apiResponse($data, 'Products that have discount successfully sent', 200);

    }


    public function create()
    {

    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:20',
            'description' => 'required|string|min:10',
            'discount_percent' => 'required|numeric|max:100',
            'active' => 'required|numeric|in:0,1',
            'deadline' => 'required|date_format:Y-m-d H:i:s|after_or_equal:today',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse($validator->errors(), "fails", 422);
        }
        $discount = Discount::create(array_merge(
            $validator->validated()
        ));

        return $this->apiResponse($discount, 'Discount successfully added', 200);

    }


    public function show($id)
    {

        if (Discount::where('id', $id)->exists()) {
            $discount = Discount::where('id', $id)->get();
            return $this->apiResponse($discount, 'Discount successfully found', 200);

        } else {
            return $this->apiResponse(null, "Discount not found", 202);
        }
    }

    public function showProductOffer($id)
    {

        if (Discount::where('id', $id)->exists()) {
            $product = Product::with('discount')->where('discount_id',$id)->get();
            return $this->apiResponse($product, 'Discount successfully found', 200);

        } else {
            return $this->apiResponse(null, "Discount not found", 202);
        }
    }


    public function edit($id)
    {

    }

    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:20',
            'description' => 'required|string|min:10',
            'discount_percent' => 'required|numeric|max:100',
            'active' => 'required|numeric|in:0,1',
            'deadline' => 'required|date_format:Y-m-d H:i:s|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse($validator->errors(), "fails", 422);
        }
        $discount = Discount::find($id);
        if ($discount) {
            $discount->update($validator->validated());
            return $this->apiResponse($discount, 'Discount successfully updated', 200);

        } else {
            return $this->apiResponse(null, "Discount not found", 202);
        }


    }

    public function destroy($id)
    {
        if (Discount::where('id', $id)->exists()) {
            $discount = Discount::destroy($id);
            return $this->apiResponse(null, 'Discount successfully deleted', 200);

        } else {
            return $this->apiResponse(null, "Discount not found", 402);
        }

    }
}
