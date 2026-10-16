<?php

namespace local_turi_rag\local;

defined('MOODLE_INTERNAL') || die();

require_once($GLOBALS['CFG']->libdir . '/filelib.php');

/**
 * Facade for communication with TURI.
 *
 * This class provides a single entry point for the rest of the
 * Moodle plugin. The actual API communication is delegated to
 * specialised clients:
 *
 * - turi_http_client
 * - turi_status_client
 * - turi_session_client
 * - turi_profile_client
 */
class turi_client {

    /** @var turi_http_client */
    private $http;

    /** @var turi_status_client */
    private $status;

    /** @var turi_session_client */
    private $session;

    /** @var turi_profile_client */
    private $profile;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->http = new turi_http_client();
        $this->status = new turi_status_client($this->http);
        $this->session = new turi_session_client($this->http);
        $this->profile = new turi_profile_client($this->http);
    }

    /**
     * Get RAG status for a course/profile.
     *
     * @param string $courseid
     * @return array
     * @throws \moodle_exception
     */
    public function get_rag_status(string $courseid): array {
        return $this->status->get_rag_status($courseid);
    }

    /**
     * Get file formats supported by TURI.
     *
     * @return array
     * @throws \moodle_exception
     */
    public function get_allowed_formats(): array {
        return $this->status->get_allowed_formats();
    }

    /**
     * Initialize a TURI session.
     *
     * The mode determines what the session is intended to do.
     * For example:
     *
     * - create - create a new profile
     * - update - update an existing profile
     *
     * @param string $mode
     * @param array $payload
     * @return array
     * @throws \moodle_exception
     */
    public function init_session(
        string $mode,
        array $payload
    ): array {
        return $this->session->init_session(
            $mode,
            $payload
        );
    }

    /**
     * Upload files to an existing TURI session.
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
        return $this->session->upload_files(
            $sessionid,
            $files
        );
    }

    /**
     * Complete an existing TURI session.
     *
     * @param string $sessionid
     * @return array
     * @throws \moodle_exception
     */
    public function complete_session(
        string $sessionid
    ): array {
        return $this->session->complete_session(
            $sessionid
        );
    }

    /**
     * Delete an entire TURI profile/entry.
     *
     * This is used when the complete RAG profile for a course
     * needs to be removed.
     *
     * @param string $courseid
     * @return bool
     * @throws \moodle_exception
     */
    public function delete_profile(
        string $courseid
    ): bool {
        return $this->profile->delete_profile(
            $courseid
        );
    }

    /**
     * Delete multiple documents from a profile.
     *
     * This is different from deleting the complete profile.
     *
     * @param string $courseid
     * @param array $documents
     * @return array
     * @throws \moodle_exception
     */
    public function single_delete(
        // string $courseid,
        // array $documents
        $id
    ): array {
        return $this->profile->single_delete(
            $id
            // $courseid,
            // $documents
        );
    }
}
