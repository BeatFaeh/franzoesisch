<?php
declare(strict_types=1);
return [
    'host' => getenv('FRANZOESISCH_DB_HOST') ?: 'localhost',
    'username' => getenv('FRANZOESISCH_DB_USERNAME') ?: 'fr_user',
    'password' => getenv('FRANZOESISCH_DB_PASSWORD') ?: '@vG%9lPl9fLyq5kg',
    'database' => getenv('FRANZOESISCH_DB_NAME') ?: 'franzoesisch_db',
];
