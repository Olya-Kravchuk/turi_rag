<?php

require('../../config.php');

use local_turi_rag\local\turi_client;
use local_turi_rag\local\file_helper;

$courseid = required_param('courseid', PARAM_INT);
$action = required_param('action', PARAM_ALPHA);

$status = $SESSION->local_turi_rag_creation_status ?? null;

if ($status === null) {
    throw new moodle_exception('invalidparameter');
}

$systemprompts = $status['system_prompts'] ?? [];
$profilestatus = $status['course_status'] ?? 'inactive';

$course = get_course($courseid);
require_login($course);

require_capability(
    'local/turi_rag:managerag',
    context_course::instance($courseid)
);

$turi = new turi_client();

$modinfo = get_fast_modinfo($course->id);
$fs = get_file_storage();

$documents = [];

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

        $filename = $file->get_filename();

        $documents[] = [
            'id' => $file->get_id(),
            'document_name' => $cm->name,
            'file_name' => $filename,
            'supported' => file_helper::is_supported_format($filename),
            'disabled' => false,
        ];
    }
}
if ($action === 'create') {
    $selecteddocuments = optional_param_array('documents', [], PARAM_INT);
    if (empty($selecteddocuments)) {
        redirect(
            new moodle_url('/local/turi_rag/profile_creation_guide.php', [
                'courseid' => $courseid,
                'action' => 'start',
                'error' => 'nodocuments'
            ])
        );
    }
}

$data = [
    'documents' => $documents,
    'systemprompts' => $systemprompts,
    'showcheckbox' => true,
    'showdocumentid' => false,
    'currentstep' => 2,
    'createurl' => (new moodle_url(
        '/local/turi_rag/profile_creation_guide.php',
        [
            'courseid' => $courseid,
            'action' => 'create',
        ]
    ))->out(false),

    'cancelurl' => (new moodle_url(
        '/local/turi_rag/profile.php',
        [
            'courseid' => $courseid,
        ]
    ))->out(false),

    'finishurl' => (new moodle_url(
        '/local/turi_rag/profile_creation_guide.php',
        [
            'courseid' => $courseid,
            'action' => 'finish',
        ]
    ))->out(false),

    'sesskey' => sesskey(),
];

$haserror = optional_param('error', '', PARAM_ALPHA);

echo $OUTPUT->header();

if ($haserror === 'nodocuments') {
    echo $OUTPUT->notification(
        get_string('err_nodocuments', 'local_turi_rag'),
        \core\output\notification::NOTIFY_ERROR
    );
}

switch ($action) {

    case 'start':
        $data['currentstep'] = 2;
        break;

    case 'create':
        $selecteddocuments = optional_param_array(
            'documents',
            [],
            PARAM_INT
        );

        $data['selecteddocuments'] = $selecteddocuments;

        if (empty($selecteddocuments)) {
            throw new moodle_exception('invalidparameter');
        }
        $selectedmoodlefiles = [];
        $selectedfiles = [];

        foreach ($selecteddocuments as $fileid) {

            $file = $fs->get_file_by_id($fileid);

            if (!$file || $file->is_directory()) {
                continue;
            }

            $filename = $file->get_filename();

            $extension = strtolower(
                pathinfo($filename, PATHINFO_EXTENSION)
            );

            $documentname = '';

            foreach ($documents as $document) {
                if ((int)$document['id'] === (int)$fileid) {
                    $documentname = $document['document_name'];
                    break;
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
        

        if (empty($selectedfiles)) {
            throw new moodle_exception('invalidparameter');
        }

        $SESSION->local_turi_rag_selected_documents = $selectedfiles;

        $SESSION->local_turi_rag_selected_file_ids = $selecteddocuments;

        $data['selecteddocuments'] = $selecteddocuments;

        $data['currentstep'] = 4;
        break;


    case 'finish':
        $systempromptid = optional_param(
            'systemprompt',
            0,
            PARAM_INT
        );

        $promptblockenabled = optional_param(
            'promptblock_enabled',
            0,
            PARAM_INT
        );

        if (!$systempromptid) {
            throw new moodle_exception('invalidparameter');
        }

        $selectedfiles = $SESSION->local_turi_rag_selected_documents ?? [];

        if (empty($selectedfiles)) {
            throw new moodle_exception('invalidparameter');
        }
        $creator = \core_user::get_user($course->userid);

        $instructor = '';

        if ($creator) {
            $instructor = fullname($creator);
        }
        $coursemetadata = [
            'course_id' => (string)$courseid,
            'course_name' => $course->fullname,
            'instructor' => $instructor,
            'semester' => '',
            'module_type' => '',
            'system_prompt_id' => $systempromptid,
        ];
        $payload = [
            'id' => (string)$courseid,
            'course_metadata' => $coursemetadata,
            'documents' => $selectedfiles,
        ];


        try {
            // echo '<details open>';
            // echo '<summary><strong>Request: init_session()</strong></summary>';
            // echo '<pre>';
            // print_r($payload);
            // echo '</pre>';
            // echo '</details>';

            $result = $turi->init_session('create', $payload);

            // echo '<details open>';
            // echo '<summary><strong>Response: init_session()</strong></summary>';
            // echo '<pre>';
            // print_r($result);
            // echo '</pre>';
            // echo '</details>';

            $sessionid = (string)($result['id'] ?? $courseid);

            $selectedmoodlefiles = [];

            foreach ($selectedfiles as $selectedfile) {

                $fileid = (int)$selectedfile['document_id'];

                $file = $fs->get_file_by_id($fileid);

                if (!$file || $file->is_directory()) {
                    continue;
                }

                $selectedmoodlefiles[] = $file;
            }

            if (empty($selectedmoodlefiles)) {
                throw new moodle_exception('invalidparameter');
            }

            // echo '<details open>';
            // echo '<summary><strong>Request: upload_files()</strong></summary>';
            // echo '<pre>';
            // echo "Session ID:\n";
            // echo $sessionid . "\n\n";
            // echo "Files:\n";

            // foreach ($selectedmoodlefiles as $file) {
            //     echo 'ID: ' . $file->get_id() . "\n";
            //     echo 'Filename: ' . $file->get_filename() . "\n";
            //     echo 'Mimetype: ' . $file->get_mimetype() . "\n";
            //     echo 'Size: ' . $file->get_filesize() . " bytes\n";
            //     echo "-------------------------\n";
            // }
            // echo '</pre>';
            // echo '</details>';

            $uploadresult = $turi->upload_files(
                $sessionid,
                $selectedmoodlefiles
            );

            // echo '<details open>';
            // echo '<summary><strong>Response: upload_files()</strong></summary>';
            // echo '<pre>';
            // print_r($uploadresult);
            // echo '</pre>';
            // echo '</details>';

            // echo '<details open>';
            // echo '<summary><strong>Request: complete_session()</strong></summary>';
            // echo '<pre>';
            // echo "Session ID: ";
            // echo $sessionid;
            // echo "\n";
            // echo '</pre>';
            // echo '</details>';

            $completeresult = $turi->complete_session(
                $sessionid
            );

            // echo '<details open>';
            // echo '<summary><strong>Response: complete_session()</strong></summary>';
            // echo '<pre>';
            // print_r($completeresult);
            // echo '</pre>';
            // echo '</details>';

            $creationstarted = (($completeresult['status'] ?? '') === 'started');


            $SESSION->local_turi_rag_creation_result = [
                'init' => $result,
                'upload' => $uploadresult,
                'complete' => $completeresult,
                'system_prompt_id' => $systempromptid,
                'promptblock_enabled' => $promptblockenabled,
                'documents' => $selectedfiles,
            ];

            unset(
                $SESSION->local_turi_rag_selected_documents,
                $SESSION->local_turi_rag_selected_file_ids
            );

        } catch (moodle_exception $e) {
            echo $OUTPUT->notification(
                $e->getMessage(),
                \core\output\notification::NOTIFY_ERROR
            );

            echo $OUTPUT->footer();
            exit;
        }
        

        $data['currentstep'] = 6;
        $data['profileinprogress'] = true;
        $data['profileactive'] = false;
        $data['profileinactive'] = false;

        $statusurl = (new moodle_url('/local/turi_rag/check_status.php', [
            'courseid' => $courseid,
            'promptblock_enabled' => $promptblockenabled,
        ]))->out(false);

        $redirecturl = (new moodle_url('/local/turi_rag/profile.php', [
            'courseid' => $courseid,
        ]))->out(false);

        $data['statusurl'] = $statusurl;
        $data['redirecturl'] = $redirecturl;
    
        break;

    default:
        throw new moodle_exception('invalidparameter');
}



$PAGE->requires->js_call_amd(
    'local_turi_rag/stepper',
    'init'
);

if ($action === 'finish') {
    // $PAGE->requires->js_call_amd(
    //     'local_turi_rag/profile_notification',
    //     'init',
    //     [
    //         $data['redirecturl'],
    //         5000,
    //     ]
    // );
    $PAGE->requires->js_call_amd(
        'local_turi_rag/profile_creation',
        'init',
        [
            $data['statusurl'],
            $data['redirecturl']
        ]
    );
}

echo $OUTPUT->render_from_template(
    'local_turi_rag/wizard_progress',
    $data
);

switch ($action) {

    case 'start':
        echo $OUTPUT->render_from_template(
            'local_turi_rag/profile_create',
            $data
        );
        
        $formats = $turi->get_allowed_formats();

        echo '<details>';
        echo '<summary>Response</summary>';
        echo '<pre>';
        print_r($formats);
        echo '</pre>';
        echo '</details>';
        break;

    case 'create':
        echo $OUTPUT->render_from_template(
            'local_turi_rag/system_prompt',
            $data
        );
        break;
    
    case 'finish':

        echo $OUTPUT->render_from_template(
            'local_turi_rag/profile_creation_result',
            $data
        );
        echo '<details>';
        echo '<summary>Response</summary>';
        echo '<pre>';
        print_r($status);
        echo '</pre>';
        echo '</details>';
        break;
}

echo $OUTPUT->footer();
