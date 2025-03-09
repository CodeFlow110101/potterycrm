<?php

namespace App\Http\Controllers;

use App\Events\TerminalPaymentEvent;
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
use Square\Models\CheckoutOptions;
use Square\Models\CreatePaymentLinkRequest;
use Illuminate\Support\Str;
use Square\Models\OrderLineItemDiscount;

class PaymentController extends Controller
{

    function webhook(Request $request)
    {
        if ($request->type == "payment.updated") {
            $this->store($request);
        } elseif ($request->type == "terminal.checkout.updated") {
            $this->terminalUpdated($request);
        }
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

    static function terminalPayment($cart, $user, $coupon)
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

        $body = new \Square\Models\CreateOrderRequest();
        $body->setOrder($order);

        $api_response = $client->getOrdersApi()->createOrder($body);

        if ($api_response->isSuccess()) {
            $order_id = $api_response->getResult()->getOrder();
        }

        $amount_money = new \Square\Models\Money();
        $amount_money->setAmount($order_id->getTotalMoney()->getAmount());
        $amount_money->setCurrency(env('SQUARE_POS_CURRENCY'));

        $device_options = new \Square\Models\DeviceCheckoutOptions(env('SQUARE_POS_DEVICE_ID'));
        $device_options->setSkipReceiptScreen(false);
        $device_options->setShowItemizedCart(true);

        $checkout = new \Square\Models\TerminalCheckout($amount_money, $device_options);
        $checkout->setOrderId($order_id->getId());

        $body = new \Square\Models\CreateTerminalCheckoutRequest(Str::uuid(), $checkout);

        $api_response = $client->getTerminalApi()->createTerminalCheckout($body);

        if ($api_response->isSuccess()) {
            $result = $api_response->getResult();
        } else {
            $errors = $api_response->getErrors();
        }
    }


    static function hardwarePayment($cart, $user, $coupon, $amount)
    {

        $url = null;

        Gate::allows('android') && $url = "intent:#Intent;" .
            "action=com.squareup.pos.action.CHARGE;" .
            "package=com.squareup;" .
            "S.com.squareup.pos.WEB_CALLBACK_URI=" . env('SQUARE_POS_WEB_CALLBACK_URI') . ";" .
            "S.com.squareup.pos.CLIENT_ID=" . env('SQUARE_POS_APPLICATION_ID') . ";" .
            "S.com.squareup.pos.API_VERSION=v2.0;" .
            "i.com.squareup.pos.TOTAL_AMOUNT=" . $amount * 100 . ";" .
            "S.com.squareup.pos.CURRENCY_CODE=" . env('SQUARE_POS_CURRENCY') . ";" .
            "S.com.squareup.pos.TENDER_TYPES=com.squareup.pos.TENDER_CARD,com.squareup.pos.TENDER_CASH;" .
            "end;";

        Gate::allows('ios') && $url = null;

        return $url;
    }


    function store(Request $request)
    {
        $payment = $request['data']['object']['payment'];

        if (Purchase::where('order_id', $payment['order_id'])->exists()) {
            return;
        }

        $client = new SquareClient([
            'accessToken' => env('SQUARE_POS_ACCESS_TOKEN'),
            'environment' => env('SQUARE_POS_ENVIRONMENT'),
        ]);

        $api_response = $client->getOrdersApi()->retrieveOrder($payment['order_id']);

        if (!$api_response->isSuccess()) {
            dd($api_response->getErrors());
        }

        $orders = $api_response->getResult();
        $purchasedItems = [];

        foreach ($orders->getorder()->getlineItems() as $order) {
            for ($i = 1; $i <= $order->getquantity(); $i++) {
                $purchasedItems[] = ['product_id' => $order->getmetadata()['id']];
            }
        }

        $user = User::find($orders->getorder()->getmetadata()['user_id']);

        $purchase = $user->purchases()->create([
            'order_id' => $payment['order_id'],
        ]);

        $purchase->payment()->create([
            'payment_id' => $payment['id'],
            'amount' => $payment['amount_money']['amount'],
            'source' => array_key_exists('external_details', $payment) ? $payment['external_details']['source'] : "Terminal Device",
            'type' => array_key_exists('external_details', $payment) ? $payment['external_details']['type'] : "Terminal Device",
            'receipt_url' => $payment['receipt_url'],
            'status' => $payment['status'],
            'transaction_id' => $orders->getorder()->gettenders()[0]->gettransactionId(),
        ]);

        $purchase->items()->createMany($purchasedItems);

        IssuedCoupon::where('is_used', false)->whereHas('user', function (Builder $query) use ($user) {
            $query->where('id', $user->id);
        })->whereHas('coupon', function (Builder $query) use ($orders) {
            $query->where('id', $orders->getorder()->getmetadata()['coupon_id']);
        })->first()?->update([
            'is_used' => true,
            'used_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        Broadcast::on('purchase')->as('admin')->with($request)->sendNow();
        Broadcast::on('order')->as('admin')->with($request)->sendNow();
    }

    function terminalUpdated(Request $request)
    {

        $client = new SquareClient([
            'accessToken' => env('SQUARE_POS_ACCESS_TOKEN'),
            'environment' => env('SQUARE_POS_ENVIRONMENT'),
        ]);

        $api_response = $client->getOrdersApi()->retrieveOrder($request['data']['object']['checkout']['order_id']);
        $user_id = $api_response->getResult()->getorder()->getmetadata()['user_id'];

        TerminalPaymentEvent::dispatch($user_id, $request);
    }
}
