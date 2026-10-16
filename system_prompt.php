<?php

require_once(__DIR__ . '/../../config.php');

use local_turi_rag\local\turi_client;

$courseid = required_param('courseid', PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);

$course = get_course($courseid);

require_login($course);

require_capability(
    'local/turi_rag:managerag',
    context_course::instance($courseid)
);

$courseconfig = $DB->get_record(
    'local_turi_course',
    ['courseid' => $courseid],
    'promptenabled',
    IGNORE_MISSING
);

$promptenabled = !empty($courseconfig->promptenabled);
$context = context_course::instance($course->id);

$PAGE->set_url(
    new moodle_url('/local/turi_rag/system_prompt.php', [
        'courseid' => $courseid,
    ])
);

$PAGE->set_context($context);
$PAGE->set_title(get_string('prompt', 'local_turi_rag'));
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();

$turi = new turi_client();

// try {
//     $status = $turi->get_rag_status((string)$courseid);
// } catch (moodle_exception $e) {

//     echo $OUTPUT->notification(
//         $e->getMessage(),
//         \core\output\notification::NOTIFY_ERROR
//     );

//     echo $OUTPUT->footer();
//     exit;
// }

$status = $SESSION->local_turi_rag_creation_status ?? null;

if ($status === null) {
    throw new moodle_exception('invalidparameter');
}

$systemprompts = $status['system_prompts'] ?? [];

$profilestatus = $status['course_status'] ?? 'inactive';
// $profilestatus = "active";
// $profilestatus = "inactive";
// $profilestatus = "inProgress";

if ($profilestatus !== 'active') {
    redirect(new moodle_url('/local/turi_rag/profile.php', [
        'courseid' => $courseid,
    ]));
}

if ($action === 'save') {
    confirm_sesskey();

    $systempromptid = required_param('systemprompt', PARAM_INT);
    $promptblockenabled = optional_param('promptblock_enabled', 0, PARAM_INT);

    $currentpromptid = (int)(
        $status['course_metadata']['system_prompt_id'] ?? 0
    );

    $currentblockenabled = (int)$promptenabled;

    $promptchanged = ($systempromptid !== $currentpromptid);

    $blockvisibilitychanged = (
        $promptblockenabled !== $currentblockenabled
    );
    if (!$promptchanged && !$blockvisibilitychanged) {
        redirect(
            new moodle_url('/local/turi_rag/system_prompt.php', [
                'courseid' => $courseid,
            ]),
            get_string('nochangesdetected', 'local_turi_rag'),
            null,
            \core\output\notification::NOTIFY_INFO
        );
    }

    if ($blockvisibilitychanged) {
        $record = $DB->get_record('local_turi_course', ['courseid' => $courseid], '*', IGNORE_MISSING);

        $now = time();

        if ($record) {
            $record->promptenabled = $promptblockenabled;
            $record->timemodified = $now;

            $DB->update_record('local_turi_course', $record);
        } else {
            $record = new stdClass();
            $record->courseid = $courseid;
            $record->promptenabled = $promptblockenabled;
            $record->timecreated = $now;
            $record->timemodified = $now;

            $DB->insert_record('local_turi_course', $record);
        }
    }
    if ($promptchanged) {
        $payload = [
            'id' => (string)$courseid,
            'course_metadata' => [
                'course_id' => (string)$courseid,
                'course_name' => $course->fullname,
                'instructor' => '',
                'semester' => '',
                'module_type' => '',
                'system_prompt_id' => $systempromptid,
            ],
            'documents' => [],
        ];

        try {
            echo '<details open>';
            echo '<summary><strong>Request: init_session(update)</strong></summary>';
            echo '<pre>';
            print_r($payload);
            echo '</pre>';
            echo '</details>';

            $result = $turi->init_session('update', $payload);

            echo '<details open>';
            echo '<summary><strong>Response: init_session(update)</strong></summary>';
            echo '<pre>';
            print_r($result);
            echo '</pre>';
            echo '</details>';

            $sessionid = (string)($result['id'] ?? $courseid);

            echo '<details open>';
            echo '<summary><strong>Request: complete_session()</strong></summary>';
            echo '<pre>';
            echo 'Session ID: ' . htmlspecialchars($sessionid);
            echo '</pre>';
            echo '</details>';

            $completeresult = $turi->complete_session($sessionid);

            echo '<details open>';
            echo '<summary><strong>Response: complete_session()</strong></summary>';
            echo '<pre>';
            print_r($completeresult);
            echo '</pre>';
            echo '</details>';
            echo '<details open>';
            echo '<summary><strong>UPDATE FINISHED</strong></summary>';
            echo '<pre>';
            echo 'System prompt update started for course ' . $courseid . '.';
            echo '</pre>';
            echo '</details>';
            echo $OUTPUT->continue_button(
                new moodle_url('/local/turi_rag/profile.php', [
                    'courseid' => $courseid,
                ])
            );
            echo $OUTPUT->footer();
            exit;

        } catch (moodle_exception $e) {
            echo '<details open>';
            echo '<summary><strong>ERROR: TURI UPDATE</strong></summary>';
            echo '<pre>';
            echo htmlspecialchars(
                $e->getMessage(),
                ENT_QUOTES,
                'UTF-8'
            );
            echo '</pre>';
            echo '</details>';
            echo $OUTPUT->footer();
            exit;
        }
    }

    $message = get_string('changessavedsuccessfully', 'local_turi_rag');

    if ($blockvisibilitychanged && !$promptchanged) {
        $message = get_string('blockvisibilityupdated', 'local_turi_rag');
    } else if ($promptchanged && !$blockvisibilitychanged) {
        $message = get_string('systempromptupdated', 'local_turi_rag');
    }

    redirect(
        new moodle_url('/local/turi_rag/system_prompt.php', [
            'courseid' => $courseid,
        ]),
        $message,
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

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

    'ragprofileactive' => false,
    'explanationactive' => false,
    'promptactive' => true,

    'showprompt' => ($profilestatus === 'active'),
]);

// echo $OUTPUT->heading(
//     get_string('prompt', 'local_turi_rag')
// );

$currentpromptid = (int)($status['course_metadata']['system_prompt_id'] ?? 0);

foreach ($systemprompts as &$promptgroup) {
    $promptid = (int)($promptgroup['system_prompt']['id'] ?? 0);
    $promptgroup['system_prompt']['selected'] = ($promptid === $currentpromptid);
}
unset($promptgroup);

$data = [
    'systemprompts' => $systemprompts,
    'promptblock_enabled' => $promptenabled,
    'finishurl' => (
        new moodle_url(
            '/local/turi_rag/system_prompt.php',
            [
                'courseid' => $courseid,
                'action' => 'save',
            ]
        )
    )->out(false),
    'sesskey' => sesskey(),
];

echo $OUTPUT->render_from_template(
    'local_turi_rag/system_prompt',
    $data
);


echo $OUTPUT->footer();
