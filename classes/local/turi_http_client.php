<?php

namespace local_turi_rag\local;

defined('MOODLE_INTERNAL') || die();

require_once($GLOBALS['CFG']->libdir . '/filelib.php');

/**
 * Low-level HTTP client for communication with TURI.
 *
 * Responsible only for:
 *
 * - TURI base URL.
 * - HTTP GET requests.
 * - HTTP POST requests.
 * - JSON encoding/decoding.
 * - Multipart file uploads.
 * - cURL/HTTP error handling.
 */
class turi_http_client {

    /**
     * Get TURI base URL.
     *
     * @return string
     * @throws \moodle_exception
     */
    private function get_base_url(): string {

        $host = get_config(
            'local_turi_rag',
            'embedding_host'
        );

        $port = get_config(
            'local_turi_rag',
            'embedding_port'
        );

        if (empty($host) || empty($port)) {

            debugging(
                'TURI configuration missing. '
                . 'Host=' . (string)$host
                . ' Port=' . (string)$port,
                DEBUG_DEVELOPER
            );

            throw new \moodle_exception(
                'embedding_config_missing',
                'local_turi_rag'
            );
        }

        return 'http://' . $host . ':' . $port;
    }

    /**
     * Build complete TURI URL.
     *
     * @param string $path
     * @param array $query
     * @return string
     */
    private function build_url(
        string $path,
        array $query = []
    ): string {

        $url = $this->get_base_url()
            . '/'
            . ltrim($path, '/');

        if (!empty($query)) {

            $url .= '?'
                . http_build_query(
                    $query,
                    '',
                    '&',
                    PHP_QUERY_RFC3986
                );
        }

        return $url;
    }

    /**
     * Debug outgoing request.
     *
     * IMPORTANT:
     * Uses debugging(), never echo.
     *
     * @param string $method
     * @param string $url
     * @param mixed $request
     * @return void
     */
    private function debug_request(
        string $method,
        string $url,
        $request = null
    ): void {

        debugging(
            "\n"
            . "========== TURI REQUEST ==========\n"
            . "METHOD: " . $method . "\n"
            . "URL: " . $url . "\n"
            . "REQUEST:\n"
            . (
                $request === null
                    ? '[none]'
                    : json_encode(
                        $request,
                        JSON_PRETTY_PRINT
                        | JSON_UNESCAPED_UNICODE
                        | JSON_UNESCAPED_SLASHES
                    )
            )
            . "\n"
            . "==================================",
            DEBUG_DEVELOPER
        );
    }

    /**
     * Debug incoming response.
     *
     * @param string $method
     * @param string $url
     * @param mixed $response
     * @param int $httpcode
     * @param int $errno
     * @param string $error
     * @return void
     */
    private function debug_response(
        string $method,
        string $url,
        $response,
        int $httpcode,
        int $errno = 0,
        string $error = ''
    ): void {

        debugging(
            "\n"
            . "========== TURI RESPONSE =========\n"
            . "METHOD: " . $method . "\n"
            . "URL: " . $url . "\n"
            . "HTTP STATUS: " . $httpcode . "\n"
            . "CURL ERRNO: " . $errno . "\n"
            . "CURL ERROR: " . $error . "\n"
            . "RAW RESPONSE:\n"
            . (string)$response
            . "\n"
            . "==================================",
            DEBUG_DEVELOPER
        );
    }

    /**
     * Perform GET request.
     *
     * @param string $path
     * @param array $query
     * @return array
     * @throws \moodle_exception
     */
    public function get(
        string $path,
        array $query = []
    ): array {

        $url = $this->build_url(
            $path,
            $query
        );

        $this->debug_request(
            'GET',
            $url,
            $query
        );

        $curl = new \curl();

        $curl->setHeader([
            'Accept: application/json',
        ]);

        $response = $curl->get($url);

        $errno = $curl->get_errno();

        $info = $curl->get_info();

        $httpcode = (int)(
            $info['http_code'] ?? 0
        );

        $this->debug_response(
            'GET',
            $url,
            $response,
            $httpcode,
            $errno,
            $curl->error
        );

        return $this->process_response(
            $curl,
            $response,
            $url
        );
    }

    /**
     * Perform POST request.
     *
     * @param string $path
     * @param array $payload
     * @return array
     * @throws \moodle_exception
     */
    public function post(
        string $path,
        array $payload = []
    ): array {

        $url = $this->build_url($path);

        $this->debug_request(
            'POST',
            $url,
            $payload
        );

        $curl = new \curl();

        $curl->setHeader([
            'Accept: application/json',
            'Content-Type: application/json',
        ]);

        if (!empty($payload)) {

            $jsonpayload = json_encode(
                $payload,
                JSON_UNESCAPED_UNICODE
                | JSON_UNESCAPED_SLASHES
            );

            if ($jsonpayload === false) {

                debugging(
                    'TURI request JSON encoding failed. '
                    . 'JSON error=' . json_last_error_msg(),
                    DEBUG_DEVELOPER
                );

                throw new \moodle_exception(
                    'turi_invalid_request',
                    'local_turi_rag'
                );
            }

            $response = $curl->post(
                $url,
                $jsonpayload,
                [
                    'CURLOPT_POSTFIELDS' => $jsonpayload,
                ]
            );

        } else {

            $response = $curl->post(
                $url,
                ''
            );
        }

        $errno = $curl->get_errno();

        $info = $curl->get_info();

        $httpcode = (int)(
            $info['http_code'] ?? 0
        );

        $this->debug_response(
            'POST',
            $url,
            $response,
            $httpcode,
            $errno,
            $curl->error
        );

        return $this->process_response(
            $curl,
            $response,
            $url
        );
    }

    /**
     * Perform multipart/form-data POST.
     *
     * @param string $path
     * @param array $files
     * @return array
     * @throws \moodle_exception
     */
    public function post_multipart(
        string $path,
        array $files
    ): array {

        if (empty($files)) {

            throw new \moodle_exception(
                'turi_file_upload_error',
                'local_turi_rag'
            );
        }

        $url = $this->build_url($path);

        $tmpfiles = [];
        $curlhandle = null;

        try {

            $parts = [];

            foreach ($files as $file) {

                if (!$file instanceof \stored_file) {
                    continue;
                }

                $filename = $file->get_filename();

                $mimetype = $file->get_mimetype();

                $content = $file->get_content();

                if ($content === false) {

                    throw new \moodle_exception(
                        'turi_file_read_error',
                        'local_turi_rag'
                    );
                }

                $tmpfile = tempnam(
                    sys_get_temp_dir(),
                    'turi_'
                );

                if ($tmpfile === false) {

                    throw new \moodle_exception(
                        'turi_file_upload_error',
                        'local_turi_rag'
                    );
                }

                $tmpfiles[] = $tmpfile;

                if (
                    file_put_contents(
                        $tmpfile,
                        $content
                    ) === false
                ) {

                    throw new \moodle_exception(
                        'turi_file_upload_error',
                        'local_turi_rag'
                    );
                }

                $parts[] = [
                    'path' => $tmpfile,
                    'filename' => $filename,
                    'mimetype' => $mimetype,
                    'size' => strlen($content),
                ];
            }

            if (empty($parts)) {

                throw new \moodle_exception(
                    'turi_file_upload_error',
                    'local_turi_rag'
                );
            }

            /*
             * Debug file information.
             *
             * We do NOT output file contents.
             */
            $this->debug_request(
                'POST MULTIPART',
                $url,
                [
                    'files' => array_map(
                        static function ($part) {
                            return [
                                'filename' => $part['filename'],
                                'mimetype' => $part['mimetype'],
                                'size_bytes' => $part['size'],
                            ];
                        },
                        $parts
                    ),
                ]
            );

            $boundary =
                '----TURI'
                . bin2hex(
                    random_bytes(16)
                );

            $body = '';

            foreach ($parts as $part) {

                $content = file_get_contents(
                    $part['path']
                );

                if ($content === false) {

                    throw new \moodle_exception(
                        'turi_file_read_error',
                        'local_turi_rag'
                    );
                }

                $filename = $this->escape_multipart_filename(
                    $part['filename']
                );

                $body .= '--'
                    . $boundary
                    . "\r\n";

                $body .=
                    'Content-Disposition: form-data; '
                    . 'name="files"; '
                    . 'filename="'
                    . $filename
                    . '"'
                    . "\r\n";

                $body .=
                    'Content-Type: '
                    . $part['mimetype']
                    . "\r\n";

                $body .= "\r\n";

                $body .= $content;

                $body .= "\r\n";
            }

            $body .=
                '--'
                . $boundary
                . "--\r\n";

            /*
             * Do NOT debug the complete body.
             *
             * It may contain binary files.
             */
            debugging(
                'TURI multipart body created. '
                . 'URL=' . $url
                . ' boundary=' . $boundary
                . ' body_size=' . strlen($body),
                DEBUG_DEVELOPER
            );

            $curlhandle = curl_init();

            if ($curlhandle === false) {

                throw new \moodle_exception(
                    'turi_connection_error',
                    'local_turi_rag'
                );
            }

            curl_setopt_array(
                $curlhandle,
                [
                    CURLOPT_URL => $url,

                    CURLOPT_POST => true,

                    CURLOPT_POSTFIELDS => $body,

                    CURLOPT_HTTPHEADER => [
                        'Accept: application/json',
                        'Content-Type: multipart/form-data; boundary='
                            . $boundary,
                        'Content-Length: '
                            . strlen($body),
                    ],

                    CURLOPT_RETURNTRANSFER => true,

                    CURLOPT_CONNECTTIMEOUT => 10,

                    CURLOPT_TIMEOUT => 300,
                ]
            );

            $response = curl_exec(
                $curlhandle
            );

            $errno = curl_errno(
                $curlhandle
            );

            $error = curl_error(
                $curlhandle
            );

            $httpcode = (int)curl_getinfo(
                $curlhandle,
                CURLINFO_HTTP_CODE
            );

            $this->debug_response(
                'POST MULTIPART',
                $url,
                $response,
                $httpcode,
                $errno,
                $error
            );

            if ($errno) {

                throw new \moodle_exception(
                    'turi_connection_error',
                    'local_turi_rag'
                );
            }

            if (
                $httpcode < 200
                || $httpcode >= 300
            ) {

                throw new \moodle_exception(
                    'turi_http_error',
                    'local_turi_rag'
                );
            }

            if (
                $response === ''
                || $response === false
            ) {

                throw new \moodle_exception(
                    'turi_invalid_response',
                    'local_turi_rag'
                );
            }

            return $this->decode_json_response(
                $response
            );

        } finally {

            if ($curlhandle !== null) {
                curl_close($curlhandle);
            }

            foreach ($tmpfiles as $tmpfile) {

                @unlink($tmpfile);
            }
        }
    }

    /**
     * Process response from Moodle curl.
     *
     * @param \curl $curl
     * @param string|false $response
     * @param string $url
     * @return array
     * @throws \moodle_exception
     */
    private function process_response(
        \curl $curl,
        $response,
        string $url
    ): array {

        $errno = $curl->get_errno();

        if ($errno) {

            debugging(
                'TURI connection error. '
                . 'URL=' . $url
                . ' errno=' . $errno
                . ' error=' . $curl->error,
                DEBUG_DEVELOPER
            );

            throw new \moodle_exception(
                'turi_connection_error',
                'local_turi_rag'
            );
        }

        $info = $curl->get_info();

        $httpcode = (int)(
            $info['http_code'] ?? 0
        );

        if (
            $httpcode < 200
            || $httpcode >= 300
        ) {

            debugging(
                'TURI HTTP error. '
                . 'URL=' . $url
                . ' HTTP=' . $httpcode
                . ' Response=' . (string)$response,
                DEBUG_DEVELOPER
            );

            throw new \moodle_exception(
                'turi_http_error',
                'local_turi_rag'
            );
        }

        if (
            $response === ''
            || $response === null
            || $response === false
        ) {

            debugging(
                'TURI returned empty response. '
                . 'URL=' . $url,
                DEBUG_DEVELOPER
            );

            throw new \moodle_exception(
                'turi_invalid_response',
                'local_turi_rag'
            );
        }

        return $this->decode_json_response(
            $response
        );
    }

    /**
     * Decode JSON response.
     *
     * @param string $response
     * @return array
     * @throws \moodle_exception
     */
    private function decode_json_response(
        string $response
    ): array {

        $data = json_decode(
            $response,
            true
        );

        if (
            json_last_error()
            !== JSON_ERROR_NONE
        ) {

            debugging(
                'TURI returned invalid JSON. '
                . 'JSON error='
                . json_last_error_msg()
                . ' Response='
                . $response,
                DEBUG_DEVELOPER
            );

            throw new \moodle_exception(
                'turi_invalid_response',
                'local_turi_rag'
            );
        }

        if (!is_array($data)) {

            debugging(
                'TURI returned unexpected JSON type. '
                . 'Response='
                . $response,
                DEBUG_DEVELOPER
            );

            throw new \moodle_exception(
                'turi_invalid_response',
                'local_turi_rag'
            );
        }

        return $data;
    }

    /**
     * Escape multipart filename.
     *
     * @param string $filename
     * @return string
     */
    private function escape_multipart_filename(
        string $filename
    ): string {

        return addcslashes(
            $filename,
            "\"\\\r\n"
        );
    }
}
