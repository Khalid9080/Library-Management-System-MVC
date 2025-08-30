<?php
$DB_HOST = getenv('DB_HOST') ?: '127.0.0.1';
$DB_NAME = getenv('DB_NAME') ?: 'lms_database';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: '';

function db(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        $DB_HOST = getenv('DB_HOST') ?: '127.0.0.1';
        $DB_NAME = getenv('DB_NAME') ?: 'lms_database';
        $DB_USER = getenv('DB_USER') ?: 'root';
        $DB_PASS = getenv('DB_PASS') ?: '';

        $dsn = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4";

        try {
            $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            exit('Database connection failed: ' . $e->getMessage());
        }
    }

    return $pdo;
}
