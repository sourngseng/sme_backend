<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        require_once app_path('Helpers/LanguageHelper.php');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Custom Blade directive for TraitLanguage translation
        \Illuminate\Support\Facades\Blade::directive('transLabel', function ($expression) {
            return "<?php echo app(\\App\\Services\\LanguageService::class)->transLabel({$expression}); ?>";
        });

        // Register Policies for Object-Level Security
        \Illuminate\Support\Facades\Gate::policy(\App\Models\School::class, \App\Policies\SchoolPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\Student::class, \App\Policies\StudentPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\SchoolShift::class, \App\Policies\SchoolShiftPolicy::class);
        \Illuminate\Support\Facades\Gate::policy(\App\Models\SchoolLocation::class, \App\Policies\SchoolLocationPolicy::class);
    }
}
