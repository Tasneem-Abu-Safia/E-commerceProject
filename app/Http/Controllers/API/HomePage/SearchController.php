<?php

namespace App\Http\Controllers\API\HomePage;

use App\Http\Controllers\API\apiResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    use apiResponseTrait;

    public function Search(Request $request, $string)
    {
        if ($string != null) {
            $result1 = Restaurant::with('product')->where('name', 'like', '%' . $string . '%')->get();
            $result2 = Product::with('restaurant')->where('name', 'like', '%' . $string . '%')->get();
            return $this->apiResponse(['Products' => $result2, 'Restaurant' => $result1], 'Result successfully send', 200);

        } else {
            return $this->apiResponse(null, "Result not found", 402);

        }
    }

}
