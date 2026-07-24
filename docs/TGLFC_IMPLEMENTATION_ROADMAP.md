# TGLFC Implementation Roadmap

## What We've Documented

✅ **TGLFC_REFERENCE_AUDIT.md** — What the React reference project does
✅ **TGLFC_STRUCTURE.md** — Laravel structure mirroring the reference
✅ **TGLFC_ERD.md** — Database schema with Mermaid diagram

---

## Phase Breakdown

### Phase 1: Foundation (ContentType + ContentEntry)

**Models:**
- ContentType
- ContentEntry
- Menu
- MenuItem

**CRUD:**
- Create/read/update/delete pages
- Manage menus
- Seed initial content types (Home, About, Training, Teams, Contact)

**Frontend:**
- Admin pages for managing content
- Public pages render ContentEntry + templates

**Status:** Ready to build

---

### Phase 2: Appointments System

**Models:**
- Service (training programmes)
- Staff (coaches)
- AppointmentSlot (available times)
- Appointment (bookings)
- ServiceStaff (many-to-many)

**Features:**
- Admin: Create/edit services, manage staff, generate slots
- Admin: View/confirm appointments
- Public: Book trial form (3-step workflow)
- Public: Confirmation screen
- Email: Confirmation emails to parent + admin

**Status:** Ready to build after Phase 1

---

### Phase 3: First Feature Validation

**Implement:** Content + Appointments working end-to-end

**Test:** Can admin manage training programmes? Can user book a trial?

**Status:** Ready after Phase 2

---

### Phase 4: Extract Patterns to Volara

**After Phase 3**, analyze:
- What repeated across Content and Appointments?
- What can be generated (resource commands)?
- What should be framework-level?

---

## Recommended Build Order

### Week 1: Phase 1 (Foundation)

```
Day 1-2: Create models + migrations
  □ ContentType migration
  □ ContentEntry migration
  □ Menu migration
  □ MenuItem migration

Day 3: Create controllers + routes
  □ ContentTypeController (index only, not CRUD)
  □ ContentEntryController (CRUD)
  □ MenuController (CRUD)
  □ MenuItemController (CRUD)

Day 4: Create Vue admin pages
  □ pages/Content/Entries/Index.vue
  □ pages/Content/Entries/Create.vue
  □ pages/Content/Entries/Edit.vue
  □ pages/Content/Menus/Index.vue
  □ pages/Content/Menus/Edit.vue

Day 5: Create seeder + test
  □ ContentTypeSeeder
  □ Test viewing published entries
  □ Test admin CRUD
```

### Week 2: Phase 2 (Appointments)

```
Day 1-2: Create appointment models
  □ Service migration
  □ Staff migration
  □ ServiceStaff migration
  □ AppointmentSlot migration
  □ Appointment migration

Day 3: Create appointment controllers
  □ ServiceController (CRUD)
  □ StaffController (CRUD)
  □ AppointmentSlotController (CRUD + generate)
  □ AppointmentController (CRUD + confirm/reject)

Day 4: Create appointment admin pages
  □ pages/Appointments/Services/Index.vue (list)
  □ pages/Appointments/Services/Create.vue
  □ pages/Appointments/Services/Edit.vue
  □ pages/Appointments/Staff/Index.vue
  □ pages/Appointments/Staff/Create.vue
  □ pages/Appointments/Slots/Generate.vue
  □ pages/Appointments/Appointments/Index.vue
  □ pages/Appointments/Appointments/View.vue (confirm/reject)

Day 5: Create public booking form
  □ pages/BookTrial.vue (3-step booking form)
  □ Validation
  □ Submit handler
  □ Confirmation screen
```

### Week 3: Phase 3 (Validation + Polish)

```
Day 1: Create public pages
  □ pages/Index.vue (Home)
  □ pages/About.vue
  □ pages/Training.vue (list services)
  □ pages/Teams.vue
  □ pages/Contact.vue

Day 2-3: Email notifications
  □ Mailable: BookingConfirmation
  □ Send to parent on booking
  □ Send to admin on new booking
  □ Reminder emails (24h before appointment)

Day 4-5: Polish + testing
  □ Test booking workflow end-to-end
  □ Test email notifications
  □ Test admin management
  □ Performance optimization
```

---

## Database Schema Summary

| Table | Rows | Purpose |
|-------|------|---------|
| content_types | ~6 | Page type definitions (Home, About, etc.) |
| content_entries | ~10-50 | Actual page content |
| menus | ~2-3 | Navigation groups (main, footer) |
| menu_items | ~20-30 | Navigation items |
| services | ~3 | Training programmes |
| staff | ~5-10 | Coaches/instructors |
| service_staff | ~10-20 | Service-Staff relationships |
| appointment_slots | ~100+ | Generated available times |
| appointments | ~0-1000 | User bookings |

---

## Key Features to Implement

### Phase 1

- [x] Admin CRUD for pages
- [x] Menu management
- [x] Seed initial content types
- [x] Public page rendering

### Phase 2

- [ ] Service CRUD (admin)
- [ ] Staff CRUD (admin)
- [ ] Slot generation (admin)
- [ ] Appointment CRUD (admin)
- [ ] Booking form (public)
- [ ] Timezone support
- [ ] Email notifications
- [ ] Appointment confirmation workflow

### Phase 3

- [ ] Public pages (Home, About, Training, Teams, Contact)
- [ ] Service listing
- [ ] Dynamic navigation from Menu
- [ ] Booking confirmation screen
- [ ] Admin dashboard with stats

---

## Success Criteria

### Phase 1: Content Management

✅ Admin can create/edit/delete pages
✅ Pages are published/drafted
✅ Menu can be edited
✅ Public pages render with correct content

### Phase 2: Appointment Booking

✅ Admin can create training programmes
✅ Admin can manage coaches
✅ Admin can generate available slots
✅ User can book a trial (3-step form)
✅ User receives confirmation email
✅ Admin receives notification email
✅ Admin can view/confirm/reject bookings

### Phase 3: End-to-End

✅ Public website works (all pages accessible)
✅ Booking workflow works (book → confirm → email)
✅ Admin dashboard shows stats
✅ Navigation updates dynamically from Menu

---

## Technology Stack

### Backend
- Laravel 13
- Eloquent ORM
- Inertia (server-side routing)

### Frontend
- Vue 3
- TypeScript
- Tailwind CSS
- Reka UI components
- Framer Motion (for animations)

### Database
- MySQL/PostgreSQL
- Migrations + Seeders

### Email
- Laravel Mail
- Mailables (BookingConfirmation, BookingNotification, etc.)

---

## Deployment Notes

### Environment Variables Needed

```
MAIL_DRIVER=smtp
MAIL_FROM_ADDRESS=bookings@topgrade.test
MAIL_FROM_NAME="Topgrade London FC"

# Or use Mailhog for local testing
```

### Database

```bash
php artisan migrate
php artisan db:seed
```

---

## Next Step

Ready to start Phase 1?

**Questions before we proceed:**

1. Should we include user authentication for admin access? (Already exists via Fortify)
2. Should appointments require email verification before confirmation?
3. Should we generate slots programmatically or manually?
4. What's the preferred email service? (SMTP, Mailhog for dev?)
