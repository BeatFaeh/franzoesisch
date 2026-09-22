<?php
declare(strict_types=1);
$flashMessage=$flash->take();
if (!$auth->isAdmin()) {
?>
<!doctype html><html lang="de"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Administration – Französische Lernkarten</title><link rel="stylesheet" href="assets/css/main.css"><link rel="stylesheet" href="assets/css/admin.css"></head><body><main class="admin-page"><header class="admin-header"><div><h1>Administration</h1><p>Französische Lernkarten</p></div><nav><a class="button neutral" href="index.php">Zur Hauptseite</a></nav></header>
<?php if($flashMessage):?><div class="message <?=Html::e($flashMessage['type'])?>"><?=Html::e($flashMessage['message'])?></div><?php endif;?>
<section class="panel"><h2>Anmelden</h2><form method="post"><input type="hidden" name="csrf_token" value="<?=Html::e($csrf->token())?>"><input type="hidden" name="form_action" value="login"><label>Passwort<input type="password" name="password" required autofocus></label><button type="submit">Anmelden</button></form></section></main></body></html>
<?php exit; }
$search=trim((string)($_GET['suche']??''));$field=(string)($_GET['feld']??'all');$limitRaw=(string)($_GET['anzahl']??'50');$limit=$limitRaw==='alle'?null:max(1,(int)$limitRaw);$cards=$cardRepository->adminSearch($search,$field,$limit);$total=$cardRepository->adminCount($search,$field);
?><!doctype html><html lang="de"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Administration – Französische Lernkarten</title><link rel="stylesheet" href="assets/css/main.css"><link rel="stylesheet" href="assets/css/admin.css"></head><body><main class="admin-page"><header class="admin-header"><div><h1>Französische Lernkarten – Administration</h1><p>Wörter, Themen und Lektionen verwalten</p></div><nav><a class="button neutral" href="index.php">Zur Hauptseite</a><a class="button neutral" href="index.php?action=logout">Abmelden</a></nav></header><?php if($flashMessage):?><div class="message <?=Html::e($flashMessage['type'])?>"><?=Html::e($flashMessage['message'])?></div><?php endif;?>

<section class="panel" id="passwort">
<h2>Admin-Passwort ändern</h2>
<p class="muted">Das Passwort kann hier jederzeit geändert werden. Es wird nur als sicherer Hash gespeichert.</p>
<form method="post">
<input type="hidden" name="csrf_token" value="<?=Html::e($csrf->token())?>">
<input type="hidden" name="form_action" value="change_password">
<div class="form-grid">
<label>Bisheriges Passwort<input type="password" name="current_password" required autocomplete="current-password"></label>
<label>Neues Passwort<input type="password" name="new_password" minlength="10" required autocomplete="new-password"></label>
<label>Neues Passwort wiederholen<input type="password" name="new_password_repeat" minlength="10" required autocomplete="new-password"></label>
</div>
<button type="submit">Passwort ändern</button>
</form>
</section>

<section class="panel"><h2>Neue Lernkarte</h2><form method="post"><input type="hidden" name="csrf_token" value="<?=Html::e($csrf->token())?>"><input type="hidden" name="form_action" value="add_card"><div class="form-grid"><label>Lektion<input type="number" name="lektion" min="1" required></label><label>Thema Deutsch<input name="thema_d" required></label><label>Thema Französisch<input name="thema_f" required></label><label>Deutsch<textarea name="wort_d" required></textarea></label><label>Französisch<textarea name="wort_f" required></textarea></label></div><button>Lernkarte speichern</button></form></section>
<section class="panel list-panel" id="lernkarten"><h2>Lernkarten bearbeiten</h2><form method="get" class="verb-search-form"><input type="hidden" name="action" value="admin"><label>Suche<input type="search" name="suche" value="<?=Html::e($search)?>" placeholder="Wort, Thema oder ID"></label><label>Feld<select name="feld"><option value="all">Alle</option><option value="id">ID</option><option value="de">Deutsch</option><option value="fr">Französisch</option></select></label><label>Anzahl<select name="anzahl"><?php foreach(['25','50','100','500','alle'] as $n):?><option value="<?=$n?>" <?=$limitRaw===$n?'selected':''?>><?=ucfirst($n)?></option><?php endforeach;?></select></label><button>Suchen</button></form><p class="muted"><?=count($cards)?> von <?=$total?> Datensätzen</p><?php foreach($cards as $c):?><details class="entry"><summary>DB-ID #<?=$c['id']?> · L<?=$c['lektion']?> · <?=Html::e($c['wort_d'])?> ↔ <?=Html::e($c['wort_f'])?></summary><form method="post"><input type="hidden" name="csrf_token" value="<?=Html::e($csrf->token())?>"><input type="hidden" name="form_action" value="update_card"><input type="hidden" name="id" value="<?=$c['id']?>"><div class="form-grid"><label>ID<input value="<?=$c['id']?>" readonly></label><label>Lektion<input type="number" name="lektion" value="<?=$c['lektion']?>" required></label><label>Thema Deutsch<input name="thema_d" value="<?=Html::e($c['thema_d'])?>" required></label><label>Thema Französisch<input name="thema_f" value="<?=Html::e($c['thema_f'])?>" required></label><label>Deutsch<textarea name="wort_d" required><?=Html::e($c['wort_d'])?></textarea></label><label>Französisch<textarea name="wort_f" required><?=Html::e($c['wort_f'])?></textarea></label></div><button>Änderungen speichern</button></form><form method="post" class="delete-form" onsubmit="return confirm('Lernkarte wirklich löschen?')"><input type="hidden" name="csrf_token" value="<?=Html::e($csrf->token())?>"><input type="hidden" name="form_action" value="delete_card"><input type="hidden" name="id" value="<?=$c['id']?>"><button class="danger">Löschen</button></form></details><?php endforeach;?></section></main></body></html>
