<?php

namespace local_turi_rag\local;

defined('MOODLE_INTERNAL') || die();

class file_helper {
    private static $allowed_extensions = null;

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

    private static function get_allowed_extensions(): array {
        if (self::$allowed_extensions === null) {
            $client = new turi_client();

            self::$allowed_extensions = $client->get_allowed_formats();
        }

        return self::$allowed_extensions;
    }
}
