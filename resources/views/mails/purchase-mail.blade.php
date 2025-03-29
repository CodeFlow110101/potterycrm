<!DOCTYPE html>
<html>

<head>
    <title>Email</title>
</head>

<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#f4f4f4">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="background-color: #ffffff; padding: 20px; border-radius: 5px;">
                    <tr>
                        <td align="center" style="font-size: 18px; font-weight: bold; color: #333; padding: 20px 0;">
                            An {{ $payment->gateway->name }} purchase has been made!
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="10" border="0" style="margin-top: 20px;">
                                <tr>
                                    <td><strong>Customer Name:</strong> {{$user->first_name}} {{$user->last_name}}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email:</strong> {{$user->email}}</td>
                                </tr>
                                <tr>
                                    <td><strong>Phone No:</strong> {{$user->phoneno}}</td>
                                </tr>
                                <tr>
                                    <td><strong>Purchase Date:</strong> {{ \Carbon\Carbon::parse($purchase->created_at)->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Amount:</strong>$ {{$payment->amount/100}}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Orders Placed:</strong>
                            <ul>
                                @foreach($orders as $order)
                                <li>{{ $order->product->name }}</li>
                                @endforeach
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="font-size: 14px; color: #888; padding-top: 20px;">
                            This mail has been sent from Icona.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>