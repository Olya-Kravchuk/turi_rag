<?php

/**
 * Find RAG documents that no longer exist in Moodle.
 *
 * @param int $courseid Moodle course ID.
 * @param array $ragdocuments Documents stored in the RAG profile.
 * @return array Documents missing in Moodle.
 */
function local_turi_rag_find_missing_documents(
    int $courseid,
    array $ragdocuments
): array {
    $modinfo = get_fast_modinfo($courseid);
    $fs = get_file_storage();
    $moodlefileids = [];

    foreach ($modinfo->get_instances_of('resource') as $cm) {
        if (!$cm->uservisible) {
            continue;
        }

        $context = context_module::instance($cm->id);

        $files = $fs->get_area_files(
            $context->id,
            'mod_resource',
            'content',
            0,
            'sortorder',
            false
        );

        foreach ($files as $file) {
            if (!$file->is_directory()) {
                $moodlefileids[] = (string)$file->get_id();
            }
        }
    }

    $missingdocuments = [];

    foreach ($ragdocuments as $document) {
        $documentid = (string)($document['document_id'] ?? '');

        if (
            $documentid !== '' &&
            !in_array($documentid, $moodlefileids, true)
        ) {
            $missingdocuments[] = $document;
        }
    }

    return $missingdocuments;
}