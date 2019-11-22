<?php

// Configure file paths.
$settings['file_public_path'] = 'files/';
$settings['file_private_path'] = $platformsh->appDir . '/private/';
$config['system.file']['path']['temporary'] = $platformsh->appDir . '/tmp/';
$settings['php_storage']['default']['directory'] = $settings['file_private_path'];
$settings['php_storage']['twig']['directory'] = $settings['file_private_path'];
