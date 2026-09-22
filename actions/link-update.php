<?php
declare(strict_types=1);
$auth->requireAdmin();
$csrf->verify();
$id = (int)($_POST['id'] ?? 0);
$titel = trim((string)($_POST['titel'] ?? ''));
$url = trim((string)($_POST['url'] ?? ''));
$beschreibung = trim((string)($_POST['beschreibung'] ?? ''));
if ($id <= 0 || $titel === '' || $url === '') {
    $flash->set('error', 'Ungültiger oder unvollständiger Link.');
} elseif (!filter_var($url, FILTER_VALIDATE_URL)) {
    $flash->set('error', 'Bitte eine gültige URL inklusive https:// eingeben.');
} elseif ($linkRepository->update($id, $titel, $url, $beschreibung)) {
    $flash->set('success', 'Link aktualisiert.');
} else {
    $flash->set('error', 'Link konnte nicht aktualisiert werden.');
}
header('Location: index.php?action=admin&notice=links#links');
exit;
