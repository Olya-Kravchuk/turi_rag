<?php

namespace local_turi_rag\local;

defined('MOODLE_INTERNAL') || die();

class turi_status_client {

    /** @var turi_http_client */
    private $http;

    public function __construct(turi_http_client $http) {
        $this->http = $http;
    }

    public function get_rag_status(string $courseid): array {

        return $this->http->get(
            '/api/embedding/getStatus', 
            ['id' => $courseid,]
        );
    }

    public function get_allowed_formats(): array {

        $data = $this->http->get(
            '/api/embedding/allowedFormats'
        );

        if (!isset($data['allowed_suffixes']) || !is_array($data['allowed_suffixes'])) {
            debugging('TURI returned invalid allowedFormats response: ' . json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), DEBUG_DEVELOPER);

            throw new \moodle_exception(
                'turi_invalid_response',
                'local_turi_rag'
            );
        }

        return array_map(
            static function ($format): string {
                return strtolower(
                    ltrim((string)$format, '.')
                );
            },
            $data['allowed_suffixes']
        );
    }
}
