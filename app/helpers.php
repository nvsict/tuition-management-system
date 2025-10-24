<?php

if (!function_exists('setting')) {
    /**
     * Get a setting value from the application config.
     */
    function setting($key, $default = null)
    {
        // This will now read from 'settings.class_from', 'settings.institute_name', etc.
        return config('settings.' . $key, $default);
    }
}
