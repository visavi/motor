<?php

return [
    'table_storage' => [
        'table_name' => 'migrations',
        'version_column_length' => 191,
    ],

    'migrations_paths' => [
        'Migrations' => dirname(__DIR__) . '/database/new',
    ],

    # Run all migrations in a transaction.
    'all_or_nothing' => false,

    # Whether to wrap migrations in a single transaction.
    'transactional' => true,

    # Adds an extra check in the generated migrations to ensure that is executed on the same database type.
    'check_database_platform' => true,

    # Possible values: "year", "year_and_month", "none"
    'organize_migrations' => 'none',

    # Connection to use for the migrations
    'connection' => null,

    # Entity manager to use for migrations. This overrides the "connection" setting.
    'em' => null,
];
