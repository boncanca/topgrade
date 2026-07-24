# TGLFC Final Architecture (MVP)

## Core Principle

**Build what the client is paying for. Postpone framework abstractions.**

TGLFC is paying for:
```
✅ Content Website (Home, About, Teams, etc.)
✅ Appointment Booking System
✅ Admin Interface to manage both
```

TGLFC is NOT paying for:
```
❌ Scheduling Engine (AppointmentSlot)
❌ Availability Management
❌ CMS Schema Builder
❌ Payments (Phase 4)
```

---

## Simplified Model List (MVP)

### Content Layer

```php
ContentType      (seeded, read-only)
├── Home
├── About
├── Training
├── Teams
└── Contact

ContentEntry     (admin CRUD)
├── Instances of each type
└── Status: draft, published, archived

Media            (admin CRUD + media browser)
├── Images for pages
├── Coach photos
└── Programme cover images
```

### Navigation Layer

```php
Menu             (admin CRUD)
├── main
├── footer
└── mobile

MenuItem         (admin CRUD, nested)
├── Links to ContentEntry OR external URL
└── Can have children (submenu)
```

### Programmes Layer

```php
Programme        (admin CRUD)
├── name: "Mini Kickers"
├── age_group: "U5–U8"
├── schedule: "Saturdays 9:00 AM"
├── price: 5.00
└── features: JSON array

Staff            (admin CRUD)
├── name
├── email
├── phone
└── bio
```

### Booking Layer

```php
Appointment      (public form + admin CRUD)
├── programme_id
├── parent_name, player_name
├── email, phone
├── requested_date, requested_time
├── timezone
├── status: pending, confirmed, rejected, cancelled, completed
└── notes

ContactSubmission (public form + admin inbox)
├── name, email, phone
├── subject, message
├── status: unread, read, responded
└── responded_at
```

---

## What's NOT in MVP

```
❌ AppointmentSlot (no scheduling engine)
❌ schema_json (ContentType stays simple)
❌ Availability Rules
❌ Google Calendar sync
❌ Payments
❌ Sections/Page Builder
❌ Multi-language
❌ Webhooks
```

These become **Phase 4+** after the booking system proves itself.

---

## Database Tables (9 total)

| Table | Purpose | Admin? | Public? |
|-------|---------|--------|---------|
| content_types | Page definitions (seeded) | No | — |
| content_entries | Page content | Yes | Yes |
| menus | Navigation groups | Yes | Yes |
| menu_items | Navigation items | Yes | Yes |
| media | Images + files | Yes | Yes |
| programmes | Training programmes | Yes | Yes |
| staff | Coaches | Yes | Yes |
| appointments | Bookings | Yes | Yes |
| contact_submissions | Contact form responses | — | Yes |

---

## Models & Fields

### ContentType (Read-Only System Table)

```php
id
name              // "Home", "About", "Training"
slug              // "home", "about", "training"
kind              // singleton, collection
template          // home, page, article
created_at
updated_at
```

**Seeded values:**
```
Home (singleton)
About (singleton)
Training (singleton)
Teams (singleton)
Contact (singleton)
```

---

### ContentEntry

```php
id
content_type_id   // FK → ContentType
title
slug              // unique
excerpt           // optional
content           // rich HTML/markdown
status            // draft, published, archived
published_at      // nullable
metadata_json     // {seo_title, seo_description, og_image, canonical_url}
created_at
updated_at
```

---

### Media

```php
id
disk              // "public", "s3"
path              // "uploads/2026/06/hero.jpg"
original_name     // "hero.jpg"
mime_type         // "image/jpeg"
size              // bytes
alt               // alt text for accessibility
caption           // optional description
created_at
updated_at
```

---

### Menu

```php
id
name              // "Main Navigation", "Footer"
slug              // "main", "footer"
location          // main, footer, mobile (enum)
created_at
updated_at
```

---

### MenuItem

```php
id
menu_id           // FK → Menu
parent_id         // FK → MenuItem (for nesting)
content_entry_id  // FK → ContentEntry (optional)
label
url               // nullable (if not linked to content)
target            // _self, _blank
icon              // lucide icon name
sort_order
created_at
updated_at
```

---

### Programme

```php
id
name              // "Mini Kickers"
slug              // "mini-kickers"
age_group         // "U5–U8", "U9–U12", "U13–U18" (enum)
description       // long text
price             // decimal(8,2)
currency          // "GBP"
location          // "North London"
schedule          // JSON: {days: ["Monday"], times: ["5:30 PM"]}
features          // JSON: ["Technical skills", "Small-sided games"]
capacity          // int (max per session)
is_active         // boolean
created_at
updated_at
```

---

### Staff

```php
id
name
email
phone             // nullable
role              // coach, assistant, admin (enum)
bio               // nullable text
location          // nullable
is_active         // boolean
created_at
updated_at
```

---

### Appointment

```php
id
programme_id      // FK → Programme
staff_id          // FK → Staff (optional, if assigned)
parent_name
player_name
email
phone
additional_info   // medical conditions, experience level (textarea)
requested_date    // the date they want
requested_time    // "09:00 AM", "10:30 AM", etc
timezone          // "Europe/London", "America/New_York"
status            // pending, confirmed, rejected, cancelled, completed (enum)
notes             // admin notes
reminder_sent     // boolean
confirmed_at      // nullable timestamp
created_at
updated_at
```

**Validation:**
```
date >= today
time in: 09:00 AM, 09:30 AM, 10:00 AM, ... (predefined list)
```

---

### ContactSubmission

```php
id
name
email
phone             // nullable
subject
message
status            // unread, read, responded (enum)
responded_at      // nullable
created_at
updated_at
```

---

## Admin Routes

```
GET    /admin/content               → ContentEntryController@index
GET    /admin/content/create        → ContentEntryController@create
POST   /admin/content               → ContentEntryController@store
GET    /admin/content/{id}/edit     → ContentEntryController@edit
PATCH  /admin/content/{id}          → ContentEntryController@update
DELETE /admin/content/{id}          → ContentEntryController@destroy

GET    /admin/media                 → MediaController@index
POST   /admin/media/upload          → MediaController@store
DELETE /admin/media/{id}            → MediaController@destroy

GET    /admin/menus                 → MenuController@index
GET    /admin/menus/{id}/edit       → MenuController@edit
PATCH  /admin/menus/{id}            → MenuController@update

GET    /admin/programmes            → ProgrammeController@index
GET    /admin/programmes/create     → ProgrammeController@create
POST   /admin/programmes            → ProgrammeController@store
GET    /admin/programmes/{id}/edit  → ProgrammeController@edit
PATCH  /admin/programmes/{id}       → ProgrammeController@update
DELETE /admin/programmes/{id}       → ProgrammeController@destroy

GET    /admin/staff                 → StaffController@index
GET    /admin/staff/create          → StaffController@create
POST   /admin/staff                 → StaffController@store
GET    /admin/staff/{id}/edit       → StaffController@edit
PATCH  /admin/staff/{id}            → StaffController@update
DELETE /admin/staff/{id}            → StaffController@destroy

GET    /admin/appointments          → AppointmentController@index
GET    /admin/appointments/{id}     → AppointmentController@show
PATCH  /admin/appointments/{id}     → AppointmentController@update (confirm/reject)

GET    /admin/contact               → ContactSubmissionController@index
GET    /admin/contact/{id}          → ContactSubmissionController@show
PATCH  /admin/contact/{id}          → ContactSubmissionController@update (mark read)
DELETE /admin/contact/{id}          → ContactSubmissionController@destroy
```

---

## Public Routes

```
GET  /                    → ContentEntryController@show('home')
GET  /{slug}              → ContentEntryController@show(slug)

GET  /training            → ProgrammeController@index
GET  /book-trial          → AppointmentController@create
POST /book-trial          → AppointmentController@store

GET  /contact             → ContactSubmissionController@create
POST /contact             → ContactSubmissionController@store
```

---

## Build Phases (Revised)

### Phase 1A: Content Foundation (2 days)

Models:
- ContentType (seeded only)
- ContentEntry
- Menu
- MenuItem

Admin:
- ContentEntry CRUD
- Menu CRUD (nested MenuItems)

Public:
- Static pages work (Home, About, Contact, Teams, Training)

---

### Phase 1B: Programmes & Booking (3 days)

Models:
- Programme
- Staff
- Appointment
- ContactSubmission

Admin:
- Programme CRUD
- Staff CRUD
- Appointment view/confirm/reject
- Contact submissions inbox

Public:
- Programme listing
- Book Trial form (3-step)
- Contact form

---

### Phase 2: Polish & Launch (2 days)

- Email notifications (booking confirmed, contact received)
- Public page templates
- Admin dashboard (stats)
- Mobile responsive
- Testing

---

## What This Removes

```
❌ AppointmentSlot table
❌ schema_json complexity
❌ availability_rules
❌ ServiceStaff pivot (not needed yet)
❌ Calendly integration (reference used it, we can add later)
❌ Payment processing
❌ Sections/page builder
```

**Why:** MVP focuses on demonstrating booking + content, not framework innovation.

---

## Key Insight

ContentType becomes **system configuration**, not user-editable.

Admin interface becomes:

```
Content Entries (what to show)
Menus (how to navigate)
Programmes (what to book)
Appointments (manage bookings)
Contact (respond to inquiries)
```

This is much simpler to understand and maintain.

---

## When Volara Emerges

After this MVP works, patterns will appear naturally:

```
What makes Programme CRUD similar to ContentEntry CRUD?
→ Extract into Resource abstraction
→ php artisan volara:resource Programme

What makes the booking form similar to contact form?
→ Extract into Form abstraction
→ php artisan volara:form

What navigation patterns repeat?
→ Extract into Navigation system
```

But we build the **real code first**, then extract.

---

## Success Criteria (MVP)

✅ Admin can manage pages (Home, About, Contact, etc.)
✅ Admin can manage menus (main, footer)
✅ Admin can manage training programmes
✅ Admin can manage staff
✅ Admin can view/confirm/reject bookings
✅ Users can book a trial (3-step form)
✅ Users can contact via contact form
✅ All pages are public-facing
✅ Email confirmations send
✅ Website is mobile-responsive
