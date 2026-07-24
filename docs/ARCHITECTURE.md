# TGLFC Architectural Principles

TGLFC is a **server-driven Inertia application**, not an SPA. This document establishes the patterns we follow to keep the codebase simple, maintainable, and Laravel-native.

---

## Core Pattern

```
HTTP Request
    ↓
Route
    ↓
Controller
    ↓
Action / Service
    ↓
Model
    ↓
Inertia Response or Redirect
```

This is the default. 95% of application logic follows this flow.

---

## Request Types

### Page Request

```php
GET /dashboard/bookings
    ↓
BookingController@index
    ↓
return Inertia::render('Bookings/Index', [...props])
```

User sees the page rendered server-side with data.

### Form Submission

```php
POST /bookings
    ↓
BookingController@store
    ↓
CreateBookingAction
    ↓
Booking::create(...)
    ↓
return redirect()->route('bookings.show', $booking)
    ->with('success', 'Booking created')
```

Vue component uses `form.post(route('bookings.store'))`. Flash message appears via `usePage().props.flash`.

---

## Controllers

Controllers are **thin routers**, not logic containers.

### Pattern

```php
class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::all();
        return Inertia::render('Bookings/Index', [
            'bookings' => $bookings,
        ]);
    }

    public function store(StoreBookingRequest $request)
    {
        $booking = (new CreateBookingAction)->execute($request->validated());
        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Booking created');
    }

    public function update(UpdateBookingRequest $request, Booking $booking)
    {
        (new UpdateBookingAction)->execute($booking, $request->validated());
        return redirect()->back()
            ->with('success', 'Booking updated');
    }
}
```

- Controllers delegate to Actions
- Controllers handle HTTP concerns (routing, requests, responses)
- Controllers never contain business logic

---

## Actions

Actions encapsulate business logic.

### Pattern

```php
class CreateBookingAction
{
    public function execute(array $data): Booking
    {
        $booking = Booking::create($data);
        // Additional logic, validations, related operations
        return $booking;
    }
}
```

### When to Create an Action

- When logic is complex enough to warrant a separate class
- When multiple controllers might need the same operation
- When the operation has side effects (sending emails, updating related records)

### When Actions Aren't Needed

For simple CRUD with no side effects, call the model directly in the controller:

```php
public function destroy(Booking $booking)
{
    $booking->delete();
    return redirect()->back()->with('success', 'Deleted');
}
```

---

## Vue Component Flow

### Form Submission

```vue
<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    parent_name: '',
    player_name: '',
    email: '',
});

const submit = () => {
    form.post(route('bookings.store'));
};
</script>

<template>
    <form @submit.prevent="submit">
        <!-- inputs -->
        <button :disabled="form.processing">Book</button>
    </form>
</template>
```

Success/error messages appear via:

```vue
<script setup>
import { usePage } from '@inertiajs/vue3';

const flash = usePage().props.flash;
</script>

<template>
    <div v-if="flash.success" class="toast">{{ flash.success }}</div>
</template>
```

---

## Vue Emits

Vue emits are **fine for parent-child communication**.

### Acceptable Examples

#### ImageUpload Component

```vue
<ImageUpload @uploaded="onImageUploaded" />
```

#### MenuEditor

```vue
<MenuEditor>
    <MenuItem @delete="removeItem" />
</MenuEditor>
```

**Rule:** Emits stay within component trees. They don't cross page/feature boundaries.

---

## Laravel Events

Laravel Events are **fine for domain events**.

### Use Case

When one business action triggers multiple independent side effects.

### Examples

#### Booking Paid

```php
event(new BookingPaid($booking));
```

Listeners:

```php
SendBookingConfirmation
NotifyAdmin
UpdateAnalytics
AwardCredits
```

#### Contact Submitted

```php
event(new ContactSubmitted($contact));
```

Listeners:

```php
SendContactNotification
LogToSlack
```

### When NOT to Use Events

❌ Don't use events for sequential, dependent operations.

❌ Don't use events as a substitute for service composition.

❌ Don't use global event buses (no `mitt`, no pub/sub, no `emitter.on()` across components).

---

## JSON Endpoints

JSON is acceptable **only for component interactions**, not for application pages.

### Acceptable

#### File Upload

```php
POST /upload
    ↓
UploadController@store
    ↓
return response()->json(['url' => $url])
```

#### Search / Autocomplete

```php
GET /api/content/search?q=home
    ↓
ContentSearchController@search
    ↓
return response()->json([
    ['id' => 1, 'title' => 'Home'],
])
```

#### Async Select Options

```php
GET /api/bookable-items
    ↓
return response()->json(BookableItem::all())
```

### Unacceptable

❌ Don't create a dedicated REST API layer for pages that Inertia renders:

```php
❌ GET /api/bookings
❌ POST /api/bookings
❌ PUT /api/bookings/{id}
❌ DELETE /api/bookings/{id}
```

Instead, use controller actions + redirects:

```php
✅ GET /bookings → BookingController@index → Inertia
✅ POST /bookings → BookingController@store → redirect()
✅ PUT /bookings/{id} → BookingController@update → redirect()
✅ DELETE /bookings/{id} → BookingController@destroy → redirect()
```

---

## Feature Structure

Each feature lives in `app/Features/{FeatureName}/`.

```
app/Features/Booking/
├── Models/
│   ├── BookableItem.php
│   └── Booking.php
├── Controllers/
│   └── BookingController.php
├── Actions/
│   ├── CreateBookingAction.php
│   └── UpdateBookingAction.php
├── Policies/
│   └── BookingPolicy.php
└── Seeders/
    └── BookingSeeder.php
```

---

## Request Validation

Use Form Requests.

```php
class StoreBookingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'parent_name' => 'required|string',
            'player_name' => 'required|string',
            'email' => 'required|email',
            'requested_date' => 'required|date|after:today',
        ];
    }
}
```

Controllers receive validated data:

```php
public function store(StoreBookingRequest $request)
{
    $booking = (new CreateBookingAction)->execute($request->validated());
    // ...
}
```

---

## Authorization

Use Policies and gates.

```php
class BookingPolicy
{
    public function update(User $user, Booking $booking): bool
    {
        return $user->isAdmin() || $user->id === $booking->created_by;
    }
}
```

In controllers:

```php
$this->authorize('update', $booking);
```

---

## Database

### Migrations

- One migration per feature table
- Include indexes for common queries
- Use foreign keys with cascades where appropriate

### Models

- Use type hints and return types
- Define relationships explicitly
- Use casts for type safety

---

## Summary

| Layer | Responsibility | Location |
|-------|---|---|
| **Route** | Define HTTP endpoints | `routes/web.php` |
| **Controller** | Route requests to actions, prepare responses | `app/Features/*/Controllers/` |
| **Action** | Encapsulate business logic | `app/Features/*/Actions/` |
| **Model** | Define data structure and relationships | `app/Features/*/Models/` |
| **Policy** | Authorization logic | `app/Features/*/Policies/` |
| **Request** | Input validation | `app/Http/Requests/` |
| **Event/Listener** | Domain events with multiple side effects | `app/Events/`, `app/Listeners/` |
| **Vue Component** | Render UI, handle user interaction | `resources/js/pages/`, `resources/js/components/` |

---

## What We Don't Do

❌ **No global event buses** (mitt, pub/sub, emitters across pages)

❌ **No REST API layer** for the main Inertia application

❌ **No frontend state management stores** (Pinia, Vuex) for CRUD operations

❌ **No Axios-based architecture** inside Inertia

❌ **No multiple request paths** (API + server-rendered pages for the same resource)

---

## Exceptions

This is a **guideline**, not an absolute rule.

If a future requirement genuinely needs a mobile app, external integration, or public SDK, we'll create a versioned API layer at that time. But it will be separate from the Inertia application, with its own routes, resources, and documentation.

Until then, server-driven simplicity is the default.
