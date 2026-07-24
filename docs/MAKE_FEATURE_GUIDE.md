# make:feature Command Guide

## Overview

The `make:feature` command scaffolds new feature modules with Laravel conventions.

It creates a feature directory structure and generates common components (models, controllers, migrations, etc.) using stubs.

---

## Usage

### Basic Usage (Interactive)

```bash
php artisan make:feature Content
```

You'll be prompted to select which components to create:
- Model? (yes)
- Controller? (yes)
- Migration? (no)
- Action? (no)
- Mail? (no)
- Policy? (no)

### With Flags (Non-Interactive)

```bash
# Create specific components
php artisan make:feature Content --model --controller --migration

# Create all components
php artisan make:feature Booking --all

# Create just a model
php artisan make:feature Contact --model
```

---

## What Gets Created

### Directory Structure

```
app/Features/FeatureName/
├── Models/
│   └── FeatureName.php
├── Controllers/
│   └── FeatureNameController.php
├── Actions/
│   └── CreateFeatureName.php
├── Mail/
│   └── FeatureNameNotification.php
└── Policies/
    └── FeatureNamePolicy.php
```

### Components

#### Model
```php
// app/Features/Content/Models/ContentEntry.php
namespace App\Features\Content\Models;

class ContentEntry extends Model
{
    protected $fillable = [];
    protected $casts = [];
}
```

#### Controller
```php
// app/Features/Content/Controllers/ContentEntryController.php
namespace App\Features\Content\Controllers;

class ContentEntryController extends Controller
{
    public function index() { }
    public function create() { }
    public function store() { }
    public function edit() { }
    public function update() { }
    public function destroy() { }
}
```

#### Action
```php
// app/Features/Content/Actions/CreateContentEntry.php
namespace App\Features\Content\Actions;

class CreateContentEntry
{
    public function execute(array $data): ContentEntry
    {
        return ContentEntry::create($data);
    }
}
```

#### Mailable
```php
// app/Features/Content/Mail/ContentEntryNotification.php
namespace App\Features\Content\Mail;

class ContentEntryNotification extends Mailable
{
    public function envelope(): Envelope { }
    public function content(): Content { }
}
```

#### Policy
```php
// app/Features/Content/Policies/ContentEntryPolicy.php
namespace App\Features\Content\Policies;

class ContentEntryPolicy
{
    public function viewAny(User $user): bool { }
    public function view(User $user, ContentEntry $model): bool { }
    public function create(User $user): bool { }
    public function update(User $user, ContentEntry $model): bool { }
    public function delete(User $user, ContentEntry $model): bool { }
}
```

#### Migration
```bash
php artisan make:migration create_content_entries_table --create=content_entries
```

---

## Feature Naming

The command uses Laravel's `Str` utilities to derive names:

| Feature Name | Model | Controller | Table |
|---|---|---|---|
| Content | Content | ContentController | contents |
| Booking | Booking | BookingController | bookings |
| ContactSubmission | ContactSubmission | ContactSubmissionController | contact_submissions |

---

## Customizing Stubs

### Option 1: Use Custom Stubs

Create stub files in `app/Stubs/Feature/`:

```
app/Stubs/Feature/
├── model.stub
├── controller.stub
├── action.stub
├── mail.stub
└── policy.stub
```

The command will use these instead of defaults.

### Option 2: Edit Default Stubs

The command uses inline stubs. To customize, edit `app/Console/Commands/MakeFeature.php`.

---

## Example: Create a Content Feature

```bash
php artisan make:feature Content --all
```

Creates:

```
app/Features/Content/
├── Models/
│   └── Content.php
├── Controllers/
│   └── ContentController.php
├── Actions/
│   └── CreateContent.php
├── Mail/
│   └── ContentNotification.php
├── Policies/
│   └── ContentPolicy.php
```

Plus a migration:
```bash
database/migrations/2026_06_15_123456_create_contents_table.php
```

---

## Next Steps After Generation

1. **Update the Model**
   ```php
   class Content extends Model
   {
       protected $fillable = ['title', 'slug', 'content', 'status'];
       protected $casts = ['published_at' => 'datetime'];
   }
   ```

2. **Update the Controller**
   ```php
   public function store(Request $request)
   {
       $validated = $request->validate([
           'title' => 'required|string',
           'content' => 'required|string',
       ]);

       Content::create($validated);
       // ...
   }
   ```

3. **Update the Migration**
   ```php
   Schema::create('contents', function (Blueprint $table) {
       $table->id();
       $table->string('title');
       $table->string('slug')->unique();
       $table->longText('content');
       $table->enum('status', ['draft', 'published', 'archived']);
       $table->timestamp('published_at')->nullable();
       $table->timestamps();
   });
   ```

4. **Register Routes**
   ```php
   // routes/web.php
   Route::resource('content', ContentController::class);
   ```

5. **Create Vue Pages**
   ```
   resources/js/pages/Content/
   ├── Index.vue
   ├── Create.vue
   └── Edit.vue
   ```

---

## For TGLFC MVP

### Phase 1A: Content Foundation

```bash
php artisan make:feature Content --model --controller --migration --policy
php artisan make:feature Menu --model --controller --migration --policy
```

### Phase 1B: Booking System

```bash
php artisan make:feature Booking --model --controller --migration --policy --mail --action
php artisan make:feature Contact --model --controller --migration --mail
```

---

## Available Flags

```bash
php artisan make:feature Name [--model] [--controller] [--migration] [--action] [--mail] [--policy] [--all]
```

| Flag | Creates |
|------|---------|
| `--model` | Model class |
| `--controller` | Controller class with CRUD stubs |
| `--migration` | Database migration |
| `--action` | Action class for business logic |
| `--mail` | Mailable class for notifications |
| `--policy` | Authorization policy |
| `--all` | All of the above |

---

## Troubleshooting

### "Feature directory already exists"

Delete or rename the existing feature directory.

### "Migration name already exists"

Laravel prevents duplicate migrations. Use a different feature name or manually adjust the migration timestamp.

### Custom stubs not loading

Ensure stub files are in `app/Stubs/Feature/` with correct names:
- `model.stub`
- `controller.stub`
- `action.stub`
- `mail.stub`
- `policy.stub`

Use placeholders:
- `{{ namespace }}` — Feature namespace
- `{{ class }}` — Class name
- `{{ model }}` — Model class name
- `{{ modelVariable }}` — Camelcase model name
- `{{ modelPath }}` — Full model path

---

## Philosophy

This command follows the principle:

> **Build the application first. Let patterns emerge. Don't over-architect.**

It provides sensible defaults but expects you to customize as needed.

No complexity beyond what you actually use.
