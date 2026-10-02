<?php

require_once(__DIR__ . '/../../config.php');

use local_turi_rag\local\turi_client;

$courseid = required_param('courseid', PARAM_INT);

$course = get_course($courseid);

require_login($course);

$context = context_course::instance($course->id);

// require_capability('local/turi_rag:view', $context);

$PAGE->set_url(
    new moodle_url('/local/turi_rag/explanation.php', [
        'courseid' => $courseid,
    ])
);

$PAGE->set_context($context);
$PAGE->set_title(get_string('explanation', 'local_turi_rag'));
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

// $profilestatus = $status['course_status'] ?? 'inactive';
$profilestatus = "active";
// $profilestatus = "inactive";
// $profilestatus = "inProgress";

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
    'explanationactive' => true,
    'promptactive' => false,

    'showprompt' => ($profilestatus === 'active'),
]);

echo $OUTPUT->render_from_template(
    'local_turi_rag/explanation_info',
    []
);

echo $OUTPUT->footer();
