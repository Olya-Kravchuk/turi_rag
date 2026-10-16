<?php

namespace local_turi_rag\local;

defined('MOODLE_INTERNAL') || die();

/**
 * Client for TURI profile deletion endpoints.
 *
 * This client is responsible only for direct deletion operations.
 *
 * Profile creation and profile updates are handled through
 * turi_session_client using the create/update session modus.
 */
class turi_profile_client {

    /** @var turi_http_client */
    private $http;

    /**
     * Constructor.
     *
     * @param turi_http_client $http
     */
    public function __construct(
        turi_http_client $http
    ) {
        $this->http = $http;
    }

    /**
     * Delete a complete RAG profile.
     *
     * TURI endpoint:
     *
     * GET /api/embedding/entry_delete?id={id}
     *
     * @param string $courseid
     * @return bool
     * @throws \moodle_exception
     */
    public function delete_profile(
        string $courseid
    ): bool {

        $data = $this->http->get(
            '/api/embedding/entry_delete',
            [
                'id' => $courseid,
            ]
        );

        /*
         * TURI returns the deleted id on success.
         *
         * Example:
         *
         * {
         *     "script": "embedding/entry_delete",
         *     "id": "2",
         *     "datum": "2026-10-01",
         *     "zeit": "12:15:36"
         * }
         */
        if (
            !isset($data['id']) ||
            (string)$data['id'] !== (string)$courseid
        ) {
            debugging(
                'TURI entry_delete returned unexpected response: '
                . json_encode(
                    $data,
                    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                ),
                DEBUG_DEVELOPER
            );

            throw new \moodle_exception(
                'turi_invalid_response',
                'local_turi_rag'
            );
        }

        return true;
    }

    /**
     *
     * @param string $id
     * @return bool
     * @throws \moodle_exception
     */
    public function single_delete(
        string $id
    ): array {

        $data = $this->http->get(
            '/api/embedding/entry_singleDelete',
            [
                'id' => $id,
            ]
        );

        /*
         * TURI returns the deleted id on success.
         *
         * Example:
         *
         * {
         *     "script": "embedding/entry_singleDelete",
         *     "id": "123",
         *     "datum": "2026-10-01",
         *     "zeit": "12:15:36"
         * }
         */
        if (
            !isset($data['id']) ||
            (string)$data['id'] !== (string)$id
        ) {
            debugging(
                'TURI entry_singleDelete returned unexpected response: '
                . json_encode(
                    $data,
                    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
                ),
                DEBUG_DEVELOPER
            );

            throw new \moodle_exception(
                'turi_invalid_response',
                'local_turi_rag'
            );
        }

        return $data;
    }
}