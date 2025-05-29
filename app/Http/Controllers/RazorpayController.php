<?php

namespace App\Http\Controllers;
use Razorpay\Api\Api;
use Illuminate\Http\Request;
use Session;
use App\Models\Order;
use App\Models\Product;
use App\Mail\OrderConfirmationMail;
use Illuminate\Support\Facades\Mail;

use Illuminate\Support\Facades\Auth;

class RazorpayController extends Controller
{
    public function index()
    {
        return view('razorpay');
    }

    public function payment(Request $request)
    {
        $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
        $payment = $api->payment->fetch($request->razorpay_payment_id);

        if ($payment->capture(['amount' => $payment->amount])) {

            $product = Product::findOrFail($request->product_id);

            $order = Order::create([
                'user_id'       => Auth::id(),
                'product_id'    => $product->id,
                'address_id'    => $request->address_id,
                'payment_id'    => $payment->id,
                'amount'        => $payment->amount / 100,
                'status'        => 'Paid',
            ]);

            Mail::to(Auth::user()->email)->send(new OrderConfirmationMail($order));

            return redirect()->route('razorpay.index')->with('success', 'Payment successful & email sent!');
        }
    }




}
