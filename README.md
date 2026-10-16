Скопіюй текст нижче безпосередньо у файл `README.md`. Він оформлений звичайним Markdown із заголовками `#`, `##`, списками `-` та виділенням `**`.

Schreiben

# README.md – Administrator-Dokumentation

## 1\. Überblick

Das Moodle-Plugin `local_turi_rag` ermöglicht die Erstellung und Verwaltung von Retrieval-Augmented-Generation-Profilen (RAG) für Moodle-Kurse. Es kommuniziert mit einem externen TURI-Dienst, um Kursdokumente zu verarbeiten und RAG-Profile zu verwalten.

Diese Dokumentation beschreibt die erforderlichen Konfigurationsschritte und die Vorbereitung des Plugins für den Einsatz.

## 2\. Voraussetzungen

Vor der Verwendung des Plugins müssen folgende Voraussetzungen erfüllt sein:

- Eine funktionsfähige Moodle-Installation.
- Zugriff auf die Moodle-Administration.
- Ein erreichbarer TURI-Dienst.
- Die erforderlichen PHP-Erweiterungen, insbesondere cURL.
- Node.js und npm, falls JavaScript-Dateien neu erstellt werden müssen.

## 3\. Konfiguration des Plugins

### 3.1 Einstellungen in Moodle

Öffnen Sie die Moodle-Administration und navigieren Sie zu:

**Website-Administration → Plugins → Lokale Plugins → TURI RAG**

Tragen Sie die Verbindungsdaten für beide Dienste ein.

#### Prompt-Dienst

- **Prompt Host:** IP-Adresse oder Hostname des Prompt-Dienstes.
- **Prompt Port:** Port, über den der Prompt-Dienst erreichbar ist.

#### Embedding-Dienst

- **Embedding Host:** IP-Adresse oder Hostname des Embedding-Dienstes.
- **Embedding Port:** Port, über den der Embedding-Dienst erreichbar ist.

**Wichtig:** Die im Plugin hinterlegten Werte sind ausschließlich Standardwerte. Der Administrator muss die tatsächlichen Verbindungsdaten selbst eintragen und speichern. Die Standardwerte dürfen nicht ungeprüft übernommen werden.

### 3.2 Verbindung zu den externen Diensten

Stellen Sie sicher, dass der Moodle-Server beide Dienste über das konfigurierte Netzwerk erreichen kann.

Prüfen Sie insbesondere:

- Die eingetragenen Hostnamen oder IP-Adressen sind korrekt.
- Die verwendeten Ports sind erreichbar.
- Die erforderlichen API-Endpunkte des TURI-Dienstes sind verfügbar.
- Firewall-Regeln verhindern keine Verbindung zwischen Moodle und TURI.

**Sicherheitshinweis:** Für Verbindungen außerhalb eines vertrauenswürdigen Netzwerks sollte HTTPS verwendet werden, sofern der Dienst und die Plugin-Konfiguration dies unterstützen.

## 4\. JavaScript-Dateien vorbereiten

Das Plugin verwendet AMD-JavaScript-Module. Nach Änderungen an den JavaScript-Quelldateien müssen die AMD-Dateien neu kompiliert werden.

### 4.1 Voraussetzungen

Installieren Sie Node.js und npm, sofern diese noch nicht verfügbar sind.

Wechseln Sie anschließend in das Moodle-Hauptverzeichnis.

### 4.2 AMD-Dateien kompilieren

Führen Sie folgenden Befehl aus:

```
npx grunt amd
```

Der Befehl kompiliert die AMD-JavaScript-Quelldateien der Moodle-Plugins.

**Wichtig:** Führen Sie den Befehl nach Änderungen an den AMD-Quelldateien erneut aus, damit die kompilierten Dateien aktualisiert werden.

### 4.3 Moodle-Caches leeren

Nach der Kompilierung kann es erforderlich sein, die Moodle-Caches zu leeren.

Navigieren Sie zu:

**Website-Administration → Entwicklung → Caches leeren**

Alternativ können Sie folgenden CLI-Befehl ausführen:

```
php admin/cli/purge_caches.php
```

## 5\. Plugin installieren und aktualisieren

Das Plugin muss im folgenden Verzeichnis installiert werden:

`moodle/local/turi_rag/`

Stellen Sie sicher, dass alle erforderlichen Dateien vorhanden sind und die Dateiberechtigungen korrekt gesetzt wurden.

Rufen Sie anschließend die Moodle-Website-Administration auf und führen Sie gegebenenfalls die angebotene Datenbankinstallation oder das Datenbank-Upgrade durch.

Nach Änderungen an der Plugin-Version müssen die Versionsinformationen in `version.php` entsprechend aktualisiert werden.

## 6\. Verwendung des Plugins

Nach erfolgreicher Konfiguration können berechtigte Benutzer die RAG-Funktionen innerhalb ihrer Moodle-Kurse verwenden.

Das Plugin unterstützt unter anderem:

- Die Erstellung von RAG-Profilen für Kurse.
- Die Auswahl und Verarbeitung von Kursdokumenten.
- Die Anzeige des aktuellen Profilstatus.
- Die Aktualisierung bestehender RAG-Profile.
- Die Auswahl eines System-Prompts.
- Die Aktivierung und Deaktivierung der entsprechenden Funktionalität.
- Das Löschen vollständiger RAG-Profile oder einzelner Dokumente.

Die verfügbaren Funktionen hängen von der Implementierung des Plugins und den API-Endpunkten des TURI-Dienstes ab.

## 7\. Fehlerbehebung

### Verbindung zum TURI-Dienst fehlgeschlagen

- Prüfen Sie die Einstellungen für Prompt Host, Prompt Port, Embedding Host und Embedding Port.
- Überprüfen Sie die Netzwerkverbindung vom Moodle-Server zum TURI-Dienst.
- Kontrollieren Sie die Verfügbarkeit der API-Endpunkte.
- Prüfen Sie die Moodle-Fehlerprotokolle.

### JavaScript-Funktionen funktionieren nicht

- Führen Sie `npx grunt amd` im Moodle-Hauptverzeichnis aus.
- Leeren Sie die Moodle-Caches.
- Prüfen Sie die Browser-Konsole auf JavaScript-Fehler.
- Kontrollieren Sie, ob die kompilierten AMD-Dateien vorhanden und aktuell sind.

### RAG-Profil kann nicht erstellt oder aktualisiert werden

- Prüfen Sie, ob der TURI-Dienst erreichbar ist.
- Kontrollieren Sie die vom Dienst zurückgegebenen Fehlermeldungen.
- Überprüfen Sie, ob die ausgewählten Dateiformate unterstützt werden.
- Prüfen Sie die Moodle-Debugging-Ausgaben, sofern das Debugging aktiviert ist.

## 8\. Datenschutz

Das Plugin kann Kursinformationen, ausgewählte Kursdokumente, Profilkonfigurationen und System-Prompt-Informationen an den externen TURI-Dienst übertragen.

Der Administrator ist dafür verantwortlich, sicherzustellen, dass:

- die Datenverarbeitung den geltenden Datenschutzbestimmungen entspricht;
- der externe Dienst angemessen abgesichert ist;
- Zugriffsrechte und Netzwerkverbindungen korrekt konfiguriert sind;
- die Speicher- und Löschrichtlinien des externen Dienstes bekannt sind.

Weitere Informationen finden Sie in der Datei `privacy.md` des Plugins.

## 9\. Wartung

Bei Wartungsarbeiten sollten folgende Punkte regelmäßig überprüft werden:

- Erreichbarkeit des TURI-Dienstes.
- Korrektheit der Plugin-Konfiguration.
- Aktualität der AMD-Dateien nach JavaScript-Änderungen.
- Moodle-Fehlerprotokolle.
- Kompatibilität des Plugins mit der installierten Moodle-Version.
- Datenschutz- und Sicherheitsanforderungen.