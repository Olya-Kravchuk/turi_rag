<?php

define('AJAX_SCRIPT', true);

require('../../config.php');

use local_turi_rag\local\turi_http_client;
use local_turi_rag\local\turi_status_client;

$courseid = required_param(
    'courseid',
    PARAM_INT
);

$promptenabled = optional_param(
    'promptblock_enabled',
    0,
    PARAM_INT
);

$course = get_course($courseid);

require_login($course);

$http = new turi_http_client();

$turi = new turi_status_client(
    $http
);

header('Content-Type: application/json; charset=utf-8');

try {

    $statusdata = $turi->get_rag_status((string)$courseid);

    $status = strtolower($statusdata['course_status'] ?? 'unknown');

    if ($status === 'active') {

        $existing = $DB->get_record(
            'local_turi_course',
            [
                'courseid' => $courseid
            ]
        );

        if ($existing) {

            $existing->promptenabled =
                $promptenabled;

            $existing->timemodified =
                time();

            $DB->update_record(
                'local_turi_course',
                $existing
            );

        } else {
            $record = new stdClass();
            $record->courseid = $courseid;
            $record->promptenabled = $promptenabled;
            $record->timecreated = time();
            $record->timemodified = time();

            $DB->insert_record(
                'local_turi_course',
                $record
            );
        }
    }
    echo json_encode(
        [
            'success' => true,
            'status' => $status,

            'turi_response' => $statusdata,
        ],
        JSON_UNESCAPED_UNICODE
        | JSON_UNESCAPED_SLASHES
    );

} catch (Throwable $e) {
    echo json_encode(
        [
            'success' => false,
            'status' => 'error',
            'message' =>
                $e->getMessage(),
        ],
        JSON_UNESCAPED_UNICODE
        | JSON_UNESCAPED_SLASHES
    );
}
