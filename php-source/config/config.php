<?php
/**
 * Eagle Reports Generator - App Configuration
 */

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Environment Constants
define('APP_NAME', 'Eagle Reports Generator');
define('BASE_URL', 'http://localhost/eagle-reports'); // Update this with your live URL in production
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('PDF_DIR', __DIR__ . '/../pdf/');

// Error Reporting (Turn off for production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Security Headers
header("X-Frame-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");

// Helper to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Generate CSRF Token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Validate CSRF Token
function validateCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
