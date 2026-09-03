<?php

defined('MOODLE_INTERNAL') || die();

/**
 * Add TURI RAG to course navigation.
 *
 * @param navigation_node $navigation
 * @param stdClass $course
 * @param context_course $context
 */
function local_turi_rag_extend_navigation_course(
    navigation_node $navigation,
    stdClass $course,
    context_course $context
) {
    $url = new moodle_url('/local/turi_rag/index.php', [
        'courseid' => $course->id,
    ]);

    $navigation->add(
        get_string('ragprofile', 'local_turi_rag'),
        $url,
        navigation_node::TYPE_CUSTOM,
        'local_turi_rag',
        'turi_rag'
    );
}