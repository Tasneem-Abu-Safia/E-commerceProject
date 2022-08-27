<?php

namespace App\Http\Controllers\API\HomePage;

use App\Http\Controllers\API\apiResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class SearchFilterController extends Controller
{
    use apiResponseTrait;

    public function Search(Request $request)
    {
        if ($request->search_query) {
            $result1 = Restaurant::where('name', 'like', '%' . $request->search_query . '%')
                ->orderBy('rating')->select('id', 'name', 'logo', 'rating', 'address')->paginate($request->pagesize);

            $result2 = Product::where('name', 'like', '%' . $request->search_query . '%')
                ->orderBy('rating')
                ->select(['id', 'name', 'image', 'price', 'calories', 'description', 'active', 'rating', 'NumRating', 'restaurant_id'])
                ->paginate($request->pagesize);

            return $this->apiResponse(['Products' => $result2, 'Restaurant' => $result1], 'Result successfully send', 200);

        } else {
            return $this->apiResponse([], "Result not found", 402);

        }
    }

    public function Filter(Request $request)
    {
        $products_query = Product::with(['restaurant', 'category', 'discount', 'subcategory']);
        if ($request->category) {
            $products_query->whereHas('category', function ($query) use ($request) {
                $query->where('title', $request->category);
            });
        }
        if ($request->subcategory) {
            $products_query->whereHas('subcategory', function ($query) use ($request) {
                $query->where('title', $request->subcategory);
            });
        }
        if ($request->rating) {
            $products_query->where('rating', $request->rating);
        }

        if ($request->priceMax && $request->priceMin) {
            $products_query->whereBetween('price', [$request->priceMin, $request->priceMax]);
        }

        $products = $products_query->paginate($request->pagesize);

        return $this->apiResponse($products, 'Result successfully send', 200);

    }


}
