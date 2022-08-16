<?php

namespace App\Http\Controllers\API\Reviews;

use App\Http\Controllers\API\apiResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductReviewController extends Controller
{
    use apiResponseTrait;

    public function index(Request $request)
    {
        $productReview = Review::with(['user'])->where([
            ['ratingFor_type', '=', 'App\Models\Product'],
            ['ratingFor_id', '=', $request['product_id']]
          ])->orderBy('rate')->get();

        return $this->apiResponse($productReview, 'All Product Review', 200);
    }


    public function show($id)
    {
        $Review = Review::where([
            ['ratingFor_type', '=', 'App\Models\Product'],
            ['id', '=', $id]
        ])->get();
        return $this->apiResponse($Review, 'Review', 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rate' => 'required|integer|between:0,5',
            'feedback' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse($validator->errors(), "fails", 422);
        }
        if (Product::find($request->product_id)){
        $is_IPrating = Review::withoutTrashed()->where('ipAddress', exec('getmac'))
            ->where([
                ['ratingFor_type', '=', 'App\Models\Product'],
                ['ratingFor_id', '=', $request['product_id']]
            ])->exists();
        if ($is_IPrating) {
            return $this->apiResponse([], 'You have already rated', 202);

        } else {

            $review = Review::create(array_merge(
                $validator->validated(),
                [
                    'user_id' => Auth::check() ? Auth::id() : null,
                    'ratingFor_id' => $request['product_id'],
                    'ratingFor_type' => 'App\Models\Product',
                    'feedback' => $request['feedback'],
                    'ipAddress' => exec('getmac'),
                ],
            ));
            $this->updateProductRating($request['product_id'], $request['rate']);
            return $this->apiResponse($review, 'Review added successfully!', 200);

        }}
        else{
            return $this->apiResponse([], 'Product Not Found', 200);

        }
    }

    public function updateProductRating($product_id, $rate = 0)
    {
        $product = Product::find($product_id);

        $ratingSum = Review::withoutTrashed()->where([
            ['ratingFor_type', '=', 'App\Models\Product'],
            ['ratingFor_id', '=', $product_id]
        ])->sum('rate');

        $count = Review::withoutTrashed()->where([
            ['ratingFor_type', '=', 'App\Models\Product'],
            ['ratingFor_id', '=', $product_id]
        ])->count();
        $ratingCount = ($count != 0) ? $count : 1;
        $newRating = $ratingSum / $ratingCount;


//        $NumRating = $product->NumRating + 1;
//        $newRating = (($product->rating * $product->NumRating) + $rate) / ($NumRating);

        $product->rating = round($newRating, 2);
        $product->NumRating = round($count, 2);
        $product->save();
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rate' => 'required|integer|between:0,5',
            'feedback' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse($validator->errors(), "fails", 422);
        }
        $userReview = Review::where([
            ['user_id', '=', Auth::id()],
            ['ratingFor_type', 'App\Models\Product'],
            ['ratingFor_id', '=', $request['product_id']]
        ])->first();

        if (!$userReview) {
            return $this->apiResponse([], "Review not found", 422);
        }

        if ($userReview->rate != $request->rate || $userReview->feedback != $request->feedback) {
            $not_match = ($userReview->rate != $request->rate) ? true : false;
            $userReview->rate = $request['rate'];
            $userReview->feedback = $request->feedback ? $request['feedback'] : $userReview->feedback;
            $userReview->save();
            if ($not_match) {
                $this->updateProductRating($request['product_id'], $request['rate']);
            }

            return $this->apiResponse($userReview, "Review successfully updated", 200);

        } else {
            return $this->apiResponse([], "Nothing to update", 422);
        }


    }


    public function destroy($product_id)
    {
        $userReview = Review::where([
            ['user_id', '=', Auth::id()],
            ['ratingFor_type', 'App\Models\Product'],
            ['ratingFor_id', '=', $product_id]
        ])->first();

        if ($userReview) {
            $userReview->delete();
            $this->updateProductRating($product_id);
            return $this->apiResponse([], "Review successfully deleted", 200);
        } else {
            return $this->apiResponse([], "Review not found", 422);
        }
    }
}
