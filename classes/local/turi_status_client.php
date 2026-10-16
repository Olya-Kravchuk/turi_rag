<?php

namespace local_turi_rag\local;

defined('MOODLE_INTERNAL') || die();

/**
 * Client for TURI status and configuration endpoints.
 *
 * Responsible for:
 *
 * - Getting RAG status for a course/profile.
 * - Getting file formats supported by TURI.
 */
class turi_status_client {

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
     * Get RAG status for a course.
     *
     * TURI endpoint:
     *
     * GET /api/embedding/getStatus?id={courseid}
     *
     * @param string $courseid
     * @return array
     * @throws \moodle_exception
     */
    public function get_rag_status(
        string $courseid
    ): array {

        return $this->http->get(
            '/api/embedding/getStatus',
            [
                'id' => $courseid,
            ]
        );
    }

    /**
     * Get allowed file formats from TURI.
     *
     * TURI endpoint:
     *
     * GET /api/embedding/allowedFormats
     *
     * Expected response:
     *
     * {
     *     "allowed_suffixes": [
     *         ".pdf",
     *         ".docx",
     *         ".txt"
     *     ]
     * }
     *
     * @return array
     * @throws \moodle_exception
     */
    public function get_allowed_formats(): array {

        $data = $this->http->get(
            '/api/embedding/allowedFormats'
        );

        if (
            !isset($data['allowed_suffixes']) ||
            !is_array($data['allowed_suffixes'])
        ) {
            debugging(
                'TURI returned invalid allowedFormats response: '
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
