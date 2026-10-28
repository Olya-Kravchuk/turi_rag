<?php

namespace local_turi_rag\local;

defined('MOODLE_INTERNAL') || die();

require_once($GLOBALS['CFG']->libdir . '/filelib.php');

class turi_client {
    private $http;
    private $status;
    private $session;
    private $profile;

    public function __construct() {
        $this->http = new turi_http_client();
        $this->status = new turi_status_client($this->http);
        $this->session = new turi_session_client($this->http);
        $this->profile = new turi_profile_client($this->http);
    }

    public function get_rag_status(string $courseid): array {
        return $this->status->get_rag_status($courseid);
    }

    public function get_allowed_formats(): array {
        return $this->status->get_allowed_formats();
    }

    public function init_session(string $mode, array $payload): array {
        return $this->session->init_session($mode, $payload);
    }

    public function upload_files(string $sessionid, array $files): array {
        return $this->session->upload_files($sessionid, $files);
    }

    public function complete_session(string $sessionid): array {
        return $this->session->complete_session($sessionid);
    }

    public function delete_profile(string $courseid): array {
        return $this->profile->delete_profile($courseid);
    }

    public function single_delete(string $courseid, array $documents): array {
        return $this->profile->single_delete($courseid, $documents);
    }
}
