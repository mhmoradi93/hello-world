<?php

declare(strict_types=1);

/**
 * Plugin Name: WP REST Resource Kit
 * Description: A compact example of a structured WordPress plugin with custom REST API endpoints.
 * Version: 1.0.0
 * Author: Mohammad Hossein Moradi
 * Requires at least: 6.4
 * Requires PHP: 8.1
 * License: MIT
 */

if (! defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/src/Repository/ResourceRepository.php';
require_once __DIR__ . '/src/Rest/ResourceController.php';
require_once __DIR__ . '/src/Plugin.php';

add_action(
    'plugins_loaded',
    static function (): void {
        (new \MHM\ResourceApi\Plugin())->boot();
    }
);
