<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Youth Policy Tracker Global Architecture Configuration
    |--------------------------------------------------------------------------
    |
    | Supports multi-country and sub-national deployment without code changes.
    | Default deployment: Federal Republic of Nigeria (36 States + FCT).
    |
    */

    'country_name' => env('TRACKER_COUNTRY_NAME', 'Nigeria'),
    'country_code' => env('TRACKER_COUNTRY_CODE', 'NG'),
    'country_iso3' => env('TRACKER_COUNTRY_ISO3', 'NGA'),

    'subnational_label' => env('TRACKER_SUBNATIONAL_LABEL', 'State'),
    'subnational_plural' => env('TRACKER_SUBNATIONAL_PLURAL', 'States'),
    'capital_territory' => env('TRACKER_CAPITAL_TERRITORY', 'Federal Capital Territory'),

    'policy_name' => env('TRACKER_POLICY_NAME', 'National Youth Policy'),
    'policy_short_name' => env('TRACKER_POLICY_SHORT_NAME', 'NYP'),

    'client_name' => 'Glowing Minds Initiatives (GMI)',
    'engineer_name' => 'Corestream Networks and Robotics Limited',
    'launch_date' => '2026-11-26',

    'supported_locales' => [
        'en' => ['name' => 'English', 'native' => 'English', 'dir' => 'ltr'],
        'fr' => ['name' => 'French', 'native' => 'Français', 'dir' => 'ltr'],
        'ha' => ['name' => 'Hausa', 'native' => 'Hausa', 'dir' => 'ltr'],
        'yo' => ['name' => 'Yoruba', 'native' => 'Yorùbá', 'dir' => 'ltr'],
        'ig' => ['name' => 'Igbo', 'native' => 'Asụsụ Igbo', 'dir' => 'ltr'],
        'ar' => ['name' => 'Arabic', 'native' => 'العربية', 'dir' => 'rtl'],
    ],
];
