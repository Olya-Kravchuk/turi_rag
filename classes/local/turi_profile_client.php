<?php

namespace local_turi_rag\local;

defined('MOODLE_INTERNAL') || die();

class turi_profile_client {

    /** @var turi_http_client */
    private $http;

    public function __construct(turi_http_client $http) {
        $this->http = $http;
    }

    public function delete_profile(string $courseid): array {

        $data = $this->http->get(
            '/api/embedding/entry_delete',
            [
                'id' => $courseid,
            ]
        );

        if (!isset($data['id']) || (string)$data['id'] !== (string)$courseid) {
            debugging('TURI entry_delete returned unexpected response: ' . json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), DEBUG_DEVELOPER);

            throw new \moodle_exception(
                'turi_invalid_response',
                'local_turi_rag'
            );
        }

        return $data;
    }

    public function single_delete(string $courseid, array $documentids): array {

        $data = $this->http->get(
            '/api/embedding/entry_singleDelete',
            ['id' => $courseid, 'documentids' => $documentids,]
        );

        if (!isset($data['id']) || (string)$data['id'] !== (string)$courseid) {
            debugging('TURI entry_singleDelete returned unexpected response: ' . json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), DEBUG_DEVELOPER);

            throw new \moodle_exception(
                'turi_invalid_response',
                'local_turi_rag'
            );
        }

        return $data;
    }
}