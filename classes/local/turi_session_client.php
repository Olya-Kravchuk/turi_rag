<?php

namespace local_turi_rag\local;

defined('MOODLE_INTERNAL') || die();

/**
 * Client for TURI session endpoints.
 *
 * A TURI session is used for operations such as:
 *
 * - create a new profile;
 * - update an existing profile.
 *
 * The same session flow is used for both operations.
 * The operation is determined by the $mode passed to init_session().
 */
class turi_session_client {

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
     * Initialize a TURI session.
     *
     * The mode determines what the session should do.
     *
     * Examples:
     *
     * - create
     * - update
     *
     * The mode is added to the payload sent to TURI.
     *
     * TURI endpoint:
     *
     * POST /api/embedding/session
     *
     * @param string $mode
     * @param array $payload
     * @return array
     * @throws \moodle_exception
     */
    public function init_session(
        string $modus,
        array $payload
    ): array {

        $payload['modus'] = $modus;

        return $this->http->post(
            '/api/embedding/session',
            $payload
        );
    }

    /**
     * Upload files to an existing TURI session.
     *
     * All files are sent using the multipart field name "files".
     *
     * TURI endpoint:
     *
     * POST /api/embedding/session/{sessionid}/upload
     *
     * @param string $sessionid
     * @param array $files
     * @return array
     * @throws \moodle_exception
     */
    public function upload_files(
        string $sessionid,
        array $files
    ): array {

        if (empty($files)) {
            throw new \moodle_exception(
                'turi_file_upload_error',
                'local_turi_rag'
            );
        }

        $path = '/api/embedding/session/'
            . rawurlencode($sessionid)
            . '/upload';

        return $this->http->post_multipart(
            $path,
            $files
        );
    }

    /**
     * Complete a TURI session.
     *
     * TURI endpoint:
     *
     * POST /api/embedding/session/{sessionid}/complete
     *
     * The endpoint does not require a request body.
     *
     * @param string $sessionid
     * @return array
     * @throws \moodle_exception
     */
    public function complete_session(
        string $sessionid
    ): array {

        $path = '/api/embedding/session/'
            . rawurlencode($sessionid)
            . '/complete';

        return $this->http->post($path);
    }
}
