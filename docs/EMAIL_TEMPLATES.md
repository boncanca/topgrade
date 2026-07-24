# Email Templates Documentation

## Overview

TGLFC uses a centralized email template system with a shared layout and specialized templates for different business events. All emails are styled using inline CSS for maximum email client compatibility.

## Architecture

### Layout
- **`resources/views/mail/layout.blade.php`** — Main email template with header, footer, and styling. All emails extend this layout.

### Email Templates

Located in `resources/views/mail/`:

#### Booking Lifecycle
1. **booking-received.blade.php** — Sent when a booking is initially created
   - Confirms receipt of booking request
   - Displays booking reference, activity, and scheduled date
   - Sets expectations for follow-up contact

2. **booking-confirmed.blade.php** — Sent when booking is confirmed by admin
   - Confirms the booking is locked in
   - Reminds participant to arrive early
   - Provides activity location details

3. **booking-cancelled.blade.php** — Sent when booking is cancelled
   - Confirms cancellation
   - Optionally includes cancellation reason
   - Encourages rebooking in the future

#### Contact/Inquiry
4. **contact-reply.blade.php** — Sent when contact inquiry is received
   - Confirms receipt of inquiry
   - Sets response time expectations (24-48 hours)
   - Provides inquiry reference number

#### Payment
5. **payment-received.blade.php** — Sent when payment is confirmed
   - Confirms payment receipt
   - Displays transaction amount and date
   - References booking details

## Mail Classes

All mail classes are located in `app/Mail/` and use Laravel's Mailable pattern:

- `BookingReceived` — Sends booking-received template
- `BookingConfirmed` — Sends booking-confirmed template
- `BookingCancelled` — Sends booking-cancelled template with optional reason
- `ContactReply` — Sends contact-reply template
- `PaymentReceived` — Sends payment-received template

### Usage Pattern

```php
// Example: Send booking received email
Mail::send(new BookingReceived($booking));

// Or with queue:
Mail::queue(new BookingReceived($booking));
```

Each Mail class:
- Accepts the relevant model(s) in the constructor
- Sets the subject line dynamically (includes reference numbers where applicable)
- Extracts the recipient email from the booking/inquiry data
- Passes all necessary data to the view

## Styling

### CSS Classes
- `.detail-box` — Highlighted information box with left border
- `.button` — Styled call-to-action button (not currently used in templates)
- `.email-header` — Blue header with app name
- `.email-body` — Main content area
- `.email-footer` — Footer with copyright and contact info

### Colors
- Primary Blue: `#1e40af` (header, borders, links)
- Background: `#f5f5f5`, `#f9fafb` (detail boxes)
- Text: `#333`, `#666` (footer)

## Configuration

Email configuration is in `config/mail.php`. Development uses the `log` transport by default (all emails logged instead of sent).

### Environment Variables
- `MAIL_MAILER` — Transport (log, smtp, etc.)
- `MAIL_FROM_ADDRESS` — Sender email address
- `MAIL_FROM_NAME` — Sender name

Current .env:
```
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="TopGrade"
```

## Testing

Email templates can be tested in Pest by mocking the Mail facade:

```php
use Illuminate\Support\Facades\Mail;

test('booking received email is sent', function () {
    Mail::fake();
    
    // Create booking...
    
    Mail::assertSent(BookingReceived::class);
});
```

## Future Enhancements

- [ ] Admin notification emails when bookings are created
- [ ] Reminder emails (24 hours before scheduled date)
- [ ] Invoice/receipt emails with payment details
- [ ] Digest emails for weekly activity summary
- [ ] SMS notifications (separate system)

## Notes

- All email addresses are extracted from booking/inquiry data
- Dates are formatted using standard Laravel date formatting
- Currency amounts are formatted with 2 decimal places
- Booking references are included in all booking-related emails for easy lookup
