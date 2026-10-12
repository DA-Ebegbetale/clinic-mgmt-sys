<?php
declare(strict_types=1);

return [
    'name'        => $_ENV['APP_NAME'] ?? 'clinic-mgmt-system',
    'env'         => $_ENV['APP_ENV'] ?? 'production',
    'debug'       => filter_var($_ENV['APP_DEBUG'] ?? true, FILTER_VALIDATE_BOOLEAN),
    'timezone'    => $_ENV['APP_TIMEZONE'] ?? 'UTC',
    'base_path'   => $_ENV['BASE_PATH'] ?? '',
];