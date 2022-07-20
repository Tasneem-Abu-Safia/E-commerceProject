<?php

namespace App\Http\Controllers\API\HomePage;

use App\Http\Controllers\API\Auth\apiResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Restaurant;

class RestaurantsController extends Controller
{
    use apiResponseTrait;
    public function index()
    {
        $data = Restaurant::orderBy('rating', 'DESC')->get();
        if ($data->isEmpty()){
            return $this->apiResponse(null, 'Nothing to view',401);
        }
        return $this->apiResponse($data, 'Restaurants send successfully',200);
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        //
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }
}
