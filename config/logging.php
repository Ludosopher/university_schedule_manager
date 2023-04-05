<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['single'],
            'ignore_exceptions' => false,
        ],

        //        'single' => [
        //            'driver' => 'single',
        //            'path' => storage_path('logs/laravel.log'),
        //            'level' => 'debug',
        //        ],
        'single' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'days' => 5,
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'days' => 5,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => 'critical',
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => 'debug',
            'handler' => SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => 'debug',
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => 'debug',
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],

        'cron' => [
            'driver' => 'daily',
            'path' => storage_path('logs/crons.log'),
            'days' => 5,
        ],

        'mail' => [
            'driver' => 'daily',
            'path' => storage_path('logs/mails.log'),
            'days' => 5,
        ],

        'notification_mail' => [
            'driver' => 'daily',
            'path' => storage_path('logs/notification_mails.log'),
            'days' => 5,
        ],

        'queue' => [
            'driver' => 'daily',
            'path' => storage_path('logs/queues.log'),
            'days' => 5,
        ],

        'websocket' => [
            'driver' => 'daily',
            'path' => storage_path('logs/websockets.log'),
            'days' => 5,
        ],

        'production_calendar' => [
            'driver' => 'daily',
            'path' => storage_path('logs/production_calendars.log'),
            'days' => 5,
        ],
    ],

];
