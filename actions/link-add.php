<?php
declare(strict_types=1);
$auth->requireAdmin();
$csrf->verify();
$titel = trim((string)($_POST['titel'] ?? ''));
$url = trim((string)($_POST['url'] ?? ''));
$beschreibung = trim((string)($_POST['beschreibung'] ?? ''));
if ($titel === '' || $url === '') {
    $flash->set('error', 'Titel und URL müssen ausgefüllt sein.');
} elseif (!filter_var($url, FILTER_VALIDATE_URL)) {
    $flash->set('error', 'Bitte eine gültige URL inklusive https:// eingeben.');
} elseif ($linkRepository->add($titel, $url, $beschreibung)) {
    $flash->set('success', 'Link gespeichert.');
} else {
    $flash->set('error', 'Link konnte nicht gespeichert werden.');
}
header('Location: index.php?action=admin&notice=links#links');
exit;
