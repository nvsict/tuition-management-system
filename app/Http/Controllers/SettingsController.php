<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
// Import the Artisan facade
use Illuminate\Support\Facades\Artisan;

class SettingsController extends Controller
{
    /**
     * Show the settings page.
     */
    public function index()
    {
        // Fetch all settings and pass them to the view
        // The pluck() method will turn this into an associative array
        // like: ['institute_name' => 'My Tuition', 'class_from' => '6']
        $settings = Setting::all()->pluck('value', 'key');
        
        return view('settings.index', compact('settings'));
    }

    /**
     * Update the settings in the database.
     */
    public function update(Request $request)
    {
        // Get all form data, except the CSRF token
        $formData = $request->except('_token');

        // Loop through each key/value pair from the form
        foreach ($formData as $key => $value) {
            // Find the setting by its key, or create it if it doesn't exist
            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        // THE FIX: This command clears the old cached config file
        // and creates a new one with the fresh settings from the database.
        Artisan::call('config:cache');

        return redirect()->route('settings.index')->with('success', 'Settings updated successfully!');
    }
}

