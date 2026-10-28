<?php

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/locallib.php');

use local_turi_rag\local\turi_client;

$courseid = required_param('courseid', PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);

$course = get_course($courseid);

require_login($course);

$context = context_course::instance($course->id);

require_capability(
    'local/turi_rag:managerag',
    context_course::instance($courseid)
);

$turi = new turi_client();

if ($action === 'delete') {
    confirm_sesskey();

    try {
        $success = $turi->delete_profile((string)$courseid);

        echo '<details open>';
        echo '<summary>Response</summary>';
        echo '<pre>';
        print_r($success);
        echo '</pre>';
        echo '</details>';
        exit;
        if ($success) {
            $record = $DB->get_record(
                'local_turi_course',
                ['courseid' => $courseid],
                '*',
                IGNORE_MISSING
            );

            if ($record) {
                $record->promptenabled = 0;
                $record->timemodified = time();

                $DB->update_record(
                    'local_turi_course',
                    $record
                );
            }

            redirect(
                new moodle_url('/local/turi_rag/profile.php', ['courseid' => $courseid]),
                get_string('profile_delete_accepted', 'local_turi_rag'),
                null,
                \core\output\notification::NOTIFY_SUCCESS
            );
        } else {
            redirect(
                new moodle_url('/local/turi_rag/profile.php', ['courseid' => $courseid]),
                get_string('error_deleting_profile', 'local_turi_rag'),
                null,
                \core\output\notification::NOTIFY_ERROR
            );
        }
    } catch (\Exception $e) {
        redirect(
            new moodle_url('/local/turi_rag/profile.php', ['courseid' => $courseid]),
            get_string('error_deleting_profile', 'local_turi_rag'),
            null,
            \core\output\notification::NOTIFY_ERROR
        );
    }
}

$PAGE->set_url(
    new moodle_url('/local/turi_rag/profile.php', [
        'courseid' => $courseid,
    ])
);

$PAGE->set_context($context);

$PAGE->set_title(
    get_string('ragprofile', 'local_turi_rag')
);

$PAGE->set_heading($course->fullname);

$PAGE->requires->js_call_amd('local_turi_rag/delete_confirm', 'init', ['#delete-profile-btn']);

echo $OUTPUT->header();

try {
    $status = $turi->get_rag_status((string)$courseid);
    
    $SESSION->local_turi_rag_creation_status = $status;
    // $status = turi_client::get_rag_status((string)'9876');
    // echo '<pre>';
    // var_dump($status);
    // echo '</pre>';
    // exit;

} catch (moodle_exception $e) {
    echo $OUTPUT->notification(
        $e->getMessage(),
        \core\output\notification::NOTIFY_ERROR
    );

    echo $OUTPUT->footer();
    exit;
}



$profilestatus = $status['course_status'] ?? 'inactive';
// $profilestatus = "active";
// $profilestatus = "inactive";
// $profilestatus = "inprogress";

echo $OUTPUT->render_from_template('local_turi_rag/tabs', [
    'ragprofileurl' => new moodle_url('/local/turi_rag/profile.php', [
        'courseid' => $courseid,
    ]),

    'explanationurl' => new moodle_url('/local/turi_rag/explanation.php', [
        'courseid' => $courseid,
    ]),

    'prompturl' => new moodle_url('/local/turi_rag/system_prompt.php', [
        'courseid' => $courseid,
    ]),

    'ragprofileactive' => true,
    'explanationactive' => false,
    'promptactive' => false,

    'showprompt' => ($profilestatus === 'active'),
]);


if ($profilestatus === 'active') {
    $ragdocuments = $status['documents'] ?? [];

    $missingdocuments = local_turi_rag_find_missing_documents(
        $courseid,
        $ragdocuments
    );

    $data = [
        // 'documents' => $documents,

        'showcheckbox' => false,
        'showdocumentid' => true,

        'editurl' => (new moodle_url(
            '/local/turi_rag/profile_edit.php',
            [
                'courseid' => $courseid,
            ]
        ))->out(false),

        'removedocumentsurl' => (new moodle_url(
            '/local/turi_rag/profile_remove_documents.php',
            [
                'courseid' => $courseid,
            ]
        ))->out(false),

        'deleteurl' => (new moodle_url('/local/turi_rag/profile.php', [
            'courseid' => $courseid,
            'action' => 'delete',
            'sesskey' => sesskey(),
        ]))->out(false),

        'documents' => $status['documents'] ?? [],
        
    ];

    echo $OUTPUT->render_from_template(
        'local_turi_rag/profile',
        $data
    );
    if (!empty($missingdocuments)) {
        if (!empty($missingdocuments)) {
            echo $OUTPUT->render_from_template(
                'local_turi_rag/missing_documents',
                [
                    'documents' => $missingdocuments,
                    'showcheckbox' => false,
                    'showdocumentid' => true,
                ]
            );
        }
    }

    echo '<details>';
    echo '<summary>Response</summary>';
    echo '<pre>';
    print_r($status);
    echo '</pre>';
    echo '</details>';


} else if ($profilestatus === 'inprogress' || $profilestatus === 'inProgress') {

    echo $OUTPUT->render_from_template(
        'local_turi_rag/profile_creation_result',
        []
    );
    echo '<details>';
    echo '<summary>Response</summary>';
    echo '<pre>';
    print_r($status);
    echo '</pre>';
    echo '</details>';


} else {

    $data = [
        // 'documents' => $documents,
        // 'canmanage' => has_capability(
        //     'local/turi_rag:manage',
        //     $context
        // ),
        'showcheckbox' => true,
        'showdocumentid' => false,

        'starturl' => (new moodle_url(
            '/local/turi_rag/profile_creation_guide.php',
            [
                'courseid' => $courseid,
                'action' => 'start',
            ]
        ))->out(false),
    ];

    echo $OUTPUT->render_from_template(
        'local_turi_rag/explanation_empty',
        $data
    );
    echo '<details>';
    echo '<summary>Response</summary>';
    echo '<pre>';
    print_r($status);
    echo '</pre>';
    echo '</details>';

}


echo $OUTPUT->footer();
