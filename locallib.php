<?php

function local_turi_rag_get_profile_status(int $courseid): string {
    $status = turi_client::get_rag_status((string)$courseid);

    return $status['course_status'] ?? 'inactive';
}

function local_turi_rag_get_profile_status_or_error(int $courseid): ?string {
    global $OUTPUT;

    try {
        return local_turi_rag_get_profile_status($courseid);

    } catch (moodle_exception $e) {

        echo $OUTPUT->notification(
            $e->getMessage(),
            \core\output\notification::NOTIFY_ERROR
        );

        echo $OUTPUT->footer();

        return null;
    }
}