<?php

namespace App\Http\Controllers\API\HomePage;

use App\Http\Controllers\API\apiResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubCategoryController extends Controller
{
    use apiResponseTrait;

    public function index(Request $request)
    {
        $data = SubCategory::pluck('title');
        if ($data->isEmpty()) {
            return $this->apiResponse($data, 'Nothing to view', 401);
        }
        return $this->apiResponse($data, 'SubCategory send successfully', 200);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:20',
            'description' => 'required|string|min:10',
            'category_id' => 'required|numeric|exists:categories,id',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse($validator->errors(), "fails", 422);
        }

        $data = SubCategory::create(array_merge(
            $validator->validated(),
        ));


        return $this->apiResponse($data, 'SubCategory successfully added', 200);

    }

    public function show($id)
    {

        if (SubCategory::where('id', $id)->exists()) {
            $subcategory = SubCategory::find($id);
            return $this->apiResponse($subcategory, 'SubCategory successfully found', 200);

        } else {
            return $this->apiResponse([], "SubCategory not found", 202);
        }

    }

    public function destroy($id)
    {

        if (SubCategory::where('id', $id)->exists()) {
            $subcategory = SubCategory::destroy($id);
            return $this->apiResponse([], 'SubCategory successfully deleted', 200);

        } else {
            return $this->apiResponse([], "SubCategory not found", 202);
        }
    }

    public function update(Request $request, $id)
    {
        $subCategory = SubCategory::find($id);
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:20',
            'description' => 'required|string|min:10',
            'category_id' => 'required|numeric|exists:categories,id',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse($validator->errors(), "fails", 422);
        }

        $subCategory->update($request->all());
        return $this->apiResponse($subCategory, 'SubCategory update successfully', 200);

    }


}
