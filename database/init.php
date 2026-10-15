<?php
/**
 * Bagicha - Database Auto-Installer Script
 */

require_once __DIR__ . '/../config/constants.php';

header('Content-Type: application/json');

try {
    // 1. Connect without DB selected to create DB if needed
    $port = defined('DB_PORT') ? DB_PORT : '3306';
    $dsn = "mysql:host=" . DB_HOST . ";port=" . $port . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ];
    if (defined('DB_SSL_CA') && !empty(DB_SSL_CA) && file_exists(DB_SSL_CA)) {
        $options[PDO::MYSQL_ATTR_SSL_CA] = DB_SSL_CA;
    }
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

    // 2. Read Schema SQL
    $schemaSql = file_get_contents(__DIR__ . '/schema.sql');
    if (!$schemaSql) {
        throw new Exception("schema.sql not found");
    }

    $pdo->exec($schemaSql);

    // 3. Read Seed SQL
    $seedSql = file_get_contents(__DIR__ . '/seed.sql');
    if (!$seedSql) {
        throw new Exception("seed.sql not found");
    }

    $pdo->exec("USE `" . DB_NAME . "`");
    $statements = array_filter(array_map('trim', explode(';', $seedSql)));
    foreach ($statements as $stmtSql) {
        if (!empty($stmtSql) && !str_starts_with($stmtSql, '--')) {
            $pdo->exec($stmtSql);
        }
    }

    // 4. Ensure all user account password hashes match local server PHP password_hash algorithm
    $pdo->exec("USE `" . DB_NAME . "`");
    $defaultPassHash = password_hash('Password123', PASSWORD_BCRYPT);
    $updateStmt = $pdo->prepare("UPDATE `users` SET `password` = :pass");
    $updateStmt->execute([':pass' => $defaultPassHash]);

    // 5. Ensure all products are set to active & have valid SKUs
    $pdo->exec("UPDATE `products` SET `is_active` = 1 WHERE `is_active` IS NULL OR `is_active` = 0");
    $pdo->exec("UPDATE `products` SET `sku` = CONCAT('PLT-', UPPER(SUBSTRING(REPLACE(name, ' ', ''), 1, 3)), '-0', id) WHERE `sku` IS NULL OR `sku` = ''");

    echo json_encode([
        'status' => 'success',
        'message' => 'Bagicha database initialized and seed account passwords set to secret successfully!'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
