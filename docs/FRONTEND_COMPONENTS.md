# Frontend Components & Theme System

This document outlines the frontend component library and configurable theme system inspired by TGLFC design language.

## Theme System

The theme system is defined in `resources/css/theme.css` and provides TGLFC-inspired colors and utilities that are configurable via CSS custom properties.

### Color Variables

All colors are defined as CSS custom properties in the root and `.dark` selectors:

```css
:root {
  /* Primary - Purple */
  --color-primary: 270 60% 38%;
  --color-primary-light: 270 60% 50%;
  --color-primary-dark: 270 60% 30%;

  /* Secondary - Magenta */
  --color-secondary: 310 50% 45%;
  
  /* Accent - Hot Pink */
  --color-accent: 310 70% 48%;

  /* Semantic */
  --color-success: 120 60% 35%;
  --color-warning: 45 90% 55%;
  --color-destructive: 0 84% 60%;

  /* Dark Mode */
  .dark {
    --color-primary: 270 60% 55%;
    /* ... */
  }
}
```

### Customizing Colors

To customize the theme colors, modify the CSS variables in `resources/css/theme.css`. All colors use HSL format (Hue, Saturation, Lightness) for easy adjustment.

Example: To change primary color from purple to blue:
```css
--color-primary: 210 100% 50%; /* Blue */
```

## Frontend Components

Components are located in `resources/js/components/frontend/` and designed specifically for the public-facing pages (Home, Booking, etc).

### HeroSection

Large banner section with title, subtitle, description, and CTAs.

```vue
<HeroSection
  title="Welcome to Our Platform"
  subtitle="Amazing Experiences"
  description="Discover what we offer"
  :actions="[
    { label: 'Get Started', href: '/booking', variant: 'default' },
    { label: 'Learn More', href: '/about', variant: 'outline' }
  ]"
/>
```

**Props:**
- `title` (required) - Main heading
- `subtitle` - Optional sub-heading above title
- `description` - Body text description
- `backgroundImage` - Optional background image URL
- `actions` - Array of action buttons with label, href, and variant

### FeatureGrid

Display features in a responsive grid with icons.

```vue
<FeatureGrid
  title="Our Features"
  description="What makes us special"
  :features="[
    {
      id: 'feature-1',
      icon: 'la-bolt',
      title: 'Fast',
      description: 'Lightning quick response times'
    }
  ]"
  :columns="3"
  :dark="false"
/>
```

**Props:**
- `title` (required) - Section title
- `description` - Section subtitle
- `features` (required) - Array of feature objects
- `columns` - Grid columns (1, 2, or 3)
- `dark` - Dark background variant

### PricingCard

Individual pricing tier card.

```vue
<PricingCard
  name="Pro"
  description="For professionals"
  :price="99"
  period="month"
  currency="$"
  :features="[
    { label: 'Feature 1', included: true },
    { label: 'Feature 2', included: false }
  ]"
  :cta="{ label: 'Choose Plan', href: '/checkout' }"
  :highlighted="true"
/>
```

**Props:**
- `name` (required) - Plan name
- `description` - Plan subtitle
- `price` (required) - Price amount
- `period` - Billing period (default: 'month')
- `currency` - Currency symbol (default: '$')
- `features` - Array of feature objects with label and included boolean
- `cta` - Call-to-action button config
- `highlighted` - Make this card stand out

### BookingCard

Activity/booking item card with image and booking button.

```vue
<BookingCard
  title="Rock Climbing"
  description="Learn the basics of rock climbing"
  image="/images/climbing.jpg"
  :price="50"
  duration="2 hours"
  participants="1-4 people"
  :tags="['Adventure', 'Outdoor']"
  bookingUrl="/book/climbing"
  :featured="true"
/>
```

**Props:**
- `title` (required) - Activity name
- `description` - Activity description
- `image` - Featured image URL
- `price` - Price amount
- `duration` - Duration string
- `participants` - Participant info
- `tags` - Array of category tags
- `bookingUrl` - Link to booking page
- `featured` - Highlight this card

### TestimonialCard

Customer testimonial/review card.

```vue
<TestimonialCard
  name="John Doe"
  role="Professional Athlete"
  content="This platform changed how I organize my training."
  :rating="5"
  image="/images/john.jpg"
/>
```

**Props:**
- `name` (required) - Author name
- `role` - Author role/title
- `content` (required) - Testimonial text
- `rating` - Star rating (1-5)
- `image` - Author avatar URL

### CTASection

Call-to-action section with title, description, and action buttons.

```vue
<CTASection
  title="Ready to get started?"
  description="Join thousands of users"
  :primaryAction="{ label: 'Get Started', href: '/signup' }"
  :secondaryAction="{ label: 'Learn More', href: '/about' }"
  variant="gradient"
/>
```

**Props:**
- `title` (required) - Section heading
- `description` - Section description
- `primaryAction` - Primary CTA button config
- `secondaryAction` - Secondary CTA button config
- `variant` - 'dark', 'light', or 'gradient'

### FAQ

Accordion-style FAQ section.

```vue
<FAQ
  title="Frequently Asked Questions"
  description="Find answers to common questions"
  :items="[
    {
      id: 'faq-1',
      question: 'How do I get started?',
      answer: 'Simply sign up and create your first booking...'
    }
  ]"
  :dark="false"
/>
```

**Props:**
- `title` (required) - Section title
- `description` - Section subtitle
- `items` (required) - Array of FAQ items
- `dark` - Dark background variant

## Utility Classes

### Layout

```html
<!-- Container centered with max-width -->
<div class="container-center">...</div>

<!-- Flexbox utilities -->
<div class="flex-center">...</div>
<div class="flex-between">...</div>
<div class="flex-col-center">...</div>
```

### Cards

```html
<!-- Basic card -->
<div class="card-base">...</div>

<!-- Card with hover effect -->
<div class="card-hover">...</div>
```

### Sections

```html
<!-- Standard padding -->
<section class="section-base">...</section>

<!-- Dark background section -->
<section class="section-dark">...</section>

<!-- Hero with gradient -->
<section class="section-hero">...</section>
```

### Typography

```html
<h1>Title</h1>
<div class="text-heading">Heading Text</div>
<div class="text-subheading">Subheading</div>
<div class="text-body">Body text</div>
<div class="text-caption">Small caption</div>
```

### Gradients

```html
<!-- Primary gradient background -->
<div class="bg-gradient-primary">...</div>

<!-- Hero gradient -->
<div class="bg-gradient-hero">...</div>

<!-- Accent gradient -->
<div class="bg-gradient-accent">...</div>

<!-- Text gradient -->
<div class="text-gradient">Gradient text</div>
```

### Shadows

```html
<div class="shadow-card">...</div>
<div class="shadow-glow">...</div>
```

## Dark Mode

Dark mode is automatically supported. Components automatically adapt their colors when the `.dark` class is added to the HTML element.

To use dark mode:
```html
<html class="dark">
```

All color variables have dark mode variants defined in `theme.css`.

## Example Page Structure

```vue
<script setup>
import {
  HeroSection,
  FeatureGrid,
  PricingCard,
  CTASection,
  BookingCard
} from '@/components/frontend';
</script>

<template>
  <div>
    <HeroSection
      title="Welcome"
      description="Start your journey"
      :actions="[...]"
    />
    
    <FeatureGrid
      title="Why Choose Us"
      :features="[...]"
    />
    
    <section class="section-base container-center">
      <h2>Our Activities</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <BookingCard
          v-for="activity in activities"
          :key="activity.id"
          :title="activity.name"
          :description="activity.description"
          :price="activity.price"
          :image="activity.image"
        />
      </div>
    </section>
    
    <CTASection
      title="Ready to Book?"
      :primaryAction="{ label: 'Browse Activities', href: '/browse' }"
    />
  </div>
</template>
```

## Integration with Reka UI

All frontend components use Reka UI components internally for buttons and other UI elements. They're styled consistently with the theme system.

Example with Button:
```vue
<script setup>
import { Button } from '@/components/ui/button';
</script>

<template>
  <Button variant="default" size="lg">
    Click Me
  </Button>
</template>
```

## Next Steps

1. Create Home page using these components
2. Create Booking workflow pages
3. Add public navigation header
4. Customize colors in `theme.css` as needed
