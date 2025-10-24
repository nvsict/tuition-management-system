<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;

class SettingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // We check if the table exists to prevent errors during initial migration.
        if (Schema::hasTable('settings')) {
            $settings = Setting::all();
            foreach ($settings as $setting) {
                // This sets config keys like 'settings.class_from', 'settings.institute_name'
                config()->set('settings.' . $setting->key, $setting->value);
            }
        }
    }
}