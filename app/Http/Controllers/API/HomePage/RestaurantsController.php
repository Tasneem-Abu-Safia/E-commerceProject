<?php

namespace App\Http\Controllers\API\HomePage;

use App\Http\Controllers\API\apiResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Restaurant_Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Restaurant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RestaurantsController extends Controller
{
    use apiResponseTrait;

    public function index()
    {
        $data = Restaurant::orderBy('rating', 'DESC')->get();
        if ($data->isEmpty()) {
            return $this->apiResponse(null, 'Nothing to view', 401);
        }
        $categories = Category::all();

        return $this->apiResponse(['restaurants' => $data, 'categories' => $categories], 'Restaurants send successfully', 200);
    }


    public function create()
    {

    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|String|min:4',
            'logo' => 'required|string|max:20',
            'description' => 'required|string|max:20',
            'phoneNumber' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'address' => 'required|String|min:5',
            'category_id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse($validator->errors(), "fails", 422);
        }

        $restaurant = Restaurant::create(array_merge(
            $validator->validated(),
            ['rating' => 0.0]
        ));

        $category = Category::where('id', $request['category_id'])->get();

        if (count($category) <= 0) {
            return $this->apiResponse(null, "category not found", 422);

        }
        foreach ($category as $c) {
            $data = Restaurant_Category::create(array_merge(
                ['restaurant_id' => $restaurant['id'],
                    'category_id' => $c['id']]
            ));

        }
        return $this->apiResponse($restaurant, 'Restaurant successfully added', 200);

    }


    public function show($id)
    {

        if (Restaurant::where('id', $id)->exists()) {
            $rest = Restaurant::with('Product')->find($id);
            return $this->apiResponse($rest, 'Restaurant successfully found', 200);

        } else {
            return $this->apiResponse(null, "Restaurant not found", 202);
        }

    }


    public function edit($id)
    {

    }


    public function update(Request $request, $id)
    {
        if (Restaurant::where('id', $id)->exists()) {
            $rest = Restaurant::where('id', $id);
            $rest->update($request->all());
            return $this->apiResponse($rest->get(), 'Restaurant successfully updated', 200);

        } else {
            return $this->apiResponse(null, "Restaurant not found", 202);
        }
    }


    public function destroy($id)
    {

        if (Restaurant::where('id', $id)->exists()) {
            $rest = Restaurant::destroy($id);
//            $rest->delete();
            return $this->apiResponse(null, 'Restaurant successfully deleted', 200);

        } else {
            return $this->apiResponse(null, "Restaurant not found", 202);
        }
    }
}
