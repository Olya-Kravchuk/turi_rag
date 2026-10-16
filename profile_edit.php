<?php

require_once(__DIR__ . '/../../config.php');

use local_turi_rag\local\turi_client;
use local_turi_rag\local\file_helper;

$courseid = required_param('courseid', PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);

$course = get_course($courseid);
require_login($course);

require_capability(
    'local/turi_rag:managerag',
    context_course::instance($courseid)
);

$turi = new turi_client();
$fs = get_file_storage();

if ($action === 'save') {
    confirm_sesskey();
    $selecteddocuments = optional_param_array(
        'documents',
        [],
        PARAM_INT
    );

    if (empty($selecteddocuments)) {

        // echo $OUTPUT->notification(
        //     get_string('err_nodocuments', 'local_turi_rag'),
        //     \core\output\notification::NOTIFY_ERROR
        // );

        // echo $OUTPUT->footer();
        // exit;
        redirect(
            new moodle_url(
                '/local/turi_rag/profile_edit.php',
                [
                    'courseid' => $courseid,
                    'error' => 'nodocuments',
                ]
            )
        );
        exit;
    }

    // echo '<details open>';
    // echo '<summary><strong>Request: Moodle form</strong></summary>';
    // echo '<pre>';
    // echo "Selected document IDs:\n";
    // print_r($selecteddocuments);
    // echo '</pre>';
    // echo '</details>';


    $selectedfiles = [];
    $selectedmoodlefiles = [];

    $modinfo = get_fast_modinfo($course->id);

    foreach ($selecteddocuments as $fileid) {
        $file = $fs->get_file_by_id($fileid);
        if (!$file || $file->is_directory()) {
            continue;
        }

        $filename = $file->get_filename();
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $documentname = '';

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


            foreach ($files as $resourcefile) {
                if ($resourcefile->is_directory()) {
                    continue;
                }

                if ((int)$resourcefile->get_id() === (int)$fileid) {
                    $documentname = $cm->name;
                    break 2;
                }
            }
        }

        $selectedfiles[] = [
            'document_id' => (string)$file->get_id(),
            'document_name' => $documentname,
            'file_name' => $filename,
            'file_extension' => $extension,
        ];

        $selectedmoodlefiles[] = $file;
    }

    // echo '<details open>';
    // echo '<summary><strong>Request: documents prepared for TURI</strong></summary>';
    // echo '<pre>';
    // print_r($selectedfiles);
    // echo '</pre>';
    // echo '</details>';

    if (empty($selectedfiles)) {
        redirect(
            new moodle_url(
                '/local/turi_rag/profile_edit.php',
                [
                    'courseid' => $courseid,
                    'error' => 'nodocuments',
                ]
            )
        );
        exit;        
    }

    try {
        // echo '<details open>';
        // echo '<summary><strong>Request: get_rag_status()</strong></summary>';
        // echo '<pre>';
        // echo 'Course ID: ' . $courseid . "\n";
        // echo '</pre>';
        // echo '</details>';

        $status = $turi->get_rag_status((string)$courseid);

        // echo '<details open>';
        // echo '<summary><strong>Response: get_rag_status()</strong></summary>';
        // echo '<pre>';

        // print_r($status);

        // echo '</pre>';
        // echo '</details>';
        $coursemetadata = [
            'course_id' => (string)$courseid,
            'course_name' => $course->fullname,
            'instructor' => '',
            'semester' => '',
            'module_type' => '',
        ];

        $payload = [
            'id' => (string)$courseid,
            'course_metadata' => $coursemetadata,
            'documents' => $selectedfiles,
        ];
        echo '<details open>';
        echo '<summary><strong>Request: TURI init_session(update)</strong></summary>';
        echo '<pre>';
        echo "METHOD:\n";
        echo "init_session()\n\n";
        echo "MODE:\n";
        echo "update\n\n";
        echo "PAYLOAD:\n";
        print_r($payload);
        echo '</pre>';
        echo '</details>';

        $result = $turi->init_session('update', $payload);

        echo '<details open>';
        echo '<summary><strong>Response: TURI init_session(update)</strong></summary>';
        echo '<pre>';
        print_r($result);
        echo '</pre>';
        echo '</details>';

        $sessionid = (string)($result['id'] ?? $courseid);
        if (isset($result['status']) && $result['status'] === 'ready_for_upload') {
            echo '</pre>';
            echo '</details>';

            $uploadresult = $turi->upload_files(
                $sessionid,
                $selectedmoodlefiles
            );

            echo '<details open>';
            echo '<summary><strong>Response: upload_files()</strong></summary>';
            echo '<pre>';
            print_r($uploadresult);
            echo '</pre>';
            echo '</details>';

            echo '<details open>';
            echo '<summary><strong>Request: complete_session()</strong></summary>';
            echo '<pre>';
            echo 'Session ID: ' . $sessionid . "\n";
            echo '</pre>';
            echo '</details>';

            $completeresult = $turi->complete_session($sessionid);

            echo '<details open>';
            echo '<summary><strong>Response: complete_session()</strong></summary>';
            echo '<pre>';
            print_r($completeresult);
            echo '</pre>';
            echo '</details>';
            // redirect(
            //     new moodle_url(
            //         '/local/turi_rag/profile.php',
            //         [
            //             'courseid' => $courseid,
            //         ]
            //     )
            // );
        }

        redirect(new moodle_url('/local/turi_rag/profile.php', ['courseid' => $courseid]));
    } catch (\Throwable $e) {
        print_error($e->getMessage(), 'local_turi_rag');
    }
}

$context = context_course::instance($course->id);
$PAGE->set_url(new moodle_url('/local/turi_rag/profile_edit.php', ['courseid' => $courseid]));
$PAGE->set_context($context);
$PAGE->set_title(get_string('edit_rag_profile', 'local_turi_rag'));
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();

$errorparam = optional_param('error', '', PARAM_ALPHA);
if ($errorparam === 'nodocuments') {
    echo $OUTPUT->notification(
        get_string('err_nodocuments', 'local_turi_rag'),
        \core\output\notification::NOTIFY_ERROR
    );
}

try {
    // echo '<details open>';
    // echo '<summary><strong>Request: get_rag_status()</strong></summary>';
    // echo '<pre>';
    // echo 'Course ID: ' . $courseid . "\n";
    // echo '</pre>';
    // echo '</details>';
    $status = $turi->get_rag_status((string)$courseid);
    // echo '<details open>';
    // echo '<summary><strong>Response: get_rag_status()</strong></summary>';
    // echo '<pre>';
    // print_r($status);
    // echo '</pre>';
    // echo '</details>';
} catch (
    moodle_exception $e
) {
    echo $OUTPUT->notification(
        $e->getMessage(),
        \core\output\notification::NOTIFY_ERROR
    );
    echo $OUTPUT->footer();
    exit;
}

$ragdocuments = $status['documents'] ?? [];
$modinfo = get_fast_modinfo($course->id);
$documents = [];
$moodlefileids = [];

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

foreach ($ragdocuments as $ragdocument) {
    $documentid = (string)($ragdocument['document_id'] ?? '');

    if ($documentid === '') {
        continue;
    }

    if (in_array($documentid, $moodlefileids, true)) {
        foreach ($documents as &$document) {
            if ($document['id'] === $documentid) {
                $document['disabled'] = true;
            }
        }

        unset($document);
    }
}

$data = [
    'documents' => $documents,
    'showcheckbox' => true,
    'showdocumentid' => false,
    'cancelurl' => (new moodle_url('/local/turi_rag/profile.php', ['courseid' => $courseid,]))->out(false),
    'saveurl' => (new moodle_url('/local/turi_rag/profile_edit.php', [
        'courseid' => $courseid, 
        'action' => 'save',
    ]))->out(false),

    'sesskey' => sesskey(),
];

echo $OUTPUT->render_from_template(
    'local_turi_rag/profile_edit',
    $data
);

echo $OUTPUT->footer();