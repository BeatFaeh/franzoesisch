<?php
declare(strict_types=1);

$auth->requireAdmin();
$csrf->verify();

$current = (string)($_POST['current_password'] ?? '');
$new = (string)($_POST['new_password'] ?? '');
$repeat = (string)($_POST['new_password_repeat'] ?? '');

if (!password_verify($current, (string)$appConfig['admin_password_hash'])) {
    $flash->set('error', 'Das bisherige Passwort ist nicht korrekt.');
} elseif (mb_strlen($new, 'UTF-8') < 10) {
    $flash->set('error', 'Das neue Passwort muss mindestens 10 Zeichen lang sein.');
} elseif ($new !== $repeat) {
    $flash->set('error', 'Die beiden neuen Passwörter stimmen nicht überein.');
} elseif (password_verify($new, (string)$appConfig['admin_password_hash'])) {
    $flash->set('error', 'Das neue Passwort muss sich vom bisherigen Passwort unterscheiden.');
} else {
    $hash = password_hash($new, PASSWORD_DEFAULT);
    $securityFile = __DIR__ . '/../config/security.php';
    $content = "<?php\n"
        . "declare(strict_types=1);\n"
        . "return ['admin_password_hash' => " . var_export($hash, true) . "];\n";

    if (@file_put_contents($securityFile, $content, LOCK_EX) === false) {
        $flash->set('error', 'Passwort konnte nicht gespeichert werden. Schreibrechte für config/security.php prüfen.');
    } else {
        session_regenerate_id(true);
        $flash->set('success', 'Admin-Passwort erfolgreich geändert.');
    }
}

header('Location: index.php?action=admin#passwort');
exit;
