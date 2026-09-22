-- Nur ausführen, wenn eine ältere Tabelle noch `verb` statt `verb_it` besitzt.
-- Wenn verb_it und verb_de bereits existieren, nur den FULLTEXT-Teil bei Bedarf ausführen.

ALTER TABLE `französisch_verben`
  CHANGE COLUMN `verb` `verb_it` varchar(250) DEFAULT NULL,
  ADD COLUMN `verb_de` varchar(250) DEFAULT NULL AFTER `verb_it`,
  MODIFY COLUMN `praesens` varchar(500) DEFAULT NULL,
  MODIFY COLUMN `perfekt` varchar(500) DEFAULT NULL,
  MODIFY COLUMN `futur` varchar(500) DEFAULT NULL,
  MODIFY COLUMN `imperativ` varchar(500) DEFAULT NULL;

ALTER TABLE `französisch_verben`
  ADD FULLTEXT KEY `ft_französisch_verben`
  (`verb_it`,`verb_de`,`praesens`,`perfekt`,`futur`,`imperativ`);
