<?php

namespace local_turi_rag\local;

defined('MOODLE_INTERNAL') || die();

require_once($GLOBALS['CFG']->libdir . '/filelib.php');

class turi_client {
    /**
     *
     * @return string
     * @throws \moodle_exception
     */
    private static function get_base_url(): string {
        $host = get_config('local_turi_rag', 'embedding_host');
        $port = get_config('local_turi_rag', 'embedding_port');

        if (empty($host) || empty($port)) {
            throw new \moodle_exception(
                'embedding_config_missing',
                'local_turi_rag'
            );
        }

        return 'http://' . $host . ':' . $port;
    }

    /**
     * Get RAG status for a course.
     *
     * @param string $courseid
     * @return array
     * @throws \moodle_exception
     */
    // public static function get_rag_status(string $courseid): array {

    //     $url = self::get_base_url() . '/api/embedding/getStatus?id=' . rawurlencode($courseid);

    //     debugging(
    //         'TURI DEBUG: requesting URL: ' . $url,
    //         DEBUG_DEVELOPER
    //     );
    //     $curl = new \curl();

    //     $response = $curl->get($url);


    //     $errno = $curl->get_errno();

    //     if ($errno) {
    //         debugging(
    //             'TURI connection error. '
    //             . 'cURL errno: ' . $errno
    //             . '. cURL error: ' . $curl->error
    //             . '. URL: ' . $url,
    //             DEBUG_DEVELOPER
    //         );

    //         throw new \moodle_exception(
    //             'turi_connection_error',
    //             'local_turi_rag'
    //         );
    //     }


    //     $info = $curl->get_info();

    //     $httpcode = $info['http_code'] ?? 0;

    //     debugging(
    //         'TURI DEBUG: HTTP status: ' . $httpcode
    //         . '. URL: ' . $url
    //         . '. Response: ' . $response,
    //         DEBUG_DEVELOPER
    //     );
    //     if ($httpcode < 200 || $httpcode >= 300) {
    //         debugging(
    //             'TURI HTTP error. '
    //             . 'HTTP status: ' . $httpcode
    //             . '. URL: ' . $url
    //             . '. Response: ' . $response,
    //             DEBUG_DEVELOPER
    //         );

    //         throw new \moodle_exception(
    //             'turi_http_error',
    //             'local_turi_rag'
    //         );
    //     }

    //     if ($response === '' || $response === null) {
    //         debugging(
    //             'TURI returned an empty response. URL: ' . $url,
    //             DEBUG_DEVELOPER
    //         );

    //         throw new \moodle_exception(
    //             'turi_invalid_response',
    //             'local_turi_rag'
    //         );
    //     }

    //     $data = json_decode($response, true);

    //     if (json_last_error() !== JSON_ERROR_NONE) {
    //         debugging(
    //             'TURI returned invalid JSON. '
    //             . 'JSON error: ' . json_last_error_msg()
    //             . '. Response: ' . $response,
    //             DEBUG_DEVELOPER
    //         );

    //         throw new \moodle_exception(
    //             'turi_invalid_response',
    //             'local_turi_rag'
    //         );
    //     }

    //     if (
    //         !isset($data['course_status']) ||
    //         !is_string($data['course_status'])
    //     ) {
    //         debugging(
    //             'TURI returned unexpected JSON structure. '
    //             . 'Missing or invalid course_status. '
    //             . 'Response: ' . $response,
    //             DEBUG_DEVELOPER
    //         );

    //         throw new \moodle_exception(
    //             'turi_invalid_response',
    //             'local_turi_rag'
    //         );
    //     }

    //     if (
    //         isset($data['documents']) &&
    //         !is_array($data['documents'])
    //     ) {
    //         debugging(
    //             'TURI returned invalid documents structure. '
    //             . 'Response: ' . $response,
    //             DEBUG_DEVELOPER
    //         );

    //         throw new \moodle_exception(
    //             'turi_invalid_response',
    //             'local_turi_rag'
    //         );
    //     }

    //     if (!isset($data['documents'])) {
    //         $data['documents'] = [];
    //     }

    //     return $data;
    // }

    // public static function get_rag_status(string $courseid): array {

    //     $url = self::get_base_url() . '/api/embedding/getStatus?id=' . rawurlencode($courseid);

    //     $curl = new \curl();

    //     $response = $curl->get($url);

    //     echo '<pre>';
    //     var_dump($response);
    //     echo '</pre>';
    //     exit;
    // }

    public static function get_rag_status(string $courseid): array {

        $url = self::get_base_url()
            . '/api/embedding/getStatus?id='
            . rawurlencode($courseid);

        $curl = new \curl();

        $response = $curl->get($url);

        $errno = $curl->get_errno();

        if ($errno) {
            throw new \moodle_exception(
                'turi_connection_error',
                'local_turi_rag'
            );
        }

        $info = $curl->get_info();
        $httpcode = $info['http_code'] ?? 0;

        if ($httpcode < 200 || $httpcode >= 300) {
            throw new \moodle_exception(
                'turi_http_error',
                'local_turi_rag'
            );
        }

        if ($response === '' || $response === null) {
            throw new \moodle_exception(
                'turi_invalid_response',
                'local_turi_rag'
            );
        }

        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \moodle_exception(
                'turi_invalid_response',
                'local_turi_rag'
            );
        }

        return $data;
    }

    /**
     * Get allowed file formats from TURI.
     *
     * @return array
     * @throws \moodle_exception
     */
    // public static function get_allowed_formats(): array {
    //     $url = self::get_base_url() . '/api/embedding/allowedFormats';

    //     $curl = new \curl();

    //     $response = $curl->get($url);

    //     debugging(
    //         'TURI allowedFormats response: ' . $response,
    //         DEBUG_DEVELOPER
    //     );


    //     $errno = $curl->get_errno();

    //     if ($errno) {
    //         throw new \moodle_exception(
    //             'turi_connection_error',
    //             'local_turi_rag'
    //         );
    //     }

    //     $info = $curl->get_info();
    //     $httpcode = $info['http_code'] ?? 0;

    //     if ($httpcode < 200 || $httpcode >= 300) {
    //         throw new \moodle_exception(
    //             'turi_http_error',
    //             'local_turi_rag'
    //         );
    //     }

    //     $data = json_decode($response, true);

    //     if (json_last_error() !== JSON_ERROR_NONE) {
    //         throw new \moodle_exception(
    //             'turi_invalid_response',
    //             'local_turi_rag'
    //         );
    //     }

    //     if (
    //         !isset($data['allowedFormats']) ||
    //         !is_array($data['allowedFormats'])
    //     ) {
    //         throw new \moodle_exception(
    //             'turi_invalid_response',
    //             'local_turi_rag'
    //         );
    //     }

    //     return array_map(
    //         static fn($format) => strtolower(ltrim((string)$format, '.')),
    //         $data['allowedFormats']
    //     );
    // }

    public static function get_allowed_formats(): array {
        $url = self::get_base_url() . '/api/embedding/allowedFormats';

        $curl = new \curl();

        $response = $curl->get($url);

        $data = json_decode($response, true);

        return array_map(
            static fn($format) => strtolower(ltrim($format, '.')),
            $data['allowed_suffixes']
        );
    }

    // public static function init_session(array $payload): array {
    //     $url = self::get_base_url() . '/session';
    //     $jsonpayload = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    //     $curl = new \curl();
    //     $curl->setHeader([
    //         'Content-Type: application/json',
    //         'Accept: application/json',
    //     ]);

    //     $response = $curl->post($url, $jsonpayload, ['CURLOPT_POSTFIELDS' => $jsonpayload]);
    //     return json_decode($response, true) ?: [];
    // }

    public static function init_session(array $payload): array {
        $url = self::get_base_url() . '/api/embedding/session';

        $jsonpayload = json_encode(
            $payload,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        $curl = new \curl();

        $curl->setHeader([
            'Content-Type: application/json',
            'Accept: application/json',
        ]);

        $response = $curl->post(
            $url,
            $jsonpayload,
            ['CURLOPT_POSTFIELDS' => $jsonpayload]
        );
        $result = json_decode($response, true) ?: [];
        $info = $curl->get_info();

        // echo '<pre>';

        // echo "===== URL =====\n";
        // var_dump($url);

        // echo "\n===== REQUEST PAYLOAD =====\n";
        // var_dump($payload);

        // echo "\n===== REQUEST JSON =====\n";
        // var_dump($jsonpayload);

        // echo "\n===== HTTP STATUS =====\n";
        // var_dump($info['http_code'] ?? null);

        // echo "\n===== CURL INFO =====\n";
        // var_dump($info);

        // echo "\n===== RESPONSE =====\n";
        // var_dump($response);

        // echo "\n===== CURL ERROR =====\n";
        // var_dump($curl->get_errno());

        // echo "\n===== CURL ERROR MESSAGE =====\n";
        // var_dump($curl->error);

        // echo '</pre>';
        echo '<details open>';
        echo '<summary><strong>Request sent to TURI</strong></summary>';
        echo '<pre>';
        echo htmlspecialchars(
            json_encode(
                $payload,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            )
        );
        echo '</pre>';
        echo '</details>';

        echo '<details open>';
        echo '<summary><strong>Response from TURI</strong></summary>';
        echo '<pre>';
        echo htmlspecialchars(
            json_encode(
                $result,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            )
        );
        echo '</pre>';
        echo '</details>';


        return json_decode($response, true) ?: [];
    }

    public static function upload_files(
        string $sessionid,
        array $files
    ): array {

        $url = self::get_base_url()
            . '/api/embedding/session/'
            . rawurlencode($sessionid)
            . '/upload';

        if (empty($files)) {
            throw new \moodle_exception(
                'turi_file_upload_error',
                'local_turi_rag'
            );
        }

        $tmpfiles = [];
        $debugfiles = [];
        $curlhandle = null;

        try {

            /*
            * Multipart-Daten vorbereiten.
            *
            * WICHTIG:
            * Jeder Eintrag bekommt denselben Key "files".
            *
            * PHP cURL wird daraus:
            *
            * files = file1
            * files = file2
            * files = file3
            */
            $multipart = [];

            foreach ($files as $file) {

                if (!$file instanceof \stored_file) {
                    continue;
                }

                $filename = $file->get_filename();
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

                if (file_put_contents($tmpfile, $content) === false) {
                    throw new \moodle_exception(
                        'turi_file_upload_error',
                        'local_turi_rag'
                    );
                }

                /*
                * CURLFile erzeugen.
                */
                $curlfile = new \CURLFile(
                    $tmpfile,
                    $file->get_mimetype(),
                    $filename
                );

                /*
                * Wichtig:
                *
                * Nicht:
                *
                * $multipart['files'][] = $curlfile;
                *
                * sondern nummerierte Keys mit demselben
                * Multipart-Namen werden später benötigt.
                */
                $multipart[] = [
                    'name' => 'files',
                    'contents' => $curlfile,
                ];

                $debugfiles[] = [
                    'multipart_key' => 'files',
                    'filename' => $filename,
                    'mime_type' => $file->get_mimetype(),
                    'size_bytes' => strlen($content),
                    'moodle_file_id' => $file->get_id(),
                ];
            }

            if (empty($multipart)) {
                throw new \moodle_exception(
                    'turi_file_upload_error',
                    'local_turi_rag'
                );
            }

            /*
            * ==========================================================
            * WICHTIG:
            *
            * PHPs normales CURLOPT_POSTFIELDS kann nicht einfach
            * mehrere identische Feldnamen als Array darstellen.
            *
            * Deshalb bauen wir den multipart Body selbst.
            * ==========================================================
            */

            $boundary = '----TURI' . bin2hex(random_bytes(16));

            $body = '';

            foreach ($multipart as $part) {

                /** @var \CURLFile $curlfile */
                $curlfile = $part['contents'];

                $filename = $curlfile->getPostFilename();
                $mime = $curlfile->getMimeType();
                $path = $curlfile->getFilename();

                $content = file_get_contents($path);

                if ($content === false) {
                    throw new \moodle_exception(
                        'turi_file_read_error',
                        'local_turi_rag'
                    );
                }

                $body .= '--' . $boundary . "\r\n";
                $body .= 'Content-Disposition: form-data; name="files"; filename="'
                    . addcslashes($filename, "\"\\")
                    . '"' . "\r\n";
                $body .= 'Content-Type: ' . $mime . "\r\n";
                $body .= "\r\n";
                $body .= $content;
                $body .= "\r\n";
            }

            $body .= '--' . $boundary . "--\r\n";

            /*
            * ==========================================================
            * DEBUG REQUEST
            * ==========================================================
            */

            echo '<details open>';
            echo '<summary><strong>File Upload Request sent to TURI</strong></summary>';
            echo '<pre>';

            echo "===== URL =====\n";
            echo htmlspecialchars($url);

            echo "\n\n===== SESSION ID =====\n";
            echo htmlspecialchars($sessionid);

            echo "\n\n===== HTTP METHOD =====\n";
            echo "POST";

            echo "\n\n===== CONTENT TYPE =====\n";
            echo htmlspecialchars(
                'multipart/form-data; boundary=' . $boundary
            );

            echo "\n\n===== MULTIPART FILES =====\n";
            echo htmlspecialchars(
                json_encode(
                    $debugfiles,
                    JSON_PRETTY_PRINT
                    | JSON_UNESCAPED_UNICODE
                    | JSON_UNESCAPED_SLASHES
                )
            );

            echo '</pre>';
            echo '</details>';

            /*
            * ==========================================================
            * CURL
            * ==========================================================
            */

            $curlhandle = curl_init();

            curl_setopt_array($curlhandle, [
                CURLOPT_URL => $url,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $body,

                CURLOPT_HTTPHEADER => [
                    'Accept: application/json',
                    'Content-Type: multipart/form-data; boundary=' . $boundary,
                    'Content-Length: ' . strlen($body),
                ],

                CURLOPT_RETURNTRANSFER => true,

                /*
                * Optional, falls Moodle/TURI lokal mit HTTP arbeitet.
                */
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_TIMEOUT => 300,
            ]);

            $response = curl_exec($curlhandle);

            $errno = curl_errno($curlhandle);
            $error = curl_error($curlhandle);
            $httpcode = (int)curl_getinfo(
                $curlhandle,
                CURLINFO_HTTP_CODE
            );

            /*
            * ==========================================================
            * DEBUG RESPONSE
            * ==========================================================
            */

            echo '<details open>';
            echo '<summary><strong>Response from TURI</strong></summary>';
            echo '<pre>';

            echo "===== HTTP STATUS =====\n";
            echo htmlspecialchars((string)$httpcode);

            echo "\n\n===== CURL ERROR =====\n";
            echo htmlspecialchars((string)$errno);

            echo "\n\n===== CURL ERROR MESSAGE =====\n";
            echo htmlspecialchars($error);

            echo "\n\n===== RAW RESPONSE =====\n";
            echo htmlspecialchars((string)$response);

            echo '</pre>';
            echo '</details>';

            /*
            * CURL error.
            */
            if ($errno) {
                debugging(
                    'TURI upload connection error: '
                    . $error
                    . ' URL=' . $url,
                    DEBUG_DEVELOPER
                );

                throw new \moodle_exception(
                    'turi_connection_error',
                    'local_turi_rag'
                );
            }

            /*
            * HTTP error.
            */
            if ($httpcode < 200 || $httpcode >= 300) {
                debugging(
                    'TURI upload HTTP error. '
                    . 'HTTP=' . $httpcode
                    . ' Response=' . $response,
                    DEBUG_DEVELOPER
                );

                throw new \moodle_exception(
                    'turi_http_error',
                    'local_turi_rag'
                );
            }

            /*
            * Empty response.
            */
            if ($response === '' || $response === false) {
                throw new \moodle_exception(
                    'turi_invalid_response',
                    'local_turi_rag'
                );
            }

            /*
            * JSON decode.
            */
            $data = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                debugging(
                    'TURI upload returned invalid JSON: '
                    . json_last_error_msg()
                    . ' Response=' . $response,
                    DEBUG_DEVELOPER
                );

                throw new \moodle_exception(
                    'turi_invalid_response',
                    'local_turi_rag'
                );
            }

            return $data;

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
     * Start processing of an uploaded TURI session.
     *
     * @param string $sessionid
     * @return array
     * @throws \moodle_exception
     */
    public static function complete_session(string $sessionid): array {

        $url = self::get_base_url()
            . '/api/embedding/session/'
            . rawurlencode($sessionid)
            . '/complete';

        $curl = new \curl();

        $curl->setHeader([
            'Accept: application/json',
        ]);

        /*
        * POST ohne Body.
        */
        $response = $curl->post(
            $url,
            []
        );

        $errno = $curl->get_errno();
        $info = $curl->get_info();
        $httpcode = $info['http_code'] ?? 0;

        echo '<details open>';
        echo '<summary><strong>Complete Request sent to TURI</strong></summary>';
        echo '<pre>';

        echo "===== URL =====\n";
        echo htmlspecialchars($url);

        echo "\n\n===== HTTP METHOD =====\n";
        echo "POST";

        echo "\n\n===== HTTP STATUS =====\n";
        echo htmlspecialchars((string)$httpcode);

        echo "\n\n===== CURL ERROR =====\n";
        echo htmlspecialchars((string)$errno);

        echo "\n\n===== CURL ERROR MESSAGE =====\n";
        echo htmlspecialchars((string)$curl->error);

        echo "\n\n===== RAW RESPONSE =====\n";
        echo htmlspecialchars((string)$response);

        echo '</pre>';
        echo '</details>';

        if ($errno) {
            throw new \moodle_exception(
                'turi_connection_error',
                'local_turi_rag'
            );
        }

        if ($httpcode < 200 || $httpcode >= 300) {
            throw new \moodle_exception(
                'turi_http_error',
                'local_turi_rag'
            );
        }

        if ($response === '' || $response === null) {
            throw new \moodle_exception(
                'turi_invalid_response',
                'local_turi_rag'
            );
        }

        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \moodle_exception(
                'turi_invalid_response',
                'local_turi_rag'
            );
        }

        return $data;
    }



    public static function create_profile(array $payload): array {

        $url = self::get_base_url() . '/api/embedding/entry_first';

        $jsonpayload = json_encode($payload);

        if ($jsonpayload === false) {
            throw new \moodle_exception(
                'turi_invalid_response',
                'local_turi_rag'
            );
        }

        debugging(
            'TURI entry_first request: ' . $jsonpayload,
            DEBUG_DEVELOPER
        );

        $curl = new \curl();

        $curl->setHeader([
            'Content-Type: application/json',
            'Accept: application/json',
        ]);

        $response = $curl->get(
            $url,
            $jsonpayload
        );

        $info = $curl->get_info();

        echo '<pre>';

        echo "===== URL =====\n";
        var_dump($url);

        echo "\n===== REQUEST JSON =====\n";
        var_dump($jsonpayload);

        echo "\n===== HTTP STATUS =====\n";
        var_dump($info['http_code'] ?? null);

        echo "\n===== RESPONSE =====\n";
        var_dump($response);

        echo "\n===== CURL ERROR =====\n";
        var_dump($curl->get_errno());

        echo '</pre>';

        exit;

        if ($curl->get_errno()) {
            throw new \moodle_exception(
                'turi_connection_error',
                'local_turi_rag'
            );
        }

        $info = $curl->get_info();
        $httpcode = $info['http_code'] ?? 0;

        debugging(
            'TURI entry_first response HTTP ' . $httpcode . ': ' . $response,
            DEBUG_DEVELOPER
        );

        if ($httpcode < 200 || $httpcode >= 300) {
            throw new \moodle_exception(
                'turi_http_error',
                'local_turi_rag'
            );
        }

        if ($response === '' || $response === null) {
            throw new \moodle_exception(
                'turi_invalid_response',
                'local_turi_rag'
            );
        }

        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \moodle_exception(
                'turi_invalid_response',
                'local_turi_rag'
            );
        }

        return $data;
    }



    public static function delete_profile(string $courseid): bool {

        $url = self::get_base_url()
            . '/api/embedding/entry_delete';

        $url .= '?id=' . rawurlencode($courseid);

        $curl = new \curl();

        $curl->setHeader([
            'Accept: application/json',
        ]);

        $response = $curl->get($url);

        $errno = $curl->get_errno();
        $info = $curl->get_info();
        $httpcode = $info['http_code'] ?? 0;

        echo '<details open>';
        echo '<summary><strong>Delete Response</strong></summary>';
        echo '<pre>';
        print_r([
            'url' => $url,
            'http_method' => 'GET',
            'id' => $courseid,
            'httpcode' => $httpcode,
            'errno' => $errno,
            'error' => $curl->error,
            'raw_response' => $response,
        ]);
        echo '</pre>';
        echo '</details>';

        exit;

        debugging(
            'TURI delete profile: ' . print_r([
                'url' => $url,
                'http_method' => 'GET',
                'id' => $courseid,
                'httpcode' => $httpcode,
                'errno' => $errno,
                'error' => $curl->error,
                'response' => $response,
            ], true),
            DEBUG_DEVELOPER
        );

        if ($errno) {
            throw new \moodle_exception(
                'turi_connection_error',
                'local_turi_rag'
            );
        }

        if ($httpcode < 200 || $httpcode >= 300) {
            throw new \moodle_exception(
                'turi_http_error',
                'local_turi_rag'
            );
        }

        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \moodle_exception(
                'turi_invalid_response',
                'local_turi_rag'
            );
        }

        /*
        * entry_delete повертає:
        *
        * {
        *   "script": "embedding/entry_delete",
        *   "id": "2",
        *   "datum": "2026-10-01",
        *   "zeit": "12:15:36"
        * }
        *
        * Оскільки HTTP 200 означає успішне виконання,
        * перевіряємо також id.
        */
        return isset($data['id'])
            && (string)$data['id'] === (string)$courseid;
    }



    /**
     * Completely delete a RAG profile for a course.
     *
     * @param string $courseid
     * @param string $coursename
     * @return array
     * @throws \moodle_exception
     */
//     public static function delete_profile(string $courseid, string $coursename): bool {
//         $url = self::get_base_url() . '/api/embedding/entry_delete';

//         $payload = [
//             'operation' => 'delete_profile',
//             'course_metadata' => [
//                 'course_id' => (string)$courseid,
//                 'course_name' => $coursename,
//             ],
//         ];

//         $jsonpayload = json_encode($payload);

//         $curl = new \curl();
//         $curl->setHeader([
//             'Content-Type: application/json',
//             'Accept: application/json',
//         ]);

//         $response = $curl->post($url, $jsonpayload, ['CURLOPT_POSTFIELDS' => $jsonpayload]);
//         $errno = $curl->get_errno();

//         if ($errno) {
//             debugging('TURI delete profile connection error: ' . $curl->error . ' (cURL errno: ' . $errno . ')', DEBUG_DEVELOPER);
//             throw new \moodle_exception('turi_connection_error', 'local_turi_rag');
//         }

//         $info = $curl->get_info();
//         $httpcode = $info['http_code'] ?? 0;

//         debugging('TURI Response HTTP ' . $httpcode . ': ' . $response, DEBUG_DEVELOPER);

//         if ($httpcode < 200 || $httpcode >= 300) {
//             debugging('TURI HTTP error on delete profile. Code: ' . $httpcode . ' Response: ' . $response, DEBUG_DEVELOPER);
//             throw new \moodle_exception('turi_http_error', 'local_turi_rag');
//         }

//         $data = json_decode($response, true);
//         if (json_last_error() !== JSON_ERROR_NONE) {
//             debugging('JSON decode error: ' . json_last_error_msg() . ' Raw response: ' . $response, DEBUG_DEVELOPER);
//             throw new \moodle_exception('turi_invalid_response', 'local_turi_rag');
//         }

//         if (isset($data['status']) && $data['status'] === 'delete_accepted') {
//             return true;
//         }

//         return false;
//     }
}