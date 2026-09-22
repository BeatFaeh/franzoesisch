<?php
declare(strict_types=1);$auth->requireAdmin();$csrf->verify();
$id=(int)($_POST['id']??0);$wd=trim((string)($_POST['wort_d']??''));$wf=trim((string)($_POST['wort_f']??''));$td=trim((string)($_POST['thema_d']??''));$tf=trim((string)($_POST['thema_f']??''));$l=(int)($_POST['lektion']??0);
if($id<=0||$l<=0||$wd===''||$wf===''||$td===''||$tf==='')$flash->set('error','Ungültige oder unvollständige Daten.');elseif($cardRepository->update($id,$wd,$wf,$td,$tf,$l))$flash->set('success','Lernkarte aktualisiert.');else$flash->set('error','Änderung fehlgeschlagen.');header('Location: index.php?action=admin#lernkarten');exit;
