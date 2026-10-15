<?php
/**
 * Bagicha 2.0 - Authenticated Profile Endpoint
 */

require_once __DIR__ . '/../helpers/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    jsonResponse('error', 'Method not allowed', null, 405);
}

$user = requireAuth();
jsonResponse('success', 'User profile retrieved', ['user' => $user]);
