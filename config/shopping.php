<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Admin Email Address
    |--------------------------------------------------------------------------
    |
    | The email address where admin notifications will be sent.
    | This is used for store owner registration and promotion approval requests.
    |
    */

    'admin_email' => env('ADMIN_EMAIL', 'shppngrio@gmail.com'),

    /*
    |--------------------------------------------------------------------------
    | Category Upgrade Thresholds
    |--------------------------------------------------------------------------
    |
    | Define the number of accepted promotions required for clients to upgrade
    | to the next category. These thresholds are evaluated based on accepted
    | promotion usage in the last 6 months.
    |
    | - medium: Number of accepted promotions needed to upgrade from 'Inicial' to 'Medium'
    | - premium: Number of accepted promotions needed to upgrade from 'Medium' to 'Premium'
    |
    */

    'category_thresholds' => [
        'medium' => env('CATEGORY_THRESHOLD_MEDIUM', 5),
        'premium' => env('CATEGORY_THRESHOLD_PREMIUM', 15),
    ],

    /*
    |--------------------------------------------------------------------------
    | Category Evaluation Period
    |--------------------------------------------------------------------------
    |
    | Number of months to look back when evaluating client promotion usage
    | for category upgrades. Default is 6 months as specified in business rules.
    |
    */

    'category_evaluation_months' => env('CATEGORY_EVALUATION_MONTHS', 6),

    /*
    |--------------------------------------------------------------------------
    | Sequential Code Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for sequential numeric codes for stores and promotions.
    | These codes are auto-generated and must be unique.
    |
    | - start_value: Starting value for sequential codes (default 1)
    | - padding: Number of digits for code display (e.g., 5 -> 00001)
    |
    */

    'sequential_codes' => [
        'start_value' => 1,
        'padding' => 5, // Codes displayed as 00001, 00002, etc.
    ],

    /*
    |--------------------------------------------------------------------------
    | News Expiration Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for automatic news expiration and cleanup.
    |
    | - default_duration_days: Default number of days for news validity
    | - cleanup_retention_days: How long to keep expired news before deletion
    |
    */

    'news' => [
        'default_duration_days' => env('NEWS_DEFAULT_DURATION', 30),
        'cleanup_retention_days' => env('NEWS_CLEANUP_RETENTION', 90),
    ],

    /*
    |--------------------------------------------------------------------------
    | Promotion Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for promotion-related features.
    |
    | - default_duration_days: Default duration for new promotions
    | - min_duration_days: Minimum allowed promotion duration
    | - max_duration_days: Maximum allowed promotion duration
    |
    */

    'promotion' => [
        'default_duration_days' => env('PROMOTION_DEFAULT_DURATION', 30),
        'min_duration_days' => env('PROMOTION_MIN_DURATION', 1),
        'max_duration_days' => env('PROMOTION_MAX_DURATION', 365),
    ],

    /*
    |--------------------------------------------------------------------------
    | Report Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for report generation.
    |
    | - default_date_range_months: Default date range for reports in months
    | - export_formats: Supported export formats for reports
    |
    */

    'reports' => [
        'default_date_range_months' => 3,
        'export_formats' => ['excel', 'pdf', 'csv'],
        'items_per_page' => 20,
    ],

    /*
    |--------------------------------------------------------------------------
    | Store Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for store management.
    |
    | - rubros: Available business categories for stores
    |
    */

    'store_rubros' => [
        'indumentaria' => 'Indumentaria',
        'perfumeria' => 'Perfumería',
        'optica' => 'Óptica',
        'comida' => 'Comida',
        'tecnologia' => 'Tecnología',
        'deportes' => 'Deportes',
        'libreria' => 'Librería',
        'jugueteria' => 'Juguetería',
        'hogar' => 'Hogar',
        'otros' => 'Otros'
    ],

    /*
    |--------------------------------------------------------------------------
    | Client Category Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for client category system.
    |
    | - categories: Available client categories
    | - benefits: Description of benefits for each category
    |
    */

    'client_categories' => [
        'Inicial' => [
            'level' => 1,
            'color' => '#6c757d', // Bootstrap secondary
            'benefits' => [
                'Access to Inicial category promotions',
                'Email notifications',
                'Upgrade path to Medium'
            ]
        ],
        'Medium' => [
            'level' => 2,
            'color' => '#0dcaf0', // Bootstrap info
            'benefits' => [
                'Access to Inicial and Medium promotions',
                'Priority email notifications',
                'Upgrade path to Premium'
            ]
        ],
        'Premium' => [
            'level' => 3,
            'color' => '#ffc107', // Bootstrap warning (gold)
            'benefits' => [
                'Access to all promotions',
                'Priority email notifications',
                'Exclusive Premium promotions',
                'Early access to new promotions'
            ]
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Notification Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for email notifications throughout the system.
    |
    | - enabled: Master switch for email notifications
    | - queue: Whether to queue emails for background processing
    |
    */

    'notifications' => [
        'enabled' => env('NOTIFICATIONS_ENABLED', true),
        'queue' => env('NOTIFICATIONS_QUEUE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Scheduled Jobs Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for scheduled background jobs.
    |
    | - category_evaluation_schedule: Cron expression for category evaluation
    | - news_cleanup_schedule: Cron expression for news cleanup
    | - retention_days: Days after expiration before permanent deletion
    |
    */

    'scheduled_jobs' => [
        'category_evaluation' => [
            'enabled' => env('JOB_CATEGORY_EVALUATION_ENABLED', true),
            'schedule' => 'monthly', // Run on first day of every month
        ],
        'news_cleanup' => [
            'enabled' => env('JOB_NEWS_CLEANUP_ENABLED', true),
            'schedule' => 'daily', // Run daily at midnight
            'retention_days' => env('NEWS_RETENTION_DAYS', 30), // Days after expiration before deletion
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Contact Information
    |--------------------------------------------------------------------------
    |
    | Contact information for system administrators displayed to users.
    |
    */

    'admin_contact' => [
        'email' => env('ADMIN_CONTACT_EMAIL', 'admin@shoppingrio.com'),
        'phone' => env('ADMIN_CONTACT_PHONE', '+54 341 XXX-XXXX'),
        'support_hours' => 'Monday to Friday, 9 AM - 6 PM'
    ],

];
