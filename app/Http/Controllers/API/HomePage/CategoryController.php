<?php

namespace App\Http\Controllers\API\HomePage;

use App\Http\Controllers\API\apiResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class CategoryController extends Controller
{
    use apiResponseTrait;

    public function index()
    {
        $data = Category::where('active', 1)->get();
//            DB::table('categories')->where('active',0)->get();
        if ($data->isEmpty()) {
            return $this->apiResponse(null, 'Nothing to view', 401);
        }
        return $this->apiResponse($data, 'categories send successfully', 200);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:20',
            'active' => 'required|numeric|in:0,1',
        ]);
        if ($validator->fails()) {
            return $this->apiResponse($validator->errors(), "fails", 422);
        }
        $category = new Category();
        $category->title = $request['title'];
        $category->active = $request['active'];
        $category->save();

        return $this->apiResponse($category, 'Category successfully added', 200);

    }

    public function show($id)
    {

        if (Category::where('id', $id)->exists()) {
            $category = Category::find($id);
            return $this->apiResponse($category, 'Category successfully found', 200);

        } else {
            return $this->apiResponse(null, "Category not found", 202);
        }

    }

    public function destroy($id)
    {

        if (Category::where('id', $id)->exists()) {
            $category = Category::destroy($id);
//            $category->delete();
            return $this->apiResponse(null, 'Category successfully deleted', 200);

        } else {
            return $this->apiResponse(null, "Category not found", 202);
        }
    }

    public function update(Request $request, $id)
    {
        if (Category::where('id', $id)->exists()) {
            $category = Category::where('id', $id);
            if (count($request->all()) >= 1) {
                $category->update($request->all());
                return $this->apiResponse($category->get(), 'Category successfully updated', 200);
            }
            else{
                return $this->apiResponse($category->get(), 'Nothing to update', 200);
            }
        } else {
            return $this->apiResponse(null, "Category not found", 202);
        }
    }


}
