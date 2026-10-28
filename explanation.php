<?php

require_once(__DIR__ . '/../../config.php');

$courseid = required_param('courseid', PARAM_INT);

$course = get_course($courseid);

require_login($course);

$context = context_course::instance($course->id);

require_capability(
    'local/turi_rag:managerag',
    context_course::instance($courseid)
);

$PAGE->set_url(
    new moodle_url('/local/turi_rag/explanation.php', [
        'courseid' => $courseid,
    ])
);

$PAGE->set_context($context);
$PAGE->set_title(get_string('explanation', 'local_turi_rag'));
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();

$status = $SESSION->local_turi_rag_creation_status ?? null;

if ($status === null) {
    throw new moodle_exception('invalidparameter');
}

$profilestatus = $status['course_status'] ?? 'inactive';

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
