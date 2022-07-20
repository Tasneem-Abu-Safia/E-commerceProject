<?php

namespace App\Http\Controllers\API\HomePage;

use App\Http\Controllers\API\Auth\apiResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoriesController extends Controller
{
    use apiResponseTrait;
    public function index()
    {
        $data = DB::table('categories')->where('active',0)->get();
        if ($data->isEmpty()){
            return $this->apiResponse(null, 'Nothing to view',401);
        }
        return $this->apiResponse($data, 'categories send successfully',200);
    }

}
