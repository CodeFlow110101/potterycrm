<?php

namespace App\Http\Middleware;

use App\Models\Payment;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VaildatePayment
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->has('com_squareup_pos_CLIENT_TRANSACTION_ID') && Payment::where('transaction_id', $request->query('com_squareup_pos_CLIENT_TRANSACTION_ID'))->doesntExist() && session()->has('cart') && session()->has('checkout_user_id') && session()->has('checkout_coupon_id')) {
            dd(session('cart'), session('checkout_user_id'), session('checkout_coupon_id'));
        }
        dd(Payment::where('transaction_id', $request->query('com_squareup_pos_CLIENT_TRANSACTION_ID'))->doesntExist());

        dd($request->query('com_squareup_pos_CLIENT_TRANSACTION_ID'));
        // session()->forget('cart');

        return $next($request);
    }
}
