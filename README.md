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

## 3. Plugin installieren

### 3.1 ZIP-Datei herunterladen

1. Öffnen Sie das Git-Repository des Plugins im Webbrowser.
2. Klicken Sie auf **Code**.
3. Wählen Sie **Download ZIP**, um das Plugin herunterzuladen.
4. Speichern Sie die ZIP-Datei auf Ihrem Computer.

### 3.2 Plugin in Moodle installieren

1. Melden Sie sich als Administrator in Moodle an.
2. Navigieren Sie zu **Website-Administration → Plugins → Plugins installieren**.
3. Laden Sie die heruntergeladene ZIP-Datei hoch.
4. Folgen Sie den Anweisungen auf dem Bildschirm und schließen Sie die Installation ab.

Falls die Installation über die Moodle-Oberfläche nicht möglich ist, entpacken Sie die ZIP-Datei und kopieren Sie den Plugin-Ordner `turi_rag` in das Verzeichnis `local` Ihrer Moodle-Installation. Der endgültige Pfad muss `moodle/local/turi_rag/` lauten.

Öffnen Sie anschließend **Website-Administration → Server → Mitteilungen**, um die Installation abzuschließen.

## 4\. Konfiguration des Plugins

### 4.1 Einstellungen in Moodle

Öffnen Sie die Moodle-Administration und navigieren Sie zu:

**Website-Administration → Plugins → Lokale Plugins → TURI RAG**

Tragen Sie die Verbindungsdaten für beide Dienste ein.

#### Prompt-Dienst

- **Prompt Host:** IP-Adresse.
- **Prompt Port:** Port, über den der Prompt-Dienst erreichbar ist.

#### Embedding-Dienst

- **Embedding Host:** IP-Adresse.
- **Embedding Port:** Port, über den der Embedding-Dienst erreichbar ist.

**Wichtig:** Die im Plugin hinterlegten Werte sind ausschließlich Standardwerte. Der Administrator muss die tatsächlichen Verbindungsdaten selbst eintragen und speichern. Die Standardwerte dürfen nicht ungeprüft übernommen werden.

## 5\. JavaScript-Dateien vorbereiten

Das Plugin verwendet AMD-JavaScript-Module. Nach Änderungen an den JavaScript-Quelldateien müssen die AMD-Dateien neu kompiliert werden.

### 5.1 Voraussetzungen

Installieren Sie Node.js und npm, sofern diese noch nicht verfügbar sind.

Wechseln Sie anschließend in das Moodle-Hauptverzeichnis.

### 5.2 AMD-Dateien kompilieren

Führen Sie folgenden Befehl aus:

```
npx grunt amd
```

Der Befehl kompiliert die AMD-JavaScript-Quelldateien der Moodle-Plugins.

**Wichtig:** Führen Sie den Befehl nach Änderungen an den AMD-Quelldateien erneut aus, damit die kompilierten Dateien aktualisiert werden.

### 5.3 Moodle-Caches leeren

Nach der Kompilierung kann es erforderlich sein, die Moodle-Caches zu leeren.

Navigieren Sie zu:

**Website-Administration → Entwicklung → Caches leeren**

Alternativ können Sie folgenden CLI-Befehl ausführen:

```
php admin/cli/purge_caches.php
```

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
