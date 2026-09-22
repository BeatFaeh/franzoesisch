<?php
declare(strict_types=1);
$auth->requireAdmin();
$csrf->verify();
$id = (int)($_POST['id'] ?? 0);
if ($id > 0 && $linkRepository->delete($id)) {
    $flash->set('success', 'Link gelöscht.');
} else {
    $flash->set('error', 'Link konnte nicht gelöscht werden.');
}
header('Location: index.php?action=admin&notice=links#links');
exit;
