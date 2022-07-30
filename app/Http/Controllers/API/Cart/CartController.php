<?php

namespace App\Http\Controllers\API\Cart;

use App\Http\Controllers\API\apiResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Order_Details;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use function PHPUnit\Framework\isNull;

class CartController extends Controller
{
    use apiResponseTrait;

    public function showCart()
    {
        $order = Order::where([
            ['user_id', '=', Auth::id()],
            ['status', '=', 'Draft'],
        ])->pluck('id');

        $orderDetails = Order_Details::whereIn('order_id', $order)->select('id', 'order_id', 'product_id', 'quantity', 'price')->get();
        $itemCount = count($orderDetails);
        $totalPrice = 0;
        foreach ($orderDetails as $item) {
            $totalPrice += $item->price;
        }
        return $this->apiResponse(['items' => $orderDetails, 'total Price ' => $totalPrice, 'itemCount' => $itemCount], "Your Cart", 200);


    }

    public function addToCart(Request $request)
    {
        $product = Product::with(['restaurant', 'discount'])->find($request->product_id);

        $validator = Validator::make($request->all(), [
            'quantity' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'product_id' => 'required|exists:products,id',
        ]);

        if ($validator->fails()) {
            return $this->apiResponse($validator->errors(), "fails", 422);
        }
        /*
                 $order = Order::firstOrCreate(
                    ['user_id' => Auth::id()],
                    ['restaurant_id' => $product->restaurant_id],
                    ['totalPrice' => 0],
                    ['discount' => $request->discount],
                    ['priceAfterDiscount' => 0],
        //            'priceAfterDiscount' => ($order->totalPrice - ($order->totalPrice * $order->discount / 100)),
                    ['status' => 'Draft'],
                );
        */
        $order = Order::where([
            ['user_id', '=', Auth::id()],
            ['restaurant_id', '=', $product->restaurant_id],
            ['status', '=', 'Draft'],
        ])->get();
//        //if not found --> new order
        $orderId = 0;
        if (count($order) == 0) {
            $newOrder = Order::create(array_merge(
                [
                    'user_id' => Auth::id(),
                    'restaurant_id' => $product->restaurant_id,
                    'totalPrice' => 0,
                    'discount' => $request->discount,
                    'priceAfterDiscount' => 0,
//                    'priceAfterDiscount' => (totalPrice - (totalPrice *discount / 100)),
                    'status' => 'Draft',
                ]
            ));
            $orderId = $newOrder->id;
        } else {
            $orderId = $order[0]->id;
        }
//        // add to Order_Details table
        return $this->createOrderItem($request, $orderId, $product);
//        return $this->apiResponse($order_details, "Add to cart Done!", 200);

    }

    public function createOrderItem($request, $orderId, $product)
    {
        $item = Order_Details::where([
            ['order_id', '=', $orderId],
            ['product_id', '=', $product->id]
        ])->exists();
        if ($item) {
            return $this->apiResponse([], "Product already exist ", 422);
        } else {
            $order_details = new Order_Details();
            $order_details['order_id'] = $orderId;
            $order_details['product_id'] = $request->product_id;
            $order_details['quantity'] = $request->quantity;
            if ($product->discount == null) {
                $order_details['price'] = $request->quantity * $product->price;
            } else {
                $order_details['price'] = ($request->quantity * ($product->price - ($product->price * $product->discount->discount_percent / 100)));
            }
            $order_details->save();
            return $this->apiResponse($order_details, "Add to cart Done!", 200);
        }

    }

    public function deleteFromCart($id)
    {
        Order_Details::destroy($id);

    }
}

