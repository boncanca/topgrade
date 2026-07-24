# TGLFC Reference Project Audit

## Overview

**TGLFC** is a React + Shadcn UI + Tailwind CSS website for a youth football academy. It's a **public-facing marketing site** with booking functionality, not an admin panel.

**Key insight:** This is the frontend users see, not the admin interface we're building.

---

## Pages & Routes

```
/                  → Index (hero, quick info, CTA to book)
/about             → About school
/training          → Training programmes (3 age groups)
/teams             → Teams/squads
/book-trial        → Booking/appointment form
/contact           → Contact form
```

---

## Key Features for Topgrade

### 1. Training Programmes

**Data Structure:**
```typescript
{
  age: "U5–U8"
  name: "Mini Kickers"
  schedule: "Saturdays 9:00–10:00 AM"
  location: "North London"
  price: "£5 per session"
  features: ["Fun-based introduction", "Ball mastery basics", ...]
}
```

**Note:** 3 age groups (U5-U8, U9-U12, U13-U18)

---

### 2. Book Trial (Booking/Appointment Feature)

**Workflow:**

Step 1: **Select Age Group**
- Choose from 3 programmes
- Shows: name, schedule, location

Step 2: **Select Date & Time**
- Date picker (calendar)
- Timezone selector (5 timezones: London, Paris, NY, LA, Dubai)
- Time slot grid (11 available slots)
- Confirmation text: "✓ Fri, Jun 15 at 10:00 AM (London GMT/BST)"

Step 3: **Enter Details**
- Parent/Guardian Name (required)
- Player Name (required)
- Email (required)
- Phone (optional)
- Additional Info textarea (medical conditions, experience)

**Bonus:** Calendly widget embedded (right sidebar, sticky position)

**Submit:** Shows confirmation screen with "BOOKING RECEIVED!" message

---

## Design Patterns

### Theme System

**Colors:**
- `primary` — Main brand color (purple/magenta)
- `secondary` — Secondary color
- `accent` — Accent highlights (gold/yellow)
- `background` — Page background
- `foreground` — Text
- `card` — Card backgrounds
- `muted` — Muted text
- `border` — Border colors
- `pitch-green` — Custom (for football theme)
- `gold` — Custom (for accents)

**Typography:**
- Display font: `Oswald` (bold headlines)
- Body font: `Inter` (regular text)

**Spacing:** Tailwind defaults

---

### Component Patterns

**Buttons:**
```tsx
<Button variant="hero" size="lg">Book a Trial</Button>
<Button variant="default" size="lg">Submit Booking</Button>
<Button variant="outline">Cancel</Button>
```

**Cards:**
- White/dark background with border
- Rounded corners (lg: var(--radius))
- Shadow for depth

**Form Fields:**
```tsx
<input 
  type="email"
  className="px-3 py-2.5 rounded-lg bg-background border border-border text-foreground"
/>
```

**Motion/Animation:**
- Framer Motion for page transitions
- `initial={{ opacity: 0, y: 20 }}` then `animate={{ opacity: 1, y: 0 }}`
- Staggered animations with `delay`
- `whileInView` for scroll triggers

---

## Booking Feature Details

### Age Groups
```
U5–U8 Mini Kickers
  Schedule: Saturdays 9:00 AM
  Location: North London

U9–U12 Development
  Schedule: Wed 5:30 PM & Sat 10:30 AM
  Location: North London

U13–U18 Performance
  Schedule: Tue & Thu 6:00 PM, Sun 10:00 AM
  Location: North London
```

### Available Time Slots
```
09:00 AM, 09:30 AM, 10:00 AM, 10:30 AM,
11:00 AM, 02:00 PM, 03:00 PM, 04:00 PM,
05:00 PM, 05:30 PM, 06:00 PM
```

### Timezone Support
- Europe/London (GMT/BST)
- Europe/Paris (CET)
- America/New_York (EST)
- America/Los_Angeles (PST)
- Asia/Dubai (GST)

---

## Tech Stack (Reference)

**Frontend:**
- React 18
- React Router v6
- React Hook Form + Zod validation
- Shadcn UI (Radix UI primitives)
- Tailwind CSS
- Framer Motion (animations)
- date-fns (date handling)
- React Query (@tanstack/react-query)
- Lucide React (icons)
- Sonner (toast notifications)

**Build:**
- Vite
- TypeScript
- ESLint + Prettier

---

## What This Tells Us About Laravel Implementation

### 1. Content We Need to Store

**Services/Programmes:**
```
id, age_group, name, schedule, location, price, features
```

**Appointments/Bookings:**
```
id
service_id (which programme)
parent_name, player_name, email, phone
additional_info
selected_date, selected_time
timezone
status (pending, confirmed, rejected)
created_at
```

**Staff/Coaches (for appointments):**
```
id, name, phone, email, location
```

### 2. Frontend Pages Needed (Laravel Inertia)

**Public pages (public website):**
- `/` — Home
- `/about` — About
- `/training` — Training programmes list
- `/teams` — Teams/squads
- `/book-trial` — Booking form
- `/contact` — Contact form

**Admin pages (backend):**
- `/admin/programmes` — Manage training programmes
- `/admin/appointments` — View/manage bookings
- `/admin/staff` — Manage coaches/staff
- `/admin/content` — Manage pages (if using ContentType system)

### 3. Booking Feature Requirements

**Must have:**
- ✅ Age group/programme selection
- ✅ Date picker
- ✅ Time slot selection
- ✅ Timezone support
- ✅ Form with validation
- ✅ Success confirmation
- ✅ Email confirmation (send to user)

**Could have:**
- Calendly integration (theirs does, we could skip for now)
- Admin email notification
- Calendar availability checking
- Booking status tracking

### 4. Design Elements to Use

- Framer Motion for smooth transitions
- Toast notifications (Vue Sonner exists)
- Modal/dialog for confirmation
- Calendar widget (Reka UI has one)
- Form validation with Zod equivalent in Laravel
- Responsive grid layout (left sidebar form, right sidebar widget)

---

## Mapping to Our Phase 1 Architecture

### What TGLFC Uses
```
Training Programmes
Appointments/Bookings
Staff (implied)
```

### What We're Building in Phase 1 (ContentType + ContentEntry)
```
ContentType: "Home", "About", "Training", "Teams", "Contact"
ContentEntry: Instances of each content page
```

### What We'll Build in Phase 2 (Features)
```
Services/Programmes (training programmes)
Staff/Coaches
Appointments (bookings)
```

---

## Key Insights

1. **Timezone support is important** — Users booking internationally
2. **Confirmation UX matters** — Shows selected details before submit
3. **Visual feedback throughout** — Status text, success screen
4. **Mobile-responsive design** — Stacked layout on mobile
5. **Integration-ready** — They embed Calendly, we could do same
6. **Animation-heavy** — Framer Motion used throughout for polish

---

## Next Steps for Our Laravel Version

1. ✅ Build Phase 1: ContentType + ContentEntry (for pages)
2. Build Phase 2: Services, Staff, Appointments (features)
3. Create public frontend pages (Laravel Inertia + Vue)
4. Create admin pages to manage the above
5. Build appointment booking form (date picker, time slots, etc.)
6. Add email notifications
7. Extract patterns into Volara

This reference project shows us what the **public-facing user experience** should be. Our job is to:
- Build the admin panel to manage it
- Build the frontend pages to display it
- Build the booking system backend

---

## Notable Architectural Decisions (For Volara)

1. **Single form submission** — No separate booking page, all on one view
2. **Sticky sidebar** — Right sidebar stays in place while form scrolls
3. **Step-by-step visual guide** — Numbered steps (1. AGE GROUP, 2. DATE & TIME, 3. YOUR DETAILS)
4. **Confirmation before submit** — Shows selected values before form submission
5. **Success screen** — Full page confirmation, not just toast

All of these are UX patterns we should consider for Volara's admin forms too.
