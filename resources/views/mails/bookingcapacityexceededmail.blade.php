<!DOCTYPE html>
<html>

<head>
    <title>Email</title>
    <style>
        @font-face {
            font-family: 'font-avenir-next-rounded-regular';
            src: url("{{ asset('/fonts/avenir_next_rounded/Nunito-Regular.ttf') }}") format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'font-avenir-next-rounded-bold';
            src: url('/fonts/avenir_next_rounded/Nunito-Bold.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        .font-avenir-next-rounded-regular {
            font-family: 'font-avenir-next-rounded-regular', sans-serif;
        }

        .font-avenir-next-rounded-bold {
            font-family: 'font-avenir-next-rounded-bold', sans-serif;
        }
    </style>
</head>

<body>
    <div style="background: url('{{ asset('images/home.jpeg') }}') no-repeat center center; background-size: cover; border-radius: 0.375rem; color: rgb(255 255 255 / var(--tw-text-opacity, 1));">
        <div style="margin-left: auto; margin-right: auto; width: 91.666667%; display: flex; flex-direction: column; gap: 2rem; padding-top: 2rem; padding-bottom: 2rem;">
            <div style="font-size: 1.8rem; line-height: 2rem; font-family: 'font-avenir-next-rounded-bold', Arial, sans-serif;">ICONA</div>
            <div style="border-style: solid; border-width: 2px; border-color: rgb(255 255 255 / var(--tw-border-opacity, 1));"></div>
            <div style="padding-top: 2rem; padding-bottom: 2rem; border-style: solid; border-width: 2px; border-color: rgb(255 255 255 / var(--tw-border-opacity, 1)); border-radius: 0.375rem; backdrop-filter: blur(24px);">
                <div style="text-align: center; font-family: 'font-avenir-next-rounded-regular', Arial, sans-serif; font-size: 1.2rem;">The following booking is required to be attended.</div>
                <div style="display: flex; flex-direction: column; gap: 2rem; padding-top: 2rem; padding-bottom: 2rem; margin-left: auto; margin-right: auto; width: 91.666667%; font-family: 'font-avenir-next-rounded-regular', Arial, sans-serif; font-size: 1rem;">
                    <div>Customer Name: Nishant Kedare</div>
                    <div>Email: Nishant Kedare</div>
                    <div>Phone No: 111111111</div>
                    <div>Number of People: 12</div>
                    <div>Booking Date: 12 Jan 2025</div>
                    <div>Slot: 1:00 PM to 2:00 PM</div>
                </div>
                <div style="text-align: center; font-family: 'font-avenir-next-rounded-regular', Arial, sans-serif; font-size: 0.8rem;">This mail has been sent from Icona.</div>
            </div>
        </div>
    </div>
</body>

</html>