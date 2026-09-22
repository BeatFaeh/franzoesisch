-- Migration für die vom Benutzer angegebene aktuelle Struktur:
-- verb_it und verb_de existieren bereits.
-- Ergänzt AUTO_INCREMENT/Primary Key (falls in der realen Tabelle noch nicht vorhanden)
-- und einen Volltextindex für die Verbtexte.
-- Vor dem Ausführen bitte prüfen, ob PRIMARY KEY bzw. FULLTEXT-Index bereits vorhanden sind.

ALTER TABLE `französisch_verben`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
  MODIFY `praesens` varchar(500) DEFAULT NULL,
  MODIFY `perfekt` varchar(500) DEFAULT NULL,
  MODIFY `futur` varchar(500) DEFAULT NULL,
  MODIFY `imperativ` varchar(500) DEFAULT NULL;

-- Nur ausführen, falls noch kein Primary Key vorhanden ist:
-- ALTER TABLE `französisch_verben` ADD PRIMARY KEY (`id`);

-- Nur ausführen, falls der Index noch nicht vorhanden ist:
ALTER TABLE `französisch_verben`
  ADD FULLTEXT KEY `ft_französisch_verben`
  (`verb_it`,`verb_de`,`praesens`,`perfekt`,`futur`,`imperativ`);
