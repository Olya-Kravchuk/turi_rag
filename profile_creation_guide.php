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

/*
 * Get documents available in the course.
 */
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


/*
 * Default data.
 */
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

echo $OUTPUT->header();

// try {
//     $status = turi_client::get_rag_status((string)$courseid);
// } catch (moodle_exception $e) {
//     echo $OUTPUT->notification(
//         $e->getMessage(),
//         \core\output\notification::NOTIFY_ERROR
//     );

//     echo $OUTPUT->footer();
//     exit;
// }

// $profilestatus = $status['course_status'] ?? 'inactive';
// $profilestatus = "active";
// $profilestatus = "inactive";
// $profilestatus = "inProgress";

/*
 * Handle wizard action.
 */
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
                // 'author' => '',
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
        $coursemetadata = [
            'course_id' => (string)$courseid,
            'course_name' => $course->fullname,
            'instructor' => '',
            'semester' => '',
            'module_type' => '',
            'system_prompt_id' => $systempromptid,
        ];
        $payload = [
            'id' => (string)$courseid,
            'modus' => 'create',
            'course_metadata' => $coursemetadata,
            'documents' => $selectedfiles,
        ];


        try {
            // echo '<details open>';
            // echo '<summary>Request sent to TURI</summary>';
            // echo '<pre>';
            // print_r($payload);
            // echo '</pre>';
            // echo '</details>';

            $result = turi_client::init_session($payload);

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

            $uploadresult = turi_client::upload_files(
                $sessionid,
                $selectedmoodlefiles
            );

            $completeresult = turi_client::complete_session(
                $sessionid
            );

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

    
        $data['profilestatus'] = $profilestatus;

        $data['profileactive'] = ($profilestatus === 'active');

        $data['profileinprogress'] = ($profilestatus === 'inprogress');

        $data['profileinactive'] = ($profilestatus === 'inactive');

        $data['redirecturl'] = (new moodle_url(
            '/local/turi_rag/profile.php',
            [
                'courseid' => $courseid,
            ]
        ))->out(false);

        break;

    default:
        throw new moodle_exception('invalidparameter');
}



$PAGE->requires->js_call_amd(
    'local_turi_rag/stepper',
    'init'
);

if ($action === 'finish') {
    $PAGE->requires->js_call_amd(
        'local_turi_rag/profile_notification',
        'init',
        [
            $data['redirecturl'],
            3000,
        ]
    );
}

echo $OUTPUT->render_from_template(
    'local_turi_rag/wizard_progress',
    $data
);

/*
 * Render current wizard content.
 */
switch ($action) {

    case 'start':
        echo $OUTPUT->render_from_template(
            'local_turi_rag/profile_create',
            $data
        );
        
        $formats = turi_client::get_allowed_formats();

        echo '<details>';
        echo '<summary>Response</summary>';
        echo '<pre>';
        print_r($formats);
        echo '</pre>';
        echo '</details>';
        break;

    // case 'documents':
    //     $selecteddocuments = optional_param_array(
    //         'documents',
    //         [],
    //         PARAM_INT
    //     );

    //     if (empty($selecteddocuments)) {
    //         throw new moodle_exception('invalidparameter');
    //     }

    //     $data['selecteddocuments'] = $selecteddocuments;
    //     $data['currentstep'] = 3;
    //     break;

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

        break;
}

echo $OUTPUT->footer();
