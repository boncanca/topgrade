<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeFeature extends Command
{
    protected $signature = 'make:feature {name : The name of the feature}
                                        {--models : Create models only}
                                        {--models-migrations : Create models and migrations}
                                        {--models-migrations-seeder : Create models, migrations, and seeder}
                                        {--seeder : Create seeder only}
                                        {--models-seeder : Create models and seeder}
                                        {--all : Create models, migrations, controller, policy, and seeder}';

    protected $description = 'Create a new feature scaffold';

    protected Filesystem $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    public function handle()
    {
        $name = $this->argument('name');
        $featurePath = app_path("Features/{$name}");

        // Determine what to create based on flags
        $components = $this->determineComponents();

        if (empty($components)) {
            $components = ['model', 'controller', 'migration', 'policy'];
        }

        // Create feature directory
        $this->files->ensureDirectoryExists($featurePath);
        $this->createDirectories($featurePath, $components);

        // Create components
        if (in_array('model', $components)) {
            $this->createModel($name, $featurePath);
        }

        if (in_array('migration', $components)) {
            $this->createMigration($name);
        }

        if (in_array('controller', $components)) {
            $this->createController($name, $featurePath);
        }

        if (in_array('policy', $components)) {
            $this->createPolicy($name, $featurePath);
        }

        if (in_array('seeder', $components)) {
            $this->createSeeder($name);
        }

        $this->printSummary($name, $components);
    }

    protected function determineComponents(): array
    {
        if ($this->option('all')) {
            return ['model', 'migration', 'controller', 'policy', 'seeder'];
        }

        if ($this->option('models-migrations-seeder')) {
            return ['model', 'migration', 'seeder'];
        }

        if ($this->option('models-migrations')) {
            return ['model', 'migration'];
        }

        if ($this->option('models-seeder')) {
            return ['model', 'seeder'];
        }

        if ($this->option('models')) {
            return ['model'];
        }

        if ($this->option('seeder')) {
            return ['seeder'];
        }

        return [];
    }

    protected function createDirectories(string $featurePath, array $components): void
    {
        $directories = [
            'model' => 'Models',
            'controller' => 'Controllers',
            'policy' => 'Policies',
            'seeder' => 'Seeders',
        ];

        foreach ($components as $component) {
            if (isset($directories[$component])) {
                $this->files->ensureDirectoryExists("{$featurePath}/{$directories[$component]}");
            }
        }
    }

    protected function createModel(string $featureName, string $featurePath): void
    {
        $modelName = Str::singular($featureName);
        $modelPath = "{$featurePath}/Models/{$modelName}.php";

        $namespace = "App\\Features\\{$featureName}\\Models";

        $stub = <<<'PHP'
<?php

namespace {{ namespace }};

use Illuminate\Database\Eloquent\Model;

class {{ class }} extends Model
{
    protected $fillable = [];

    protected $casts = [];
}
PHP;

        $content = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$namespace, $modelName],
            $stub
        );

        $this->files->put($modelPath, $content);
        $this->line("  <fg=green>✓</> Model: Models/{$modelName}.php");
    }

    protected function createMigration(string $featureName): void
    {
        $tableName = Str::snake(Str::plural($featureName));
        $migrationName = "create_{$tableName}_table";

        $this->call('make:migration', [
            'name' => $migrationName,
            '--create' => $tableName,
        ]);
    }

    protected function createController(string $featureName, string $featurePath): void
    {
        $controllerName = Str::singular($featureName).'Controller';
        $controllerPath = "{$featurePath}/Controllers/{$controllerName}.php";

        $namespace = "App\\Features\\{$featureName}\\Controllers";
        $modelName = Str::singular($featureName);

        $stub = <<<'PHP'
<?php

namespace {{ namespace }};

use App\Http\Controllers\Controller;

class {{ class }} extends Controller
{
    //
}
PHP;

        $content = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$namespace, $controllerName],
            $stub
        );

        $this->files->put($controllerPath, $content);
        $this->line("  <fg=green>✓</> Controller: Controllers/{$controllerName}.php");
    }

    protected function createPolicy(string $featureName, string $featurePath): void
    {
        $policyName = Str::singular($featureName).'Policy';
        $policyPath = "{$featurePath}/Policies/{$policyName}.php";

        $namespace = "App\\Features\\{$featureName}\\Policies";

        $stub = <<<'PHP'
<?php

namespace {{ namespace }};

use App\Models\User;

class {{ class }}
{
    //
}
PHP;

        $content = str_replace(
            ['{{ namespace }}', '{{ class }}'],
            [$namespace, $policyName],
            $stub
        );

        $this->files->put($policyPath, $content);
        $this->line("  <fg=green>✓</> Policy: Policies/{$policyName}.php");
    }

    protected function createSeeder(string $featureName): void
    {
        $seederName = Str::singular($featureName).'Seeder';
        $seederPath = "database/seeders/{$seederName}.php";

        $namespace = 'Database\Seeders';
        $modelNamespace = "App\\Features\\{$featureName}\\Models\\".Str::singular($featureName);

        $stub = <<<'PHP'
<?php

namespace {{ namespace }};

use {{ modelNamespace }};
use Illuminate\Database\Seeder;

class {{ class }} extends Seeder
{
    public function run(): void
    {
        // Add seeding logic here
    }
}
PHP;

        $content = str_replace(
            ['{{ namespace }}', '{{ modelNamespace }}', '{{ class }}'],
            [$namespace, $modelNamespace, $seederName],
            $stub
        );

        $this->files->put($seederPath, $content);
        $this->line("  <fg=green>✓</> Seeder: seeders/{$seederName}.php");
    }

    protected function printSummary(string $name, array $components): void
    {
        $this->newLine();
        $this->info("✅ Feature [{$name}] scaffolded");
        $this->info("📁 Location: app/Features/{$name}");
        $this->newLine();

        if (! empty($components)) {
            $this->line('Generated:');
            foreach ($components as $component) {
                match ($component) {
                    'model' => $this->line('  • '.Str::singular($name).' model'),
                    'migration' => $this->line('  • create_'.Str::snake(Str::plural($name)).'_table migration'),
                    'controller' => $this->line('  • '.Str::singular($name).'Controller'),
                    'policy' => $this->line('  • '.Str::singular($name).'Policy'),
                    'seeder' => $this->line('  • '.Str::singular($name).'Seeder'),
                    default => null,
                };
            }
        }
    }
}
