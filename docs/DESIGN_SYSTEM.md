# Volara Design System

Based on Bloom's principles of restraint + clarity, but adapted for admin and public contexts.

---

## Core Philosophy

### Not Filament

No dense admin panels with sidebars, cards, widgets, and layers.

### Not Bloom

Bloom is gorgeous for consumers. But admins need information density without overwhelming visuals.

### Task-First

Every screen answers three questions:

1. What am I doing?
2. What do I do next?
3. What information matters right now?

---

## Design Principles

### Principle 1: One Screen = One Job

Bad:

```
Content Management
├── Statistics
├── Filters
├── Recent Activity
├── Widgets
├── Table
└── Charts
```

Good:

```
Content

[Search]
[Table]
```

**Rule:** If a page can lose 30% of its UI and still accomplish the same task, remove the 30%.

---

### Principle 2: Forms Are The Product

Most of the CMS experience is:

- Create
- Edit
- Publish

Therefore forms deserve design attention, not dashboards.

---

### Principle 3: Tables Fill The Width

Don't nest tables in cards.

```
Page
  Card
     Table
```

becomes:

```
Page
  Table
```

---

### Principle 4: Centering Creates Focus

For forms:

```css
max-w-4xl
mx-auto
```

Never full-width edge-to-edge.

---

### Principle 5: Whitespace Is Structure

Generous margins and gutters create calm and clarity.

Not "wasting" space—using it intentionally.

---

### Principle 6: Every Page Has Three Elements

Every admin page must have:

1. **Title** — What is this page?
2. **Purpose** — Why does this page exist?
3. **Primary Action** — What do I do here?

If any page lacks these, it's incomplete.

Example:

```
Bookings

Manage customer bookings and appointments.

                            [ Create Booking ]
```

---

### Principle 7: Status Is Consistent

Status representation is standardized across all domains.

Mapping:

```
Draft       → Gray badge
Published   → Green badge
Archived    → Amber badge
Pending     → Blue badge
Confirmed   → Green badge
Cancelled   → Red badge
```

Same visual language for:

- Content (Draft/Published/Archived)
- Bookings (Pending/Confirmed/Cancelled)
- Payments (Pending/Completed/Refunded)
- Contacts (New/Read/Responded)

---

### Principle 8: Actions Are Bottom-Right

Form actions always appear bottom-right.

Patterns:

```
Form submission:
                    [Cancel]  [Save]

Page navigation:
                    [Back]  [Next]

Page confirmation:
                    [Delete]  [Save]
```

Never scatter actions.

---

## Layout Specifications

### Page Container (Data/Tables)

```css
max-width: 1200px
margin: auto
padding-inline: 32px
padding-block: 24px
```

### Form Container

```css
max-width: 768px
margin: auto
padding-inline: 32px
padding-block: 40px
```

### Admin Sidebar

```css
width: 240px;
```

Not 300px. Subtle.

---

## Typography

**Font:** Instrument Sans only. No Oswald, Poppins, Montserrat.

### Page Title

```css
font-size: 30px
font-weight: 600
line-height: 1.2
letter-spacing: -0.5px
```

Example: "Content"

---

### Page Description

```css
font-size: 14px
font-weight: 400
color: text-muted
```

Example: "Manage pages and content."

---

### Section Heading

```css
font-size: 16px
font-weight: 600
margin-bottom: 24px
```

---

### Form Label

```css
font-size: 14px
font-weight: 500
margin-bottom: 8px
```

---

### Table Content

```css
font-size: 14px
font-weight: 400
```

---

### Table Header

```css
font-size: 12px
font-weight: 500
uppercase
letter-spacing: 0.5px
```

---

## Spacing Scale

| Purpose                 | Pixels |
| ----------------------- | ------ |
| Between major sections  | 48px   |
| Between form rows       | 24px   |
| Between label and input | 8px    |
| Page padding            | 32px   |
| Sidebar width           | 240px  |

---

## Color

Use the Laravel starter kit / Reka UI token system from `resources/css/app.css`.

`resources/css/app.css` is the single source for:

- Tailwind v4 imports
- theme color variables
- focus ring color
- border/input/sidebar tokens
- Volara layout helper variables

No custom gradients. No fancy shadows.

- `background`
- `foreground`
- `muted`
- `border`
- `primary` (only for primary actions)
- `ring` (only through tokenized focus utilities)

Football colors belong on the public website, not the admin.

Do not hardcode admin action colors such as `bg-blue-*`, `text-blue-*`, `focus:ring-blue-*`, or `focus:border-blue-*`. Use tokens:

```html
focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]
```

Destructive actions use `destructive`, not ad hoc red borders.

---

## Buttons

Use the existing Reka-backed button component:

```ts
import { Button } from '@/components/ui/button';
```

### Primary Button

- Solid background
- Used sparingly
- Examples: "Create Content", "Save", "Publish"

### Secondary Button

- Ghost/outline style
- Used for everything else
- Examples: "Cancel", "Back", "Delete"

Patterns:

```vue
<Button>Save</Button>
<Button variant="outline">Cancel</Button>
<Button variant="destructive">Delete</Button>
```

Do not create one-off button classes in pages unless the shared component cannot express the state.

---

## Form Controls

Prefer existing `resources/js/components/ui/*` Reka wrappers where they exist:

- `Button`
- `Input`
- `Select`
- `Checkbox`
- `Label`
- `Dialog`
- `DropdownMenu`
- `Sheet`
- `Tooltip`

When a wrapper does not exist yet, native controls must still use theme tokens:

```html
border-input bg-background focus-visible:border-ring focus-visible:ring-ring/50
focus-visible:ring-[3px]
```

Validation text uses `text-destructive`.

---

## Primitive Components

Build these first. They determine 80% of the app's feel.

### 1. PageHeader

```vue
<PageHeader title="Content" description="Manage pages and content.">
    <template #actions>
        <Button>Create Content</Button>
    </template>
</PageHeader>
```

**Usage:** Every list, index, and dashboard page.

---

### 2. FormPage

```vue
<FormPage title="Create Content">
    <FormSection title="Basic Information">
        <!-- inputs -->
    </FormSection>
    
    <FormSection title="SEO">
        <!-- inputs -->
    </FormSection>
    
    <div class="flex gap-4 justify-end">
        <Button variant="ghost">Cancel</Button>
        <Button>Save</Button>
    </div>
</FormPage>
```

**Usage:** All create/edit pages.

**Rules:**

- Centered, max-w-4xl
- No cards, use FormSection instead
- Vertical layout, never columns
- Actions at bottom

---

### 3. ResourceTable

```vue
<ResourceTable
    :data="items"
    :columns="columns"
    :loading="loading"
    searchable
    paginated
>
</ResourceTable>
```

**Usage:** All list views.

**Features:**

- Search
- Pagination
- Sorting
- Row actions (edit, delete)
- Selection (bulk actions later)

---

### 4. EmptyState

```vue
<EmptyState
    title="No content yet"
    description="Create your first page."
    icon="File"
>
    <Button>Create Content</Button>
</EmptyState>
```

**Usage:** When no data exists.

---

### 5. FormSection

```vue
<FormSection title="Basic Information" description="Content details.">
    <!-- form fields -->
</FormSection>
```

**Usage:** Grouping related form fields.

No card styling. Just title and spacing.

---

## Admin Navigation

```
Dashboard

CONTENT
  Content
  Menus

BOOKINGS
  Activities
  Bookings

COMMUNICATION
  Contacts

SYSTEM
  Settings
```

Minimal. Task-based, not feature-based.

---

## Public Booking Flow

Much closer to Bloom's linear approach.

### Step 1: Choose Activity

```
Book a Trial

Select an activity:
- Football Trial ($20)
- Advanced Training ($25)
```

---

### Step 2: Choose Date & Time

```
Book a Trial

Select a date and time:

[Calendar] [Time Slots]
```

---

### Step 3: Your Details

```
Book a Trial

Tell us about you:

First Name
Last Name
Email
Phone
```

---

### Step 4: Review

```
Book a Trial

Confirm your booking:

Activity: Football Trial
Date: May 30, 2024
Time: 3:00 PM
Participants: 1

Total: £20
```

---

### Step 5: Payment (if required)

```
Book a Trial

Payment:

[Stripe]
```

---

### Step 6: Confirmation

```
Booking Confirmed!

Your trial is booked.

Confirmation sent to your email.
```

---

## Key Differences from Bloom

Bloom for public booking: **exactly as shown**

Volara admin: **slightly denser**

- Tables show more columns
- Summary panels alongside forms (like Stripe Dashboard)
- Reduced whitespace in admin vs. public

Volara public: **same restrained principle as Bloom**

- One step per screen
- Progress indicator
- Summary panel (visible from step 2+)

---

## What Not To Build

Avoid these patterns:

- ❌ Card inside card inside card
- ❌ Sidebar widgets
- ❌ Dashboard charts and analytics
- ❌ Activity feeds
- ❌ Custom color schemes per page
- ❌ Full-width forms
- ❌ Dense tables with 15+ columns
- ❌ Nested navigation
- ❌ Modal dialogs for simple actions (use pages instead)

---

## What To Build First

1. **PageHeader** — used on every admin list
2. **FormPage** — used on every create/edit
3. **ResourceTable** — used on every index
4. **FormSection** — used in every form
5. **EmptyState** — used when no data

Then:

6. **AdminLayout** — sidebar + header + content
7. **PublicLayout** — minimal header + content
8. **Wizard** — for multi-step booking flow

---

## Typography Example

### Content Index Page

```
Content                                 [+ Create]

Manage pages and content.

[Search ___________]

Title              Status    Published
Home               Draft     —
About Us           Published 2024-06-01
News              Published 2024-06-02

← 1 2 3 →
```

Clean. Simple. One job: manage content.

---

## Form Example

### Create Content

```
Create Content

Create a page, article, or content entry.

Basic Information

Title
[_________________________]

Slug
[_________________________]

Status
[ Draft ]

Content Type
[ Select ]

Content

[_________________________]
[_________________________]
[_________________________]

SEO

SEO Title
[_________________________]

SEO Description
[_________________________]

                    [Cancel]  [Publish]
```

No extra panels. No metadata boxes. No "advanced" sections.

---

## Design Checklist

For every screen:

- [ ] Page header with title and description
- [ ] One primary action (or none)
- [ ] Generous whitespace
- [ ] 14px body text, 30px title
- [ ] Max-width container (never full-width)
- [ ] Centered on page
- [ ] Clear call-to-action
- [ ] Empty state when no data
- [ ] Progress indicator (if multi-step)

If any screen fails this checklist, remove UI until it passes.
