<?php

require_once(__DIR__ . '/../../config.php');

use local_turi_rag\local\turi_client;
use local_turi_rag\local\file_helper;

$courseid = required_param('courseid', PARAM_INT);

$course = get_course($courseid);

require_login($course);

$context = context_course::instance($course->id);

$PAGE->set_url(
    new moodle_url('/local/turi_rag/profile_edit.php', [
        'courseid' => $courseid,
    ])
);

$PAGE->set_context($context);

$PAGE->set_title(
    get_string('edit_rag_profile', 'local_turi_rag')
);

$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();


try {
    $status = turi_client::get_rag_status((string)$courseid);
} catch (moodle_exception $e) {

    echo $OUTPUT->notification(
        $e->getMessage(),
        \core\output\notification::NOTIFY_ERROR
    );

    echo $OUTPUT->footer();
    exit;
}
// echo '<pre>';
// var_dump($status);
// echo '</pre>';
// die();


$ragdocuments = $status['documents'] ?? [];

// $ragdocumentids = [];

$modinfo = get_fast_modinfo($course->id);
$fs = get_file_storage();

$documents = [];
$moodlefileids = [];


// Moodle documents.
foreach ($modinfo->get_instances_of('resource') as $cm) {

    if (!$cm->uservisible) {
        continue;
    }

    $contextmodule = context_module::instance($cm->id);

    $files = $fs->get_area_files(
        $contextmodule->id,
        'mod_resource',
        'content',
        0,
        'sortorder',
        false
    );

    foreach ($files as $file) {

        if ($file->is_directory()) {
            continue;
        }

        $fileid = (string)$file->get_id();

        $moodlefileids[] = $fileid;

        $documents[] = [
            'id' => $fileid,
            'document_name' => $cm->name,
            'file_name' => $file->get_filename(),
            'supported' => file_helper::is_supported_format($file->get_filename()),
            'disabled' => false,
        ];
    }
}


// FastAPI documents.
foreach ($ragdocuments as $ragdocument) {

    $documentid = (string)($ragdocument['document_id'] ?? '');

    if ($documentid === '') {
        continue;
    }

    // Document already exists in Moodle.
    if (in_array($documentid, $moodlefileids, true)) {

        foreach ($documents as &$document) {
            if ($document['id'] === $documentid) {
                $document['inrag'] = true;
            }
        }

        unset($document);

        continue;
    }

    // Document exists only in RAG.
    $documents[] = [
        'id' => $documentid,
        'document_name' => $ragdocument['document_name'] ?? '',
        'file_name' => $ragdocument['file_name'] ?? '',
        'supported' => true,
        'disabled' => true,
    ];
}

// echo '<pre>';

// echo "FASTAPI:\n";

// foreach ($status['documents'] ?? [] as $document) {
//     echo 'document_id = ';
//     var_dump($document['document_id']);
// }

// echo "\nMOODLE:\n";

// foreach ($documents as $document) {
//     echo 'file_id = ';
//     var_dump($document['id']);
// }

// echo '</pre>';

// die();
$data = [
    'documents' => $documents,

    'showcheckbox' => true,
    'showdocumentid' => false,

    'cancelurl' => (new moodle_url(
        '/local/turi_rag/profile.php',
        [
            'courseid' => $courseid,
        ]
    ))->out(false),

    'saveurl' => (new moodle_url(
        '/local/turi_rag/profile_edit.php',
        [
            'courseid' => $courseid,
        ]
    ))->out(false),

    'sesskey' => sesskey(),
];

echo $OUTPUT->render_from_template(
    'local_turi_rag/profile_edit',
    $data
);

echo $OUTPUT->footer();
