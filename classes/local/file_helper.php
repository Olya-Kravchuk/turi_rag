<?php

namespace local_turi_rag\local;

defined('MOODLE_INTERNAL') || die();

class file_helper {

    /** @var array|null */
    private static $allowed_extensions = null;

    /**
     * Check whether the file format is supported by TURI.
     *
     * @param string $filename
     * @return bool
     * @throws \moodle_exception
     */
    public static function is_supported_format(string $filename): bool {

        $extension = strtolower(
            pathinfo($filename, PATHINFO_EXTENSION)
        );

        // $allowed_extensions = turi_client::get_allowed_formats();

        // return in_array($extension, $allowed_extensions, true);
        if (self::$allowed_extensions === null) {
            self::$allowed_extensions = turi_client::get_allowed_formats();
        }

        return in_array(
            $extension,
            self::$allowed_extensions,
            true
        );
    }

}
