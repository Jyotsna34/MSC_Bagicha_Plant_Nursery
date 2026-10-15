<?php

/**
 * Bagicha 2.0 - About Us Page
 * Redirects to the unified single-page Home & About Us view.
 */
if (!defined('BAGICHA_INIT')) {
    define('BAGICHA_INIT', true);
}
require_once __DIR__ . '/../config/constants.php';

header("Location: " . BASE_URL . "/index.php#about", true, 301);
exit;
