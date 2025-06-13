<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Mail\GiftTrackingUpdateMail;
use App\Models\GiftOrder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::with(['user','orderItems'])->latest()->paginate(30);

        // return $orders;

        return view('admin.order.index',compact('orders'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = Order::with('user')->findOrFail($id);
        $orderItem = OrderItem::where('order_id',$id)->first();
        $product_id = $orderItem->product_id;
        $product = Product::findOrFail($product_id);
        
        return view('admin.order.show', compact('order','orderItem','product'));
    }


    // view gift info
    public function giftInfos(string $id)
    {
        $order = GiftOrder::where('order_id',$id)->first();

        return view('admin.order.gift-info',compact('order'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $order = Order::findOrFail($id);

        // delete order items
        $order->orderItems()->delete();
        // delete gift record
        $order->giftOrders()->delete();

        $order->delete();

        return response(['status' => 'success', 'message' => 'Deleted successfully!']);
    }

    public function pendingGiftOrders()
    {

        $orders = Order::with(['orderItems' => function ($query) {
        $query->where('product_type', 'gift');
        },'user','giftOrders'])->where('status','pending')->get();


        return view('admin.order.pending-order',compact('orders'));
    }

    public function updateGiftOrders(string $id)
    {
        $orderItem = OrderItem::where('order_id',$id)->firstOrFail();

        return view('admin.order.update-gift-order',compact('orderItem'));
    }

    public function updateGiftTrackingId(Request $request, string $id)
    {

        $request->validate([
            'trackingInfo' => ['required']
        ]);

        // update tracking code
         OrderItem::where('id',$id)->update([
            'gift_tracking_id' => $request->trackingInfo
        ]);
        
        // fetch order id
        $orderItem = OrderItem::where('id',$id)->first();
        
        $orderId = $orderItem->order_id;
        
        // update order status
        Order::where('id',$orderId)->update([
           'status' => 'completed'
       ]);

       $user = Order::with('user')->where('id',$orderId)->first()->user;

       // Send the email
        Mail::to($user->email)->send(new GiftTrackingUpdateMail($user->name, $orderId, $request->trackingInfo));

        toastr()->success('Gift Tracking Id Successfully Updated!. Email has being sent.');

        return redirect()->route('admin.pending-gift-orders');
    }
}
