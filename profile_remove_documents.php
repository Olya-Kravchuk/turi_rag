<?php

require_once(__DIR__ . '/../../config.php');

use local_turi_rag\local\turi_client;

$courseid = required_param('courseid', PARAM_INT);

// Перевірка авторизації та прав доступу.
require_login($courseid);
$context = context_course::instance($courseid);
require_capability('moodle/course:update', $context);

$PAGE->set_url(new moodle_url('/local/turi_rag/profile_remove_documents.php', ['courseid' => $courseid]));
$PAGE->set_context($context);
$PAGE->set_title(get_string('remove_documents', 'local_turi_rag'));
$PAGE->set_heading(get_string('remove_documents', 'local_turi_rag'));

// Обробка форми видалення.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    confirm_sesskey();

    $docids = optional_param_array('documents', [], PARAM_TEXT);

    if (!empty($docids)) {
        // Тут буде ваш виклик API для видалення документів:
        // turi_client::delete_documents($courseid, $docids);
    }

    $redirecturl = new moodle_url('/course/view.php', ['id' => $courseid]);
    redirect(
        $redirecturl,
        get_string('documents_removed_success', 'local_turi_rag'),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

// Отримання списку документів.
$statusdata = turi_client::get_rag_status((string)$courseid);
$rawdocuments = $statusdata['documents'] ?? [];

$documents = [];
foreach ($rawdocuments as $doc) {
    $documents[] = [
        'id' => $doc['id'] ?? $doc['doc_id'] ?? '',
        'document_name' => $doc['document_name'] ?? $doc['name'] ?? '',
        'file_name' => $doc['file_name'] ?? $doc['filename'] ?? '',
        'supported' => true,
        'disabled' => false,
    ];
}

$templatecontext = [
    'sesskey' => sesskey(),
    'actionurl' => $PAGE->url->out(false),
    'cancelurl' => (new moodle_url('/course/view.php', ['id' => $courseid]))->out(false),
    'documents' => $documents,
    'showcheckbox' => true,
];

echo $OUTPUT->header();
echo $OUTPUT->render_from_template('local_turi_rag/profile_remove_documents', $templatecontext);
echo $OUTPUT->footer();