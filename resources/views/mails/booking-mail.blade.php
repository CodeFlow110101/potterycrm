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
                            {{$heading}}
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
                                    <td><strong>Number of People:</strong> {{$booking->no_of_people}}</td>
                                </tr>
                                <tr>
                                    <td><strong>Booking Date:</strong> {{$booking_date}}</td>
                                </tr>
                                <tr>
                                    <td><strong>Slot:</strong> {{$time_slot}}</td>
                                </tr>
                            </table>
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