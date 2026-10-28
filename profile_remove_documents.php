<?php

require_once(__DIR__ . '/../../config.php');

use local_turi_rag\local\turi_client;

$courseid = required_param('courseid', PARAM_INT);


require_login($courseid);
$context = context_course::instance($courseid);

require_capability(
    'local/turi_rag:managerag',
    context_course::instance($courseid)
);

$PAGE->set_url(new moodle_url('/local/turi_rag/profile_remove_documents.php', ['courseid' => $courseid]));
$PAGE->set_context($context);
$PAGE->set_title(get_string('remove_documents', 'local_turi_rag'));
$PAGE->set_heading(get_string('remove_documents', 'local_turi_rag'));

$turi = new turi_client();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    confirm_sesskey();

    $docids = optional_param_array('documents', [], PARAM_TEXT);

    $docids = array_values(array_filter(
        array_map('strval', $docids),
        static fn($id) => $id !== ''
    ));

    echo '<details open>';
    echo '<summary><strong>1. Received from Moodle</strong></summary>';
    echo '<pre>';
    print_r($docids);
    echo '</pre>';
    echo '</details>';

    if (!empty($docids)) {
        try {
            $result = $turi->single_delete(
                (string) $courseid,
                $docids
            );

            echo '<details open>';
            echo '<summary><strong>3. Received from TURI</strong></summary>';
            echo '<pre>';
            print_r($result);
            echo '</pre>';
            echo '</details>';
        } catch (\Throwable $e) {

            echo '<details open>';
            echo '<summary><strong>3. TURI Error</strong></summary>';
            echo '<pre>';
            print_r([
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            echo '</pre>';
            echo '</details>';
        }

    } else {
        echo '<details open>';
        echo '<summary><strong>No data received</strong></summary>';
        echo '<pre>';
        print_r($docids);
        echo '</pre>';
        echo '</details>';
    }

    exit;

    $redirecturl = new moodle_url('/course/profile.php', ['id' => $courseid]);
    redirect(
        $redirecturl,
        get_string('documents_removed_success', 'local_turi_rag'),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

$statusdata = $turi->get_rag_status((string)$courseid);
$rawdocuments = $statusdata['documents'] ?? [];

$documents = [];
foreach ($rawdocuments as $doc) {
    $documents[] = [
        'id' => $doc['document_id'] ?? '',
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
echo '<details>';
echo '<summary>Response</summary>';
echo '<pre>';
print_r($statusdata);
echo '</pre>';
echo '</details>';
echo $OUTPUT->footer();