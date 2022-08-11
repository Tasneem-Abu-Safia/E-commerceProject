<?php

namespace App\Http\Controllers\API\HomePage;

use App\Http\Controllers\API\apiResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Restaurant_Category;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Restaurant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\Console\Input\Input;

class RestaurantController extends Controller
{
    use apiResponseTrait;

    public function index(Request $request)
    {
        $data = Restaurant::with(['categories' => function ($q) {
            $q->where('active', 1)->select('title');
        }])->orderBy('rating', 'DESC')->select(['id', 'name', 'logo', 'rating'])->paginate($request->pagesize);
        if ($data->isEmpty()) {
            return $this->apiResponse($data, 'Nothing to view', 401);
        }
        $categories = Category::where('active', 1)->pluck('title');
        $categories->prepend("All");;
        return $this->apiResponse(['restaurants' => $data, 'categories' => $categories], 'Restaurants send successfully', 200);
    }

    public function popularRestaurant()
    {
        $data = Restaurant::with('product')->orderBy('rating', 'DESC')->take(6)
            ->get(['id', 'name', 'logo', 'rating', 'NumRating', 'address', 'start_at', 'end_at']);
        return $this->apiResponse($data, 'Restaurants send successfully', 200);
    }


    public function store(Request $request)
    {
        //use map for address --> website
        $validator = Validator::make($request->all(), [
            'name' => 'required|String|min:4',
            'logo' => 'required|mimes:png,jpg,jpeg,gif|max:2048',
            'description' => 'required|string|min:10',
            'phoneNumber' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'start_at' => 'required|date_format:H:i',
            'end_at' => 'required|date_format:H:i',
            'address' => 'required|String|min:5',
            'category_id' => 'required|numeric|exists:categories,id',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse($validator->errors(), "fails", 422);
        }

        $path = "";
        if ($request->hasFile('logo')) {
            $logo = $request->logo;
            $fileName = date('Y-m-d') . $request->name . '-' . $logo->getClientOriginalName();

            $path = $request->logo->storeAs('restaurant_image', $fileName, 'public');

        }


        $restaurant = Restaurant::create(array_merge(
            $validator->validated(),
            ['logo' => 'storage/' . $path, 'rating' => 0.0,
                'start_at' => Carbon::parse($request->start_at)->format('h:i A'),
                'end_at' => Carbon::parse($request->end_at)->format('h:i A'),
            ]
        ));


        $data = Restaurant_Category::create(array_merge(
            ['restaurant_id' => $restaurant['id'],
                'category_id' => $request['category_id'],

            ]
        ));


        return $this->apiResponse($restaurant, 'Restaurant successfully added', 200);

    }


    public function show($id)
    {
        if (Restaurant::where('id', $id)->exists()) {
            $rest = Restaurant::with(['product'])->where('id', $id)
                ->get(['id', 'name', 'logo', 'rating', 'NumRating', 'address', 'start_at', 'end_at']);

            return $this->apiResponse($rest, 'Restaurant successfully found', 200);

        } else {
            return $this->apiResponse([], "Restaurant not found", 202);
        }
    }


    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|String|min:4',
            'logo' => 'nullable|mimes:png,jpg,jpeg,gif|max:2048',
            'description' => 'required|string|min:10',
            'phoneNumber' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'address' => 'required|String|min:5',
            'start_at' => 'required|date_format:H:i',
            'end_at' => 'required|date_format:H:i',
            'category_id' => 'required|numeric|exists:categories,id',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse($validator->errors(), "fails", 422);
        }
        $rest = Restaurant::find($id);
        if ($rest) {

            if ($request->hasFile('logo')) {
                if ($rest->logo) {
                    $old_path = public_path($rest->logo);
                    if (File::exists($old_path)) {
                        File::delete($old_path);
                    }
                }
                $logo = $request->logo;
                $fileName = date('Y-m-d') . $rest->name . '-' . $logo->getClientOriginalName();
                $path = $request->logo->storeAs('restaurant_image', $fileName, 'public');
                $rest['logo'] = 'storage/' . $path;
            } else {
                $rest['logo'] = $rest->logo;
            }
            $data = Restaurant_Category::where(['restaurant_id' => $rest['id']])->first()->update(['category_id' => $request->category_id]);

            $rest->update(
                $request->except(['category_id', 'logo']),
                ['start_at' => 'required|date_format:H:i',
                    'end_at' => 'required|date_format:H:i',]
            );
            return $this->apiResponse($rest, 'Restaurant successfully updated', 200);

        } else {
            return $this->apiResponse([], "Restaurant not found", 202);
        }
    }


    public function destroy($id)
    {

        if (Restaurant::where('id', $id)->exists()) {
            $rest = Restaurant::destroy($id);
//            $rest->delete();
            return $this->apiResponse([], 'Restaurant successfully deleted', 200);

        } else {
            return $this->apiResponse([], "Restaurant not found", 202);
        }
    }
}
