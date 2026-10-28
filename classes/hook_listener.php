<?php

namespace local_turi_rag;

defined('MOODLE_INTERNAL') || die();

use core_course\hook\before_course_deleted;
use local_turi_rag\local\turi_client;

class hook_listener {

    public static function before_course_deleted(before_course_deleted $hook): void {
        global $DB;

        $courseid = $hook->courseid;

        $DB->delete_records(
            'local_turi_course',
            ['courseid' => $courseid]
        );

        try {
            $turi = new turi_client();
            $turi->delete_profile((string)$courseid);
        } catch (\Exception $e) {
            debugging('Failed to delete TURI RAG profile for course ' . $courseid . ': ' . $e->getMessage(), DEBUG_DEVELOPER);
        }
    }
}
