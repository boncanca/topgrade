# TGLFC Project Structure

Based on the reference React/Shadcn project, here's the Laravel implementation structure.

---

## Database Models

### 1. Service (Training Programme)

```php
// app/Features/Appointments/Models/Service.php

id (PK)
name              // "Mini Kickers", "Development Squad", "Performance Squad"
slug              // "mini-kickers", "development-squad", "performance-squad"
age_group         // "U5–U8", "U9–U12", "U13–U18"
description       // Long description
price             // 5.00, 7.00, 8.00
currency          // "GBP"
location          // "North London"
schedule          // JSON: { days: ["Monday", "Wednesday"], times: ["5:30 PM", "7:00 PM"] }
features          // JSON array: ["Technical skills focus", "Small-sided games", ...]
capacity          // 15 (max students)
is_active         // boolean
created_at
updated_at
```

---

### 2. Staff (Coaches/Instructors)

```php
// app/Features/Appointments/Models/Staff.php

id (PK)
name
email
phone
role              // "coach", "assistant", "admin"
location          // "North London"
bio               // Text description
services          // Many-to-many with Service
availability      // JSON: availability rules
is_active         // boolean
created_at
updated_at
```

---

### 3. Appointment (Booking)

```php
// app/Features/Appointments/Models/Appointment.php

id (PK)
service_id        // FK to Service
parent_name
player_name
email
phone
additional_info   // Medical conditions, experience level
selected_date     // Date of appointment
selected_time     // Time string "09:00 AM"
timezone          // "Europe/London", "America/New_York", etc
status            // "pending", "confirmed", "rejected", "cancelled"
staff_id          // FK to Staff (assigned coach, optional)
notes             // Admin notes
reminder_sent     // boolean
confirmed_at      // timestamp (when confirmed)
created_at
updated_at
```

---

### 4. AppointmentSlot (Available Slots)

```php
// app/Features/Appointments/Models/AppointmentSlot.php

id (PK)
service_id        // FK to Service
staff_id          // FK to Staff (optional, if specific coach)
date              // Date
time              // Time string "09:00 AM"
max_capacity      // How many can book this slot
booked_count      // Current bookings
is_available      // boolean
created_at
updated_at
```

---

### 5. ContentType (Pages) - From Phase 1

```php
// app/Features/Content/Models/ContentType.php

id (PK)
name              // "Home", "About", "Contact"
slug              // "home", "about", "contact"
kind              // "singleton", "collection"
template          // "home", "page", "article"
schema_json       // JSON: future field definitions
is_system         // boolean
is_active         // boolean
created_at
updated_at
```

---

### 6. ContentEntry (Page Instances) - From Phase 1

```php
// app/Features/Content/Models/ContentEntry.php

id (PK)
content_type_id   // FK to ContentType
title
slug
excerpt
content           // Rich HTML/markdown
status            // "draft", "published", "archived"
published_at      // timestamp
metadata_json     // Custom fields
created_at
updated_at
```

---

### 7. Menu (Navigation)

```php
// app/Features/Navigation/Models/Menu.php

id (PK)
name              // "Main Navigation", "Footer Navigation"
slug              // "main", "footer"
location          // "header", "footer", "mobile"
created_at
updated_at
```

---

### 8. MenuItem (Menu Items)

```php
// app/Features/Navigation/Models/MenuItem.php

id (PK)
menu_id           // FK to Menu
parent_id         // FK to MenuItem (for nesting)
content_entry_id  // FK to ContentEntry (optional)
label
url               // External URL (if not linked to content)
target            // "_self", "_blank"
icon              // Icon name (lucide-react compatible)
sort_order        // 0, 1, 2, ... (ordering)
created_at
updated_at
```

---

## Relationships

```
Service (1) ──────── (Many) Appointment
Service (1) ──────── (Many) AppointmentSlot
Service (Many) ───── (Many) Staff

Staff (1) ──────── (Many) Appointment
Staff (1) ──────── (Many) AppointmentSlot

ContentType (1) ──────── (Many) ContentEntry

Menu (1) ──────── (Many) MenuItem
MenuItem (Many) ─── (Many) MenuItem (self-join for nesting)
MenuItem (Many) ─── (One) ContentEntry (optional)
```

---

## Controllers & Routes

### Public Routes (Frontend)

```
GET  /                    → HomeController@index
GET  /about               → PageController@show('about')
GET  /training            → TrainingController@index
GET  /teams               → TeamsController@index
GET  /book-trial          → AppointmentController@create
POST /book-trial          → AppointmentController@store
GET  /contact             → ContactController@create
POST /contact             → ContactController@store
```

### Admin Routes

```
GET    /admin/services              → ServiceController@index
GET    /admin/services/create       → ServiceController@create
POST   /admin/services              → ServiceController@store
GET    /admin/services/{id}/edit    → ServiceController@edit
PATCH  /admin/services/{id}         → ServiceController@update
DELETE /admin/services/{id}         → ServiceController@destroy

GET    /admin/staff                 → StaffController@index
GET    /admin/staff/create          → StaffController@create
POST   /admin/staff                 → StaffController@store
GET    /admin/staff/{id}/edit       → StaffController@edit
PATCH  /admin/staff/{id}            → StaffController@update
DELETE /admin/staff/{id}            → StaffController@destroy

GET    /admin/appointments          → AppointmentController@index
GET    /admin/appointments/{id}     → AppointmentController@show
PATCH  /admin/appointments/{id}     → AppointmentController@update (confirm/reject)

GET    /admin/slots                 → AppointmentSlotController@index
POST   /admin/slots/generate        → AppointmentSlotController@generate

GET    /admin/content               → ContentEntryController@index
GET    /admin/content/create        → ContentEntryController@create
POST   /admin/content               → ContentEntryController@store
GET    /admin/content/{id}/edit     → ContentEntryController@edit
PATCH  /admin/content/{id}          → ContentEntryController@update
DELETE /admin/content/{id}          → ContentEntryController@destroy

GET    /admin/menus                 → MenuController@index
GET    /admin/menus/{id}/edit       → MenuController@edit
PATCH  /admin/menus/{id}            → MenuController@update
```

---

## Frontend Structure (Pages)

### Public Pages (resources/js/pages/)

```
pages/
├── Index.vue                    (Home page - ContentEntry)
├── About.vue                    (About page - ContentEntry)
├── Training.vue                 (Training programmes list - Services)
├── Teams.vue                    (Teams page - ContentEntry)
├── BookTrial.vue                (Booking form - AppointmentController)
├── BookingConfirmation.vue      (Confirmation after submit)
├── Contact.vue                  (Contact form)
└── NotFound.vue                 (404)
```

### Admin Pages (resources/js/pages/Admin/)

```
Admin/
├── Dashboard.vue                (Overview - counts, recent bookings)
├── Services/
│   ├── Index.vue                (List all services)
│   ├── Create.vue               (Create service form)
│   ├── Edit.vue                 (Edit service form)
│   └── View.vue                 (Service details)
├── Staff/
│   ├── Index.vue
│   ├── Create.vue
│   ├── Edit.vue
│   └── View.vue
├── Appointments/
│   ├── Index.vue                (List all appointments)
│   ├── View.vue                 (Appointment details)
│   └── Accept/Reject buttons
├── Slots/
│   ├── Index.vue                (Manage available slots)
│   └── Generate.vue             (Generate slots for a service)
├── Content/
│   ├── Index.vue
│   ├── Create.vue
│   ├── Edit.vue
│   └── View.vue
└── Menus/
    ├── Index.vue
    └── Edit.vue
```

---

## API Response Format

### GET /book-trial

```json
{
  "services": [
    {
      "id": 1,
      "name": "Mini Kickers",
      "age_group": "U5–U8",
      "schedule": "Saturdays 9:00 AM",
      "location": "North London",
      "price": 5.00
    }
  ],
  "timezones": [
    { "value": "Europe/London", "label": "London (GMT/BST)" },
    ...
  ],
  "availableSlots": [
    { "date": "2026-06-20", "times": ["09:00 AM", "10:00 AM", ...] },
    ...
  ]
}
```

### POST /book-trial

```json
{
  "service_id": 1,
  "parent_name": "John Doe",
  "player_name": "Jane Doe",
  "email": "john@example.com",
  "phone": "+44123456789",
  "additional_info": "Has asthma, beginner level",
  "selected_date": "2026-06-20",
  "selected_time": "09:00 AM",
  "timezone": "Europe/London"
}
```

### Success Response

```json
{
  "success": true,
  "message": "Booking received! We'll confirm within 24 hours.",
  "appointment_id": 123,
  "confirmation_number": "APT-20260620-001"
}
```

---

## Seeder Data (DatabaseSeeder)

```php
// database/seeders/TglfcSeeder.php

Services:
- Mini Kickers (U5-U8, Saturdays 9:00 AM, £5)
- Development Squad (U9-U12, Wed 5:30 PM & Sat 10:30 AM, £7)
- Performance Squad (U13-U18, Tue & Thu 6:00 PM, Sun 10:00 AM, £8)

Content Types:
- Home (singleton)
- About (singleton)
- Training (singleton)
- Teams (singleton)
- Contact (singleton)

Menus:
- Main Navigation (header)
- Footer Navigation (footer)

Menu Items:
- Home → /
- About → /about
- Training → /training
- Teams → /teams
- Book a Trial → /book-trial
- Contact → /contact
```

---

## Key Design Decisions

### 1. AppointmentSlot Separate from Service

Instead of:
```
Service has many time slots (just hard-coded)
```

We have:
```
AppointmentSlot = explicit availability + booking capacity
```

**Why:** Allows us to:
- Block off slots (staff unavailable)
- Track bookings per slot
- Generate recurring slots automatically
- Support different staff for different slots

### 2. Service + Staff Many-to-Many

```
Service can have many Staff
Staff can teach many Services
```

**Why:** Real-world coaching often has assistants, subject matter experts, etc.

### 3. AppointmentSlot has staff_id (optional)

```
Some slots = general service
Some slots = specific coach (if preferred)
```

**Why:** Lets users book with preferred coach or just any available coach.

### 4. ContentEntry for Pages

Instead of hardcoding pages:
```
Pages live in ContentEntry with content_type_id
Admin can edit page copy
```

**Why:** Non-technical admins can update page text without touching code.

### 5. Timezone Support

```
Appointment stores user's timezone
Backend can send reminders in their timezone
```

---

## Validation Rules

### Service

```php
name              required|string|max:255
age_group         required|in:U5–U8,U9–U12,U13–U18
price             required|numeric|min:0
location          required|string|max:255
schedule          required|json
features          required|json|array
is_active         boolean
```

### Appointment

```php
service_id        required|exists:services,id
parent_name       required|string|max:255
player_name       required|string|max:255
email             required|email
phone             nullable|phone:AUTO,NG
additional_info   nullable|string|max:1000
selected_date     required|date|after_or_equal:today
selected_time     required|in:09:00 AM,09:30 AM,... (list of valid slots)
timezone          required|timezone
```

### ContentEntry

```php
content_type_id   required|exists:content_types,id
title             required|string|max:255
slug              required|string|max:255|unique:content_entries
content           required|string
status            required|in:draft,published,archived
published_at      nullable|date
```

---

## What This Structure Enables

✅ **Phase 1:** ContentType + ContentEntry + Menu
✅ **Phase 2:** Services + Staff + Appointments
✅ **Phase 3:** Validate with booking workflow
✅ **Phase 4:** Extract patterns into Volara
