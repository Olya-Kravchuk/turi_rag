<?php

defined('MOODLE_INTERNAL') || die();

$callbacks = [
    [
        'hook' => \core_course\hook\before_course_deleted::class,
        'callback' => \local_turi_rag\hook_listener::class . '::before_course_deleted',
    ],
];
