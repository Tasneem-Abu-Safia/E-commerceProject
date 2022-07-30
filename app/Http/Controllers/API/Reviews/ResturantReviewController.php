<?php

namespace App\Http\Controllers\API\Reviews;

use App\Http\Controllers\API\apiResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ResturantReviewController extends Controller
{
    use apiResponseTrait;

    public function index(Request $request)
    {
        $restaurantReview = Review::with(['user' => function ($query) {
            $query->select('id', 'image');
        }])->where([
            ['ratingFor_type', '=', 'App\Models\Restaurant'],
            ['ratingFor_id', '=', $request['restaurant_id']]
        ])->orderBy('rate')->get();
        return $this->apiResponse($restaurantReview, 'All Restaurant Reviews', 200);
    }

    public function show($id)
    {
        $Review = Review::where([
            ['ratingFor_type', '=', 'App\Models\Restaurant'],
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
        $is_IPrating = Review::withoutTrashed()->where('ipAddress', exec('getmac'))
            ->where([
                ['ratingFor_type', '=', 'App\Models\Restaurant'],
                ['ratingFor_id', '=', $request['restaurant_id']]
            ])->exists();

        if ($is_IPrating) {
            return $this->apiResponse([], 'You have already rated', 202);

        } else {

            $review = Review::create(array_merge(
                $validator->validated(),
                [
                    'user_id' => Auth::check() ? Auth::id() : null,
                    'ratingFor_id' => $request['restaurant_id'],
                    'ratingFor_type' => 'App\Models\Restaurant',
                    'feedback' => $request['feedback'],
                    'ipAddress' => exec('getmac'),
                ],
            ));
            $this->updateRestaurantRating($request['restaurant_id'], $request['rate']);
            return $this->apiResponse($review, 'Review added successfully!', 200);

        }
    }

    public function updateRestaurantRating($restaurant_id, $rate = 0)
    {
        $rest = Restaurant::find($restaurant_id);

        $ratingSum = Review::withoutTrashed()->where([
            ['ratingFor_type', '=', 'App\Models\Restaurant'],
            ['ratingFor_id', '=', $restaurant_id]
        ])->sum('rate');

        $count = Review::withoutTrashed()->where([
            ['ratingFor_type', '=', 'App\Models\Restaurant'],
            ['ratingFor_id', '=', $restaurant_id]
        ])->count();

        $ratingCount = ($count != 0) ? $count : 1;
        $newRating = $ratingSum / $ratingCount;


//        $NumRating = $restaurant->NumRating + 1;
//        $newRating = (($restaurant->rating * $restaurant->NumRating) + $rate) / ($NumRating);

        $rest->rating = round($newRating, 2);
        $rest->NumRating = round($count, 2);
        $rest->save();
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
            ['ratingFor_type', 'App\Models\Restaurant'],
            ['ratingFor_id', '=', $request['restaurant_id']]
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
                $this->updateRestaurantRating($request['restaurant_id'], $request['rate']);
            }

            return $this->apiResponse($userReview, "Review successfully updated", 200);

        } else {
            return $this->apiResponse([], "Nothing to update", 422);
        }


    }


    public function destroy($restaurant_id)
    {
        $userReview = Review::where([
            ['user_id', '=', Auth::id()],
            ['ratingFor_type', 'App\Models\Restaurant'],
            ['ratingFor_id', '=', $restaurant_id]
        ])->first();

        if ($userReview) {
            $userReview->delete();
            $this->updateRestaurantRating($restaurant_id);
            return $this->apiResponse([], "Review successfully deleted", 200);
        } else {
            return $this->apiResponse([], "Review not found", 422);
        }
    }
}
