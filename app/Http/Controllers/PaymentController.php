<?php

namespace App\Http\Controllers;

use App\Events\TerminalPaymentEvent;
use App\Models\Checkout;
use App\Models\IssuedCoupon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Broadcast;
use Square\SquareClient;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Square\Models\CheckoutOptions;
use Square\Models\CreatePaymentLinkRequest;
use Illuminate\Support\Str;
use Square\Models\OrderLineItemDiscount;

class PaymentController extends Controller
{

    function webhook(Request $request)
    {
        $client = new SquareClient([
            'accessToken' => env('SQUARE_POS_ACCESS_TOKEN'),
            'environment' => env('SQUARE_POS_ENVIRONMENT'),
        ]);

        $payment = $request['data']['object']['payment'];

        if (!isset($payment['status']) || $payment['status'] !== 'COMPLETED') {
            return;
        }

        $api_response = $client->getOrdersApi()->retrieveOrder($payment['order_id']);

        if (!$api_response->isSuccess()) {
            dd($api_response->getErrors());
        }

        if (Purchase::where('order_id', $payment['order_id'])->exists()) {
            return;
        }


        $api_response->getResult()->getorder()->getmetadata() && $this->storeOnlinePurchase($payment);
        $api_response->getResult()->getorder()->getmetadata() || $this->hardwareOnlinePurchase($payment);
    }

    public function hardwareOnlinePurchase($payment)
    {
        $client = new SquareClient([
            'accessToken' => env('SQUARE_POS_ACCESS_TOKEN'),
            'environment' => env('SQUARE_POS_ENVIRONMENT'),
        ]);

        $api_response = $client->getOrdersApi()->retrieveOrder($payment['order_id']);

        if (!$api_response->isSuccess()) {
            dd($api_response->getErrors());
        }

        $orders = $api_response->getResult();

        $checkout = Checkout::find(preg_replace('/\D/', '', collect($orders->getorder()->getlineItems())->first()->getNote()));

        $this->store(collect(json_decode($checkout->cart))->all(), $checkout->user_id, $checkout->coupon_id, $payment, $orders->getorder()->gettenders()[0]->gettransactionId());
    }

    public function storeOnlinePurchase($payment)
    {
        $client = new SquareClient([
            'accessToken' => env('SQUARE_POS_ACCESS_TOKEN'),
            'environment' => env('SQUARE_POS_ENVIRONMENT'),
        ]);

        $api_response = $client->getOrdersApi()->retrieveOrder($payment['order_id']);

        if (!$api_response->isSuccess()) {
            dd($api_response->getErrors());
        }

        $orders = $api_response->getResult();

        $cart = [];

        foreach ($orders->getorder()->getlineItems() as $order) {
            $cart[$order->getmetadata()['id']] = $order->getquantity();
        }

        $this->store($cart, $orders->getorder()->getmetadata()['user_id'], $orders->getorder()->getmetadata()['coupon_id'], $payment, $orders->getorder()->gettenders()[0]->gettransactionId());
    }

    static function onlinePayment($cart, $user, $coupon)
    {
        $products = Product::whereIn('id', array_keys($cart))->get();

        $client = new SquareClient([
            'accessToken' => env('SQUARE_POS_ACCESS_TOKEN'),
            'environment' => env('SQUARE_POS_ENVIRONMENT'),
        ]);

        $all_order_line_item = [];

        foreach ($cart as $id => $quantitiy) {
            $metadata = ['id' => (string)$id];
            $base_price_money = new \Square\Models\Money();
            $base_price_money->setAmount($products->where('id', $id)->first()->price * 100);
            $base_price_money->setCurrency(env('SQUARE_POS_CURRENCY'));

            $order_line_item = new \Square\Models\OrderLineItem($quantitiy);
            $order_line_item->setName($products->where('id', $id)->first()->name);
            $order_line_item->setMetadata($metadata);
            $order_line_item->setBasePriceMoney($base_price_money);
            $all_order_line_item[] = $order_line_item;
        }

        $discounts  = null;
        if ($coupon) {
            $order_line_item_discount = new OrderLineItemDiscount();
            $order_line_item_discount->setPercentage((string)$coupon->discount_value);
            $order_line_item_discount->setName($coupon->discount_value . '% Discount'); // Required field
            $discounts = [$order_line_item_discount];
        }

        $line_items = $all_order_line_item;
        $metadata = ['user_id' => (string)$user->id, 'coupon_id' => (string)($coupon ? $coupon->id : 0)];
        $order = new \Square\Models\Order(env('SQUARE_POS_LOCATION_ID'));
        $order->setLineItems($line_items);
        $order->setMetadata($metadata);
        if ($coupon) {
            $order->setDiscounts($discounts);
        }

        $accepted_payment_methods = new \Square\Models\AcceptedPaymentMethods();
        $accepted_payment_methods->setApplePay(true);
        $checkout_options = new CheckoutOptions();
        $checkout_options->setRedirectUrl(url('cart'));
        $checkout_options->setEnableCoupon(false);
        $checkout_options->setEnableLoyalty(false);
        $checkout_options->setAcceptedPaymentMethods($accepted_payment_methods);


        $body = new CreatePaymentLinkRequest();
        $body->setIdempotencyKey('');
        $body->setOrder($order);

        $body->setCheckoutOptions($checkout_options);
        $api_response = $client->getCheckoutApi()->createPaymentLink($body);

        if ($api_response->isSuccess()) {
            $result = $api_response->getResult();
            return redirect()->away($result->getPaymentLink()->getlongUrl());
        } else {
            $errors = $api_response->getErrors();
            dd(response()->json(['error' => $errors]));
        }
    }

    static function hardwarePayment($amount, $checkout_id)
    {

        $url = null;

        Gate::allows('android') && $url = "intent:#Intent;" .
            "action=com.squareup.pos.action.CHARGE;" .
            "package=com.squareup;" .
            "S.com.squareup.pos.WEB_CALLBACK_URI=" . url('/process-payment') . ";" .
            "S.com.squareup.pos.CLIENT_ID=" . env('SQUARE_POS_APPLICATION_ID') . ";" .
            "S.com.squareup.pos.API_VERSION=v2.0;" .
            "i.com.squareup.pos.TOTAL_AMOUNT=" . $amount * 100 . ";" .
            "S.com.squareup.pos.CURRENCY_CODE=" . env('SQUARE_POS_CURRENCY') . ";" .
            "S.com.squareup.pos.TENDER_TYPES=com.squareup.pos.TENDER_CARD,com.squareup.pos.TENDER_CASH;" .
            "S.com.squareup.pos.NOTE=" . 'Checkout Number: ' . $checkout_id . ";" .
            "end;";

        Gate::allows('apple') && $url = "square-commerce-v1://payment/create?data=" . urlencode(json_encode([
            "amount_money" => [
                "amount" => $amount * 100,
                "currency_code" => env('SQUARE_POS_CURRENCY'),
            ],
            "callback_url" => url('/process-payment'),
            "client_id" => env('SQUARE_POS_APPLICATION_ID'),
            "version" => "1.3",
            "notes" => 'Checkout Number: ' . $checkout_id,
            "options" => [
                "supported_tender_types" => [
                    "CREDIT_CARD",
                    "CASH",
                    "OTHER",
                    "SQUARE_GIFT_CARD",
                    "CARD_ON_FILE"
                ]
            ]
        ]));

        return $url;
    }

    static function store($cart, $user_id, $coupon_id, $payment, $transaction_id)
    {

        $purchasedItems = [];
        foreach ($cart as $id => $quantitiy) {
            for ($i = 1; $i <= $quantitiy; $i++) {
                $purchasedItems[] = ['product_id' => $id];
            }
        }
        $user = User::find($user_id);

        $purchase = $user->purchases()->create([
            'order_id' => $payment['order_id'],
        ]);

        $purchase->payment()->create([
            'payment_id' => $payment['id'],
            'amount' => $payment['amount_money']['amount'],
            'source' => array_key_exists('external_details', $payment) ? $payment['external_details']['source'] : $payment['source_type'],
            'type' => array_key_exists('external_details', $payment) ? $payment['external_details']['type'] : $payment['source_type'],
            'receipt_url' => $payment['receipt_url'],
            'status' => $payment['status'],
            'transaction_id' => $transaction_id,
        ]);

        $purchase->items()->createMany($purchasedItems);

        IssuedCoupon::where('is_used', false)->whereHas('user', function (Builder $query) use ($user) {
            $query->where('id', $user->id);
        })->whereHas('coupon', function (Builder $query) use ($coupon_id) {
            $query->where('id', $coupon_id);
        })->first()?->update([
            'is_used' => true,
            'used_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
}
