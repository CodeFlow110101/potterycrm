<?php

return [
  'faq' => [
    "What types of pottery items do you offer?" => "We offer a diverse range of pottery items ideal for painting, including mugs, plates, bowls, vases, and seasonal items. Each piece is meticulously selected to enhance your painting experience.",

    "How do the DIY pottery kits work?" => "Our DIY pottery kits come with everything you need to create your masterpiece at home. Each kit includes the pottery item, paints, brushes, and easy-to-follow instructions. Depending on the kit you choose, you can either return it to us for firing or let it dry at home to keep as is.",

    "How can I order a pottery kit online?" => "You have two options for ordering online:\n
    • Option 1: Paint and Fire\n
      1. Order Your Kit: Visit our ‘Shop’ tab and order your DIY pottery painting kit. Be sure to select the 'Fire Option' during your purchase. Each kit includes all the essentials you need to paint at home.\n
      2. Book Your Pickup: Call us on 0450 131 936 to schedule a time to pick up your kit from our shop.\n
      3. Paint at Your Leisure: At home, take your time to paint your pottery with the colours and designs you prefer.\n
      4. Drop It Off: Once you've completed your masterpiece, bring it back to our shop for firing.\n
      5. We Fire It: We handle all the glazing and firing, ensuring your pottery turns out perfectly.\n
      6. Pick Up: We’ll notify you when your pottery is ready. Call us to schedule a pickup time, then stop by to collect and enjoy your handiwork!\n
    • Option 2: Paint and Display\n
      1. Order Your Kit: Choose the 'Non-Fire' option from our ‘Shop’ tab. This kit includes everything you need, including special paints that do not require firing.\n
      2. Book Your Pickup: Call us on 0450 131 936 to schedule a time to pick up your kit from our shop.\n
      3. Paint at Your Leisure: Relax and enjoy painting your pottery at home with our decorative paints.\n
      4. Let It Dry: Allow your pottery to fully dry at home—no firing needed.\n
      5. Display It: Place your finished piece around your home or give it as a gift. Enjoy your artwork without any additional steps!",

    "How long does shipping take?" => "Orders are processed within 2–3 business days, with delivery typically within 8–10 business days. Delivery times may vary depending on your location.",

    "Do you offer gift cards or vouchers?" => "Yes, we provide gift cards and vouchers that make excellent gifts for friends and family. They can be used both online and in-store.",

    "Are your paints and materials safe?" => "Absolutely! All our paints and materials are non-toxic, water-based, and safe for all ages, including children.",

    "Do you offer group bookings or events?" => "Yes, we cater for group bookings such as birthdays, date nights, and family gatherings. Contact us for more details or to book a table.",

    "Can I book a table to paint in-store?" => "Yes, bookings can be made via our website. Walk-ins are welcome, but reservations are recommended to secure your spot.",

    "How much does it cost to paint pottery in-store?" => "The cost varies depending on the pottery item you select. Each item's price includes paints and studio time.",

    "Do you have a loyalty or membership program?" => "Yes, we offer a loyalty program that rewards regular customers. Earn points with every purchase and enjoy exclusive discounts and perks.",

    "Do you offer corporate or large event packages?" => "Yes, we offer customised packages for corporate events, school activities, and other large gatherings. Please contact us to discuss your needs.",

    "Who can I contact for additional questions or support?" => "Our customer service team is available to help with any questions or special requests. Reach out via email or phone on " . env("TWILIO_PHONE_COUNTRY_CODE") . env("ADMIN_PHONE_NO") . ".",
  ],


  'booking-1-message' => "Your booking is not confirmed yet, we will confirm it shortly!",
  'booking-2-message' => "Your booking has been successfully confirmed. We look forward to welcoming you at Icona Pottery Cafe. Feel free to text or call us on " . env("TWILIO_PHONE_COUNTRY_CODE") . env("ADMIN_PHONE_NO") . " if you have any questions.",
  'booking-3-message' => "Your booking is now active!",
  'booking-4-message' => "Thank you for being an amazing customer at Icona Pottery Cafe! We truly appreciate you.",
  'booking-5-message' => "We regret to inform you that your booking has been cancelled. Please text or call  us on " . env("TWILIO_PHONE_COUNTRY_CODE") . env("ADMIN_PHONE_NO") . " if you have any questions or need further assistance.",
  'booking-day-before-reminder-message' => "Hi {first name}, just a friendly reminder of your booking at Icona Pottery Café tomorrow at {time}. We’re excited to have you! Let us know if you need to reschedule.",
  'booking-hour-before-reminder-message' => "Hi {first name}, your booking at Icona Pottery Café is at {time} today. Can’t wait to see you! 188 Sir Donald Bradman Drive, Cowandilla",
  'booking-day-after-reminder-message' => "Hi {first name}, hope you had fun at Icona Pottery Café yesterday! We’d love to see your painted piece in action — tag us in your Instagram story and show it off, and we’ll give you 10% off your next visit. Just show us the tag at the counter. Also, if you’ve got a sec, we’re always looking to improve. Was there anything we could’ve done better? We take feedback seriously and really appreciate your thoughts. Thanks again for visiting!",
  'coupon-issued-message' => "Here's a special discount just for you: {coupon code}. Show this message to our staff on your next visit to redeem. Offer valid for {validity} days. See you soon!",
  'confirmation-code-message' => 'Your confirmation code is {confirmation code}.',
  'admin-booking-alert-mail-subject-1' => 'Booking Capacity Exceeded',
  'admin-booking-alert-mail-subject-2' => 'New Booking Recieved',
  'admin-booking-1-day-before-alert-message' => 'A customer has a booking tomorrow. Customer name: {full name}',
  'admin-booking-1-hour-before-alert-message' => 'A customer has a booking in an hour. Customer name: {full name}',
  'purchase-message' => "Thank you for your order at Icona Pottery Cafe! Your payment has been received, and we’ll start preparing your order. Need help? Message or call " . env("TWILIO_PHONE_COUNTRY_CODE") . env("ADMIN_PHONE_NO") . ".",
  'order-status-message-1' => "Your painted item (ID: {id}) is now prepared for the firing stage at Icona Pottery Cafe.",
  'order-status-message-2' => "Your painted item (ID: {id}) is currently undergoing the firing process at Icona Pottery Cafe.",
  'order-status-message-3' => "The firing process for your painted item (ID: {id}) is complete at Icona Pottery Cafe",
  'order-status-message-4' => "Hello from Icona Pottery Cafe! Your painted item (ID: {id}) is now complete and ready for pickup. It will be reserved for the next 7 days. If you need extra time, please text or call us on " . env("TWILIO_PHONE_COUNTRY_CODE") . env("ADMIN_PHONE_NO") . ". Thank you for choosing Icona Pottery Cafe!",
];
