<?php
declare(strict_types=1);
return [
    // Admin-Passwort als sicherer Hash gespeichert.
    'admin_password_hash' => (require __DIR__ . '/security.php')['admin_password_hash'],
    'session_name' => 'franzoesisch_lernkarten_admin',
];
