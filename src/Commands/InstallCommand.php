<?php

namespace MicaelDias\SingleFileRoutes\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'single-file-routes:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install all of the File Routes resources';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->comment('Publishing Service Provider...');
        $this->callSilent('vendor:publish', ['--tag' => 'single-file-routes-provider']);

        $this->comment('Publishing Configuration...');
        $this->callSilent('vendor:publish', ['--tag' => 'single-file-routes-config']);

        $this->updateNamespaceInConfig();
        $this->registerFileRoutesServiceProvider();

        $this->info('Single File Routes installed successfully.');
    }

    protected function updateNamespaceInConfig(): void
    {
        $configPath = config_path('single-file-routes.php');
        $namespace = Str::replaceLast('\\', '', $this->laravel->getNamespace());

        file_put_contents($configPath, str_replace(
            'namespace App\\Http\\Routes;',
            "namespace {$namespace}\\Http\\Routes;",
            file_get_contents($configPath)
        ));
    }

    protected function registerFileRoutesServiceProvider(): void
    {
        $namespace = Str::replaceLast('\\', '', $this->laravel->getNamespace());

        $configPath = config_path('app.php');
        $appConfig = file_get_contents(config_path('app.php'));

        if (Str::contains($appConfig, $namespace.'\\Providers\\SingleFileRoutesServiceProvider::class')) {
            return;
        }

        file_put_contents($configPath, str_replace(
            "{$namespace}\\Providers\RouteServiceProvider::class,".PHP_EOL,
            "{$namespace}\\Providers\RouteServiceProvider::class,".PHP_EOL."        {$namespace}\Providers\SingleFileRoutesServiceProvider::class,".PHP_EOL,
            $appConfig
        ));

        $providerPath = app_path('Providers/SingleFileRoutesServiceProvider.php');

        file_put_contents($providerPath, str_replace(
            "namespace App\Providers;",
            "namespace {$namespace}\Providers;",
            file_get_contents($providerPath)
        ));
    }
}
