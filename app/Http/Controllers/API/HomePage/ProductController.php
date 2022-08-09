<?php

namespace App\Http\Controllers\API\HomePage;

use App\Http\Controllers\API\apiResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Restaurant;
use App\Models\Restaurant_Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{

    use apiResponseTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = Product::orderBy('rating')
            ->select(['id', 'name', 'image', 'price', 'calories', 'description', 'active', 'rating', 'NumRating', 'restaurant_id'])
            ->paginate($request->pagesize);
        if ($data->isEmpty()) {
            return $this->apiResponse($data, 'Nothing to view', 401);
        }
        return $this->apiResponse($data, 'Products send successfully', 200);


    }

    public function popularProduct()
    {
        $data = Product::orderBy('rating', 'DESC')->take(6)->get(['id', 'name', 'image', 'price', 'calories', 'description', 'active', 'rating', 'NumRating', 'restaurant_id']);

        return $this->apiResponse($data, 'Products send successfully', 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|String|min:4',
            'image' => 'required|mimes:png,jpg,jpeg,gif|max:2048',
            'description' => 'required|string|max:50',
            'price' => 'required|numeric',
            'restaurant_id' => 'required|exists:restaurants,id',
            'category_id' => 'required|numeric|exists:categories,id',
            'subcategory_id' => 'numeric|exists:subcategories,id',
            'calories' => 'required|numeric',
            'active' => 'required|numeric|in:0,1',
        ]);

        $allcategory_id = Restaurant_Category::where('restaurant_id', $request->restaurant_id)->pluck('category_id')->toArray();
        $subcategory = SubCategory::whereIn('category_id', $allcategory_id)->pluck('id')->toArray();

        $path = "";
        if ($request->hasFile('image')) {
            $logo = $request->image;
            $fileName = date('Y-m-d') . $request->name . '-' . $logo->getClientOriginalName();

            $path = $request->image->storeAs('product_image', $fileName, 'public');
        }

        if ($validator->fails()) {
            return $this->apiResponse($validator->errors(), "fails", 422);
        }

        if (!in_array($request['category_id'], $allcategory_id)) {

            if (!in_array($request['subcategory_id'], $subcategory)) {
                return $this->apiResponse([], "Select False SubCategory", 422);
            }
            return $this->apiResponse([], "Select False Category", 422);
        }

        $product = Product::create(array_merge(
            $validator->validated(),
            ['image' => 'storage/' . $path, 'calories' => $request['calories']],

        ));

        return $this->apiResponse($product, 'Products send successfully', 200);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (Product::where('id', $id)->exists()) {
            $product = Product::with(['discount' => function ($q) use ($id) {
                $q->where('active' , 1)->orderBy('id', 'desc')->first();
            }])->select(['id', 'name', 'image', 'price', 'description', 'calories', 'active', 'rating', 'NumRating', 'restaurant_id'])->find($id);
            return $this->apiResponse($product, 'Product successfully found', 200);

        } else {
            return $this->apiResponse([], "Product not found", 202);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if ($product) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|String|min:4',
                'image' => 'nullable|mimes:png,jpg,jpeg,gif|max:2048',
                'description' => 'required|string|max:50',
                'price' => 'required|numeric',
                'restaurant_id' => 'required|exists:restaurants,id',
                'category_id' => 'required|numeric|exists:categories,id',
                'subcategory_id' => 'required|numeric|exists:subcategories,id',
                'calories' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return $this->apiResponse($validator->errors(), "fails", 422);
            }


            $allcategory_id = Restaurant_Category::where('restaurant_id', $request->restaurant_id)->pluck('category_id')->toArray();
            $subcategory = SubCategory::whereIn('category_id', $allcategory_id)->pluck('id')->toArray();


            if (!in_array($request['category_id'], $allcategory_id)) {

                if (!in_array($request['subcategory_id'], $subcategory)) {
                    return $this->apiResponse([], "Select False SubCategory", 422);
                }
                return $this->apiResponse([], "Select False Category", 422);
            }

            if ($request->hasFile('image')) {
                if ($product->image) {
                    $old_path = public_path($product->image);
                    if (File::exists($old_path)) {
                        File::delete($old_path);
                    }
                }
                $logo = $request->image;
                $fileName = date('Y-m-d') . $product->name . '-' . $logo->getClientOriginalName();

                $path = $request->image->storeAs('product_image', $fileName, 'public');
                $product['image'] = 'storage/' . $path;
            } else {
                $product['image'] = $product->image;
            }

            $product->update(
                $request->except('image'),
            );


            return $this->apiResponse($product, 'Products update successfully', 200);
        } else {

            return $this->apiResponse([], 'Products Not found', 200);

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Product::where('id', $id)->exists()) {
            $product = Product::destroy($id);
//            $product->delete();
            return $this->apiResponse([], 'Product successfully deleted', 200);

        } else {
            return $this->apiResponse([], "Product not found", 202);
        }
    }
}
