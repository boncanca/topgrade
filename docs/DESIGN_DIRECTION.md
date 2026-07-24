# Design Direction: Task-First Admin + Consumer-First Booking

Based on Bloom screenshots and design principles. This is Volara's design philosophy.

---

## The Problem We're Solving

Most admin systems are either:

1. **Filament-style:** Dense, overwhelming, everything visible
2. **Bloom-style:** Beautiful but empty, information hidden

Volara should be **Task-First**: clear what you're doing, focused on the job, no fluff.

---

## Three Contexts

### Admin (Dashboard)
**Goal:** Manage content, bookings, contacts efficiently.

**Feel:** Clean + informative

**Density:** Moderate (more than Bloom, less than Filament)

**Example:**
- Page title + description
- Search bar
- Full-width table
- Row actions (edit/delete)

---

### Forms (Create/Edit)
**Goal:** Enter data clearly.

**Feel:** Centered, focused

**Density:** Low (lots of whitespace)

**Example:**
- Centered max-w-4xl
- Clear section titles
- One form field per row
- Submit buttons at bottom

---

### Public Booking (Consumer)
**Goal:** Book an appointment step-by-step.

**Feel:** Bloom-like

**Density:** Very low (generous whitespace)

**Example:** Exactly the Headshot Portland flow shown.

---

## What Bloom Gets Right (Copy This)

✅ **Massive whitespace** — breathing room creates calm

✅ **Single focus per screen** — one question answered

✅ **Clear progress** — "Step 2 of 6"

✅ **Typography hierarchy** — title stands out

✅ **Generous margins** — not cramped

---

## What Filament Gets Right (Learn This)

✅ **Information density** — show what's needed

✅ **Keyboard navigation** — power user friendly

✅ **Rich interactions** — bulk actions, inline editing

(But don't copy Filament's visual style—too many cards/widgets)

---

## What Volara Gets (Best of Both)

✅ **Admin:** Moderate density + clean design

✅ **Forms:** Centered, focused, distraction-free

✅ **Booking:** Bloom-like simplicity + clarity

✅ **Typography:** Inter only, consistent hierarchy

✅ **Spacing:** Generous gutters, intentional margins

❌ **NO:** Nested cards, sidebars with widgets, custom colors everywhere

---

## Visual Examples

### Bad Admin Page

```
┌─────────────────────────────────────┐
│ Dashboard                           │
├─────────────────────────────────────┤
│ Sidebar                             │
│ ├── Content    │ ┌────────────────┐ │
│ ├── Bookings   │ │  Statistics    │ │
│ ├── Contacts   │ │  ┌──────────┐  │ │
│ └── Settings   │ │  │          │  │ │
│                │ │  └──────────┘  │ │
│                │ └────────────────┘ │
│                │ ┌────────────────┐ │
│                │ │  Widgets       │ │
│                │ └────────────────┘ │
│                │ ┌────────────────┐ │
│                │ │  Table         │ │
│                │ └────────────────┘ │
└─────────────────────────────────────┘
```

**Problems:** Everything competes. Sidebar huge. Cards everywhere.

---

### Good Admin Page

```
┌─────────────────────────────────────┐
│  Sidebar (240px)  │ Content          │
│                   │                  │
│ Content           │  Content         │
│ Navigation        │  Manage pages.   │
│ Bookings          │               [+] │
│ Contacts          │                  │
│                   │  [Search...]     │
│                   │                  │
│                   │  Title   Status  │
│                   │  ────────────── │
│                   │  Home    Draft  │
│                   │  About   Pub    │
│                   │  News    Pub    │
│                   │                  │
│                   │  [← 1 2 3 →]    │
└─────────────────────────────────────┘
```

**Good:** Clear hierarchy. Table is the focus. Sidebar subtle.

---

## Color Reference

Use Reka UI defaults. Example for light mode:

- **Background:** white
- **Foreground:** black
- **Muted foreground:** gray-500
- **Border:** gray-200
- **Primary:** your brand color (used sparingly)

No custom gradients. No extra colors.

---

## Typography Reference

All examples assume Inter font.

```
Page Title (h1)
30px, semibold
Color: foreground

Page Description (p)
14px, regular
Color: muted-foreground

Section Title (h2)
16px, semibold
Color: foreground

Input Label (label)
14px, medium
Color: foreground

Body Text (p)
14px, regular
Color: foreground

Table Header (th)
12px, medium, uppercase
Color: muted-foreground

Table Cell (td)
14px, regular
Color: foreground
```

---

## Spacing Reference

Margins and padding in the design:

```
Page container:
  - horizontal padding: 32px
  - vertical padding: 24px

Form container:
  - horizontal padding: 32px
  - vertical padding: 40px

Between major sections:
  - 48px

Between form fields:
  - 24px

Between label and input:
  - 8px

Table row height:
  - 52px
```

---

## Component Examples

### PageHeader

```
Title
Description

                                    [Primary Action]
```

### FormPage (Create Content)

```
Create Content
Create a page, article, or content entry.

Basic Information

Title
[________________]

Slug
[________________]

Content Type
[Select▼]


Content

[________________________________]
[________________________________]
[________________________________]


SEO

SEO Title
[________________]

SEO Description
[________________]


                        [Cancel]  [Create]
```

### ResourceTable (Content Index)

```
Content
Manage pages and content.

                                    [+ Create]

[Search ___________]

Title           Status    Published
─────────────────────────────────────
Home            Draft     —
About           Published Jun 1
Blog            Published Jun 2

[← 1 2 3 →]
```

### EmptyState

```
[Icon]

No content yet

Create your first page.

[+ Create Content]
```

---

## Public Booking Flow

Linear, step-by-step. Very similar to Headshot Portland's design.

### Step 1: Activity Selection

```
Book a Trial

What would you like to book?

┌─────────────────────────────┐
│ Football Trial              │
│ 60 mins • £20               │
│ North London                │
└─────────────────────────────┘

┌─────────────────────────────┐
│ Advanced Training           │
│ 90 mins • £25               │
│ North London                │
└─────────────────────────────┘

                    [Continue]
```

---

### Step 2: Date & Time

```
Book a Trial
Step 2 of 6

Select a date and time

[Calendar]                    [Times]
                               8:00am
                               9:00am
May 2024                       10:00am
                               11:00am
[Calendar grid]                ...

                    [Back]  [Continue]
```

---

### Step 3: Your Details

```
Book a Trial
Step 3 of 6

Tell us about you

First Name
[_______________]

Last Name
[_______________]

Email
[_______________]

Phone
[_______________]

                    [Back]  [Continue]
```

---

### Step 4: Review

```
Book a Trial
Step 4 of 6

Confirm your booking

Left Side:               Right Side:
                         
Activity                 Football Trial
Date                     May 30, 2024
Time                     3:00 PM
Participants             1
Timezone                 UTC

Total: £20

[Edit]

                    [Back]  [Confirm]
```

---

### Step 5: Payment (if requires_payment = true)

```
Book a Trial
Step 5 of 6

Payment

[Stripe form]

Summary on right:
Date: May 30
Time: 3:00 PM
Total: £20.00

                    [Cancel]  [Pay £20]
```

---

### Step 6: Confirmation

```
Booking Confirmed! ✓

Your trial is booked.

Football Trial
May 30, 2024 at 3:00 PM
North London

Confirmation sent to user@example.com

[View Calendar]  [Done]
```

---

## Implementation Rules

1. **Never full-width forms**
   - Max-width: 768px on forms
   - Centered on page

2. **Tables are primary**
   - Not nested in cards
   - Fill available width

3. **Whitespace is intentional**
   - Not "empty"
   - Breathing room

4. **One job per page**
   - Not dashboards
   - Not widgets
   - One task at a time

5. **Forms own the experience**
   - Most time spent creating/editing
   - Design accordingly

6. **Typography matters more than components**
   - Clear hierarchy = understands structure
   - Font choices: Inter only

7. **Admin vs. Booking**
   - Admin: task-first, moderate density
   - Booking: consumer-first, Bloom-like

---

## Red Flags (Don't Do This)

🚫 Nested cards
🚫 Sidebar with widgets
🚫 Dashboard with stats
🚫 Full-width forms
🚫 Multiple fonts
🚫 Fancy gradients
🚫 Modal dialogs for forms
🚫 Tables in cards
🚫 Complex layouts
🚫 Footer in pages (just let content end)

---

## Green Flags (Do This)

✅ Clean page title
✅ Clear description
✅ One primary action
✅ Generous margins
✅ Simple table
✅ Centered forms
✅ Consistent spacing
✅ Clear buttons
✅ Empty states
✅ Progress indicators (bookings)

---

## Volara > Filament Because

- Not overwhelming with options
- Task-focused design
- Minimal learning curve
- Mobile-friendly defaults
- No vendor lock-in to Nova/Filament
- Reusable components for clients

---

## Volara > Bloom Because

- Admin density (Bloom too empty for admins)
- Information visible (Bloom hides everything)
- Power user friendly (Bloom linear only)
- Bulk actions (Bloom single item)
- Extensible (Bloom is a product, not a framework)

---

## Summary

**Task-First Design** means:
- What am I doing? (clear title)
- What do I do next? (obvious next action)
- What matters right now? (focused content)

Apply this to every screen.

When in doubt: remove UI.
