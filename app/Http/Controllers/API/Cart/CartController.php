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
        $itemPrice = 0;

        foreach ($orderDetails as $item) {
            $itemPrice += $item->price;
        }
        $shipping = 5;
        $totalPrice = $itemPrice + $shipping;
        return $this->apiResponse(['items' => $orderDetails, 'item Price ' => $itemPrice, 'itemCount' => $itemCount, 'shipping' => $shipping, 'totalPrice' => $totalPrice], "Your Cart", 200);


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
                    'discount' => $request->discount ? $request->discount : 0,
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
            $order_details['unitPrice'] = $product->price;
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
        return $this->apiResponse([], "Delete Product Done!", 200);

    }

    /*
     public function changeQuntity(Request $request)
       {
           $validator = Validator::make($request->all(), [
               'order_item_id' => 'required|numeric|exists:order_details,id',
               'newQuantity' => 'numeric',
           ]);

           if ($validator->fails()) {
               return $this->apiResponse($validator->errors(), "fails", 422);
           }
           $order_item = Order_Details::find($request->order_item_id);
           if ($order_item) {
               if ($order_item['quantity'] != $request->newQuantity) {
                   $order_item->quantity = $request->newQuantity;
                   $order_item->price = $order_item['unitPrice'] * $request->newQuantity;
                   $order_item->save();
                   return $this->apiResponse($order_item, "update success", 422);
               }
           } else {
               return $this->apiResponse([], "Not found", 422);

           }
       }
*/

    public function checkOutOrderTable()
    {
        $orders = Order::where([
            ['user_id', '=', Auth::id()],
            ['status', '=', 'Draft'],
        ])->get();

        foreach ($orders as $order) {
            $totalPrice = 0;
            $order_details = Order_Details::where('order_id', $order->id)->pluck('price');
            foreach ($order_details as $order_detail) {
                $totalPrice += $order_detail;
            }
            $order->totalPrice = $totalPrice;
            $order->priceAfterDiscount = $order->totalPrice * $order->discount;
            $order->status = 'Waiting';
            $order->save();
        }
    }

    public function checkout(Request $request)
    {
        if(count($request->array) == 0){
            return $this->apiResponse([], "Cart Empty:(", 200);
        }
        foreach ($request->array as $order) {
            $orderDetails = Order_Details::find($order['id']);
            if ($orderDetails) {
                $orderDetails->quantity = $order['quantity'];
                $orderDetails->price = $order['quantity'] * $orderDetails->unitPrice;
                $orderDetails->save();
            } else {
                return $this->apiResponse([], "Order Not Found", 200);
            }
        }
        $this->checkOutOrderTable();
        return $this->apiResponse([], "CheckOut Done", 200);

    }
}

