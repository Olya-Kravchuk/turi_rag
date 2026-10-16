<?php

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $secretsalt = get_config('local_turi_rag', 'secret_salt');
    if (empty($secretsalt)) {
        $secretsalt = bin2hex(random_bytes(32));

        set_config(
            'secret_salt',
            $secretsalt,
            'local_turi_rag'
        );
    }

    $settings = new admin_settingpage(
        'local_turi_rag',
        get_string('pluginname', 'local_turi_rag')
    );

    // Prompt service.
    $settings->add(
        new admin_setting_heading(
            'local_turi_rag/prompt_heading',
            get_string('promptsettings', 'local_turi_rag'),
            get_string('promptsettings_desc', 'local_turi_rag')
        )
    );

    $settings->add(
        new admin_setting_configtext(
            'local_turi_rag/prompt_host',
            get_string('prompt_host', 'local_turi_rag'),
            get_string('prompt_host_desc', 'local_turi_rag'),
            '192.168.137.140',
            PARAM_TEXT
        )
    );

    $settings->add(
        new admin_setting_configtext(
            'local_turi_rag/prompt_port',
            get_string('prompt_port', 'local_turi_rag'),
            get_string('prompt_port_desc', 'local_turi_rag'),
            '8000',
            PARAM_INT
        )
    );

    // Embedding service.
    $settings->add(
        new admin_setting_heading(
            'local_turi_rag/embedding_heading',
            get_string('embeddingsettings', 'local_turi_rag'),
            get_string('embeddingsettings_desc', 'local_turi_rag')
        )
    );

    $settings->add(
        new admin_setting_configtext(
            'local_turi_rag/embedding_host',
            get_string('embedding_host', 'local_turi_rag'),
            get_string('embedding_host_desc', 'local_turi_rag'),
            '192.168.137.140',
            PARAM_TEXT
        )
    );

    $settings->add(
        new admin_setting_configtext(
            'local_turi_rag/embedding_port',
            get_string('embedding_port', 'local_turi_rag'),
            get_string('embedding_port_desc', 'local_turi_rag'),
            '8000',
            PARAM_INT
        )
    );

    $ADMIN->add('localplugins', $settings);
}