<?php

namespace local_turi_rag\local;

defined('MOODLE_INTERNAL') || die();

class turi_session_client {

    /** @var turi_http_client */
    private $http;

    public function __construct(turi_http_client $http) {
        $this->http = $http;
    }

    public function init_session(string $modus, array $payload): array {
        $payload['modus'] = $modus;

        return $this->http->post('/api/embedding/session', $payload);
    }

    public function upload_files(string $sessionid, array $files): array {

        if (empty($files)) {
            throw new \moodle_exception(
                'turi_file_upload_error',
                'local_turi_rag'
            );
        }

        $path = '/api/embedding/session/' . rawurlencode($sessionid) . '/upload';

        return $this->http->post_multipart($path, $files);
    }

    public function complete_session(string $sessionid): array {

        $path = '/api/embedding/session/' . rawurlencode($sessionid) . '/complete';

        return $this->http->post($path);
    }
}
