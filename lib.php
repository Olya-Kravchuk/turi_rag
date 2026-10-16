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

    if (!has_capability('local/turi_rag:managerag', $context)) {
        return;
    }
    
    $url = new moodle_url('/local/turi_rag/profile.php', [
        'courseid' => $course->id,
    ]);

    $navigation->add(
        get_string('pluginname', 'local_turi_rag'),
        $url,
        navigation_node::TYPE_CUSTOM,
        'local_turi_rag',
        'turi_rag'
    );
    $navigation->add(
        get_string('explanation', 'local_turi_rag'),
        new moodle_url('/local/turi_rag/explanation.php', [
            'courseid' => $course->id,
        ]),
        navigation_node::TYPE_CUSTOM,
        'local_turi_rag',
        'explanation'
    );

    $navigation->add(
        get_string('prompt', 'local_turi_rag'),
        new moodle_url('/local/turi_rag/system_prompt.php', [
            'courseid' => $course->id,
        ]),
        navigation_node::TYPE_CUSTOM,
        'local_turi_rag',
        'prompt'
    );
}