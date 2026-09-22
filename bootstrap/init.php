<?php
declare(strict_types=1);

$appConfig = require __DIR__ . '/../config/app.php';
$dbConfig  = require __DIR__ . '/../config/database.php';

session_name((string)$appConfig['session_name']);
session_set_cookie_params([
    'httponly' => true,
    'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'samesite' => 'Strict',
    'path'     => '/',
]);
session_start();

foreach ([
    'Database.php',
    'Security/Auth.php',
    'Security/Csrf.php',
    'Support/Flash.php',
    'Support/Html.php',
    'Repository/CardRepository.php',
    'Service/QuizService.php',
    'Service/ExamService.php',
] as $file) {
    require_once __DIR__ . '/../src/' . $file;
}

try {
    $database = new Database($dbConfig);
    $db = $database->connection();
} catch (Throwable $e) {
    http_response_code(500);
    exit('Datenbankverbindung fehlgeschlagen. Bitte Zugangsdaten, Datenbankname und MariaDB-Benutzerrechte prüfen.');
}

$auth = new Auth();
$csrf = new Csrf();
$flash = new Flash();
$cardRepository = new CardRepository($db);
$quizService = new QuizService($db);
$examService = new ExamService($cardRepository, $quizService);

// WICHTIG: Kein automatisches CREATE TABLE beim Seitenaufruf.
// Die Datenbank wird einmalig über database/franzoesisch_woerter_und_saetze.sql importiert.
