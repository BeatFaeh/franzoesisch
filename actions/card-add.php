<?php
declare(strict_types=1);$auth->requireAdmin();$csrf->verify();
$wd=trim((string)($_POST['wort_d']??''));$wf=trim((string)($_POST['wort_f']??''));$td=trim((string)($_POST['thema_d']??''));$tf=trim((string)($_POST['thema_f']??''));$l=(int)($_POST['lektion']??0);
if($l<=0||$wd===''||$wf===''||$td===''||$tf==='')$flash->set('error','Alle Felder müssen ausgefüllt sein.');elseif($cardRepository->add($wd,$wf,$td,$tf,$l))$flash->set('success','Lernkarte gespeichert.');else$flash->set('error','Lernkarte konnte nicht gespeichert werden.');header('Location: index.php?action=admin#lernkarten');exit;
