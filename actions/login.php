<?php
declare(strict_types=1);
$csrf->verify();
$password = (string)($_POST['password'] ?? '');
if (password_verify($password, (string)$appConfig['admin_password_hash'])) {
    $auth->login();
    $flash->set('success', 'Anmeldung erfolgreich.');
    header('Location: index.php?action=admin');
    exit;
}
$flash->set('error', 'Passwort nicht korrekt.');
header('Location: index.php?action=admin');
exit;
