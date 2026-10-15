<?php
/**
 * Bagicha - Database Connection (PDO Singleton)
 */

if (!defined('BAGICHA_INIT')) {
    define('BAGICHA_INIT', true);
}

require_once __DIR__ . '/constants.php';

class Database {
    private static ?PDO $instance = null;

    private function __construct() {}
    private function __clone() {}

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            try {
                $port = defined('DB_PORT') ? DB_PORT : '3306';
                $dsn = "mysql:host=" . DB_HOST . ";port=" . $port . ";dbname=" . DB_NAME . ";charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
                if (defined('DB_SSL_CA') && !empty(DB_SSL_CA) && file_exists(DB_SSL_CA)) {
                    $options[PDO::MYSQL_ATTR_SSL_CA] = DB_SSL_CA;
                }
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                // Log error internally without exposing SQL details
                error_log("Database Connection Error: " . $e->getMessage());
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Database connection failed. Please check server configuration.'
                ]);
                exit();
            }
        }
        return self::$instance;
    }
}
