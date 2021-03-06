<?php

namespace App\Http\Controllers\API\HomePage;

use App\Http\Controllers\API\apiResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Restaurant;
use App\Models\Restaurant_Category;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    use apiResponseTrait;

    use apiResponseTrait;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Product::with('restaurant')->with('category')
            ->with('discount')
            ->get();
        if ($data->isEmpty()) {
            return $this->apiResponse(null, 'Nothing to view', 401);
        }
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
            'discount_id' => 'exists:discounts,id',
            'calories' => 'required|numeric',
        ]);
        $data = Restaurant_Category::where('restaurant_id', $request->restaurant_id)->pluck('category_id')->toArray();
        $path = "";
        if ($request->hasFile('image')) {
            $logo = $request->image;
            $fileName = date('Y') . $logo->getClientOriginalName();

            $path = $request->image->storeAs('product_image', $fileName, 'public');
        }

        if (!in_array($request['category_id'], $data)) {
            if ($validator->fails()) {
                return $this->apiResponse($validator->errors(), "fails", 422);
            }
            return $this->apiResponse(null, "Select False Category", 422);
        }

        $product = Product::create(array_merge(
            $validator->validated(),
            ['image' => $path, 'calories' => $request['calories'], 'discount_id' => $request['discount_id']],

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
            $product = Product::with('restaurant')->with('category')->with('discount')->find($id);
            return $this->apiResponse($product, 'Restaurant successfully found', 200);

        } else {
            return $this->apiResponse(null, "Restaurant not found", 202);
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
        $validator = Validator::make($request->all(), [
            'name' => 'required|String|min:4',
            'image' => 'required|mimes:png,jpg,jpeg,gif|max:2048',
            'description' => 'required|string|max:50',
            'price' => 'required|numeric',
            'restaurant_id' => 'required|exists:restaurants,id',
            'category_id' => 'required|numeric|exists:categories,id',
            'discount_id' => 'exists:discounts,id',
            'calories' => 'required|numeric',
        ]);
        $data = Restaurant_Category::where('restaurant_id', $request->restaurant_id)->pluck('category_id')->toArray();
        $filename = "";


        if (!in_array($request['category_id'], $data)) {
            if ($validator->fails()) {
                return $this->apiResponse($validator->errors(), "fails", 422);
            }
            return $this->apiResponse(null, "Select False Category", 422);
        }

        if ($request->hasFile('image')) {
            $logo = $request->image;
            $fileName = date('Y') . $logo->getClientOriginalName();

            $path = $request->image->storeAs('product_image', $fileName, 'public');
            $product['image'] = $path;
        }


        $product->update($request->except('image'));


        return $this->apiResponse($product, 'Products update successfully', 200);

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
            return $this->apiResponse(null, 'Product successfully deleted', 200);

        } else {
            return $this->apiResponse(null, "Product not found", 202);
        }
    }
}
