<?php

require_once(__DIR__ . '/../../config.php');

$courseid = required_param('courseid', PARAM_INT);

$course = get_course($courseid);

require_login($course);

$context = context_course::instance($course->id);

$PAGE->set_url(
    new moodle_url('/local/turi_rag/index.php', [
        'courseid' => $courseid,
    ])
);

$PAGE->set_context($context);

$PAGE->set_title(
    get_string('ragprofile', 'local_turi_rag')
);

$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();

echo $OUTPUT->heading(
    get_string('ragprofile', 'local_turi_rag')
);

echo $OUTPUT->notification(
    get_string('fakewarning', 'local_turi_rag'),
    \core\output\notification::NOTIFY_INFO
);

echo html_writer::div(
    'Course ID: ' . $courseid,
    'alert alert-secondary'
);

echo html_writer::div(
    'RAG-Profil für diesen Kurs',
    'mt-3'
);

echo $OUTPUT->footer();