<?php

namespace local_turi_rag\local;

defined('MOODLE_INTERNAL') || die();

/**
 * Helper for working with TURI-supported files.
 */
class file_helper {

    /** @var array|null */
    private static $allowed_extensions = null;

    /**
     * Check whether a filename has a format supported by TURI.
     *
     * @param string $filename
     * @return bool
     * @throws \moodle_exception
     */
    public static function is_supported_format(
        string $filename
    ): bool {

        $extension = strtolower(
            pathinfo($filename, PATHINFO_EXTENSION)
        );

        $allowed_extensions = self::get_allowed_extensions();

        return in_array(
            $extension,
            $allowed_extensions,
            true
        );
    }

    /**
     * Get allowed file extensions.
     *
     * The result is cached for the current PHP request.
     *
     * @return array
     * @throws \moodle_exception
     */
    private static function get_allowed_extensions(): array {

        if (self::$allowed_extensions === null) {
            $client = new turi_client();

            self::$allowed_extensions = $client->get_allowed_formats();
        }

        return self::$allowed_extensions;
    }
}
