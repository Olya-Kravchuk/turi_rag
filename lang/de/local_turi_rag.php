<?php

$string['pluginname'] = 'TURI';

// settings.php
$string['promptsettings'] = 'Prompt-Dienst';
$string['promptsettings_desc'] = 'Konfiguration des TURI Prompt-Dienstes.';
$string['prompt_host'] = 'Prompt-IP oder URL';
$string['prompt_host_desc'] = 'IP-Adresse oder URL des TURI Prompt-Dienstes.';
$string['prompt_port'] = 'Prompt-Port';
$string['prompt_port_desc'] = 'Port des TURI Prompt-Dienstes.';
$string['embeddingsettings'] = 'Embedding-Dienst';
$string['embeddingsettings_desc'] = 'Konfiguration des TURI Embedding-Dienstes.';
$string['embedding_host'] = 'Embedding-IP oder URL';
$string['embedding_host_desc'] = 'IP-Adresse oder URL des TURI Embedding-Dienstes.';
$string['embedding_port'] = 'Embedding-Port';
$string['embedding_port_desc'] = 'Port des TURI Embedding-Dienstes.';
$string['savechanges'] = 'Änderungen speichern';

// Navigation.
$string['ragprofile'] = 'TURI RAG-Profil';
$string['course'] = 'Kurs';
$string['explanation'] = 'Erklärung';
$string['prompt'] = 'Prompt';

// profile.mustache
$string['rag_profile_title'] = 'RAG-Profil';
$string['rag_profile_description'] =
    'Dieses RAG-Profil enthält die Dokumente, die als Wissensgrundlage für den KI-Tutor in diesem Kurs verwendet werden.';
$string['remove_documents'] = 'Dokumente entfernen';
$string['edit_rag_profile'] = 'RAG-Profil bearbeiten';
$string['delete_rag_profile'] = 'RAG-Profil löschen';
$string['documents'] = 'Dokumente';
$string['document_id'] = 'Dokument-ID';
$string['document_name'] = 'Dokumentname';
$string['file_name'] = 'Dateiname';

// ptofile_edit.mustache
$string['edit_rag_profile_description'] = 'Dokumente, die mit einem Häkchen gekennzeichnet sind, wurden bereits zum RAG-Profil hinzugefügt. Wenn Sie weitere Dokumente hinzufügen möchten, wählen Sie diese aus.';
$string['save'] = 'Speichern';

// profile.create.mustache
$string['select_documents'] = 'Dokumente auswählen';
$string['select_documents_description'] = 'Wählen Sie die Dokumente aus, die Sie in Ihr RAG-Profil aufnehmen möchten.';
$string['cancel'] = 'Abbrechen';
$string['continue'] = 'Weiter';

// profile_remove_documents
$string['remove_documents'] = 'Dokumente entfernen';
$string['remove_documents_description'] = 'Wählen Sie die Dokumente aus, die Sie aus dem RAG-Profil entfernen möchten.';
$string['delete'] = 'Löschen';
$string['documents_removed_success'] = 'Die ausgewählten Dokumente wurden erfolgreich entfernt.';
$string['profile_delete_accepted'] = 'Der Löschauftrag wurde angenommen. Das Profil wird in Kürze entfernt.';

// explanation_empty.mustache
$string['no_rag_profile'] = 'Für diesen Kurs wurde noch kein RAG-Profil erstellt.';
$string['no_rag_profile_description'] =
    'Ein RAG-Profil legt fest, welche Kursdokumente die KI als Wissensgrundlage verwendet. Erstellen Sie ein Profil, indem Sie die gewünschten Dokumente auswählen.';
$string['rag_profile_step_by_step'] = 'Nach dem Klick auf „Jetzt starten“ wirst du Schritt für Schritt durch die Einrichtung geführt.';
$string['more_information'] = 'Weitere Informationen zum RAG-Profil und dessen Konfiguration finden Sie auf der Seite „Erklärung“.';
$string['start'] = 'Jetzt starten';

// explanation_info.mustache
$string['what_is_rag_profile'] = 'Was ist ein RAG-Profil?';
$string['what_is_rag_profile_desc'] = 'Ein RAG-Profil legt fest, wie der KI-Assistent die Kursmaterialien verwendet, um relevante Informationen abzurufen und Antworten auf Grundlage der ausgewählten Dokumente und des System-Prompts zu erstellen.';
$string['create_rag_profile'] = 'RAG-Profil erstellen';
$string['create_rag_profile_desc'] = 'Ein RAG-Profil wird automatisch erstellt, sobald Sie die gewünschten Dokumente auswählen und hochladen. Die ausgewählten Dokumente werden anschließend als Wissensgrundlage für die Beantwortung von Fragen innerhalb des RAG-Profils verwendet.';
$string['select_upload_documents'] = 'Dokumente auswählen und hochladen';
$string['select_upload_documents_desc'] = 'Wählen Sie die Dokumente aus, die für das RAG-Profil verwendet werden sollen. Sobald Sie die Dokumente ausgewählt und abgesendet haben, wird das RAG-Profil automatisch erstellt. Die hochgeladenen Dokumente bilden die Wissensgrundlage für die Antworten der KI.';
$string['course_assignment'] = 'Zuordnung zu einem Kurs';
$string['course_assignment_desc'] = 'Ein RAG-Profil gehört immer zu genau einem Kurs. Das Profil kann daher nur innerhalb des ausgewählten Kurses verwendet werden und stellt das Wissen für diesen Kurs bereit.';
$string['activate_ai_assistant'] = 'KI-Assistenten aktivieren';
$string['activate_ai_assistant_desc'] = 'Nach der Erstellung des RAG-Profils muss der KI-Assistent-Block im Kurs aktiviert werden. Erst danach können die im Kurs eingeschriebenen Teilnehmer den KI-Assistenten nutzen.';
$string['select_system_prompt'] = 'System-Prompt auswählen';
$string['select_system_prompt_desc'] = 'Wählen Sie einen passenden System-Prompt aus, um die Rolle und das Verhalten der KI festzulegen. Der System-Prompt bestimmt, wie die KI auf Fragen reagieren soll und welche Regeln, Vorgaben oder Einschränkungen sie bei ihren Antworten berücksichtigen muss. Je nach gewähltem Prompt kann die KI beispielsweise stärker auf die bereitgestellten Dokumente beschränkt werden oder mit weniger Einschränkungen antworten.';
$string['delete_rag_profile'] = 'RAG-Profil löschen';
$string['delete_rag_profile_desc'] = 'Beim Löschen eines RAG-Profils werden auch alle mit diesem Profil verknüpften Dokumente gelöscht. Zusätzlich wird die Nutzungshistorie des RAG-Profils entfernt. Dazu gehören auch die bisherigen Fragen und Interaktionen der Nutzer, die das RAG-Profil verwendet haben.';
$string['note'] = 'Hinweis:';
$string['delete_rag_profile_note'] = 'Das Löschen eines RAG-Profils sollte daher sorgfältig geprüft werden, da die zugehörigen Dokumente und die Nutzungshistorie nicht erhalten bleiben.';

// profile_creation_result.mustache
$string['ragprofilecreation'] = 'RAG-Profil erstellen';
$string['ragprofilesuccess'] = 'Sie haben das RAG-Profil erfolgreich erstellt.';
$string['ragprofilecreating'] = 'Das RAG-Profil wird erstellt.';
$string['ragprofileinactive'] = 'Es wurde noch kein RAG-Profil für diesen Kurs erstellt.';

// wizard_progress.mustache
$string['step_start'] = 'Start';
$string['documents_selected'] = 'Dokumente ausgewählt';
$string['rag_profile_creation'] = 'RAG-Profil erstellen';
$string['system_prompt'] = 'System-Prompt';
$string['completed'] = 'Abgeschlossen';

// modal
$string['delete_profile_confirm_title'] = 'RAG-Profil löschen';
$string['delete_profile_confirm_body'] = 'Nach dem Löschen dieses Profils können die Lernenden den KI-Assistenten nicht mehr verwenden. Möchten Sie es wirklich löschen?';

// error classes/local/turi_client.php
$string['embedding_config_missing'] = 'Der TURI-Dienst ist nicht korrekt konfiguriert. Bitte wenden Sie sich an den Administrator.';
$string['turi_connection_error'] = 'Der TURI-Dienst ist derzeit nicht verfügbar. Bitte versuchen Sie es später erneut.';
$string['turi_http_error'] = 'Der TURI-Dienst hat einen Fehler zurückgegeben. Bitte versuchen Sie es später erneut.';
$string['turi_invalid_response'] = 'Der TURI-Dienst hat eine unerwartete Antwort zurückgegeben. Bitte wenden Sie sich an den Administrator.';

$string['profile_delete_accepted'] = 'Der Löschauftrag wurde angenommen. Das Profil wird in Kürze entfernt.';
$string['error_deleting_profile'] = 'Beim Löschen des RAG-Profils ist ein Fehler aufgetreten. Bitte versuchen Sie es später erneut.';

// profile_notification.js
$string['rag_status_success'] = 'Erstellung des RAG-Profils erfolgreich abgeschlossen! Weiterleitung...';
$string['rag_status_inprogress'] = 'Erstellung des RAG-Profils läuft, bitte warten...';
$string['rag_status_error'] = 'Bei der Verarbeitung ist ein Fehler aufgetreten oder es wurde ein unerwarteter Status empfangen.';