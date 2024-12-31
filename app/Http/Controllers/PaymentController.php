<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Square\SquareClient;

class PaymentController extends Controller
{
    function store(Request $request)
    {
        $payment = $request['data']['object']['payment'];

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
            $purchasedItems[] = ['product_id' => $order->getmetadata()['id'], 'quantity' => $order->getquantity()];
        }

        $user = User::find($orders->getorder()->getmetadata()['user_id']);

        $purchase = $user->purchases()->create([
            'order_id' => $payment['order_id'],
        ]);

        $purchase->payment()->create([
            'payment_id' => $payment['id'],
            'amount' => $payment['amount_money']['amount'],
            'source' => $payment['external_details']['source'],
            'type' => $payment['external_details']['type'],
            'receipt_url' => $payment['receipt_url'],
            'status' => $payment['status'],
            'transaction_id' => $orders->getorder()->gettenders()[0]->gettransactionId(),
        ]);

        $purchase->items()->createMany($purchasedItems);

        Broadcast::on('purchase')->as('admin')->with($request)->sendNow();
        Broadcast::on('order')->as('admin')->with($request)->sendNow();
    }
}
