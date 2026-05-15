<?php
// Database configuration
define('DB_HOST', isset($_ENV['DB_HOST']) ? $_ENV['DB_HOST'] : 'db');
define('DB_USER', isset($_ENV['DB_USER']) ? $_ENV['DB_USER'] : 'root');
define('DB_PASS', isset($_ENV['DB_PASS']) ? $_ENV['DB_PASS'] : 'password');
define('DB_NAME', isset($_ENV['DB_NAME']) ? $_ENV['DB_NAME'] : 'quiz_app');


// Session settings
define('SESSION_SECRET', isset($_ENV['SESSION_SECRET']) ? $_ENV['SESSION_SECRET'] : 'your-secret-key');
define('SESSION_TIMEOUT', 1000 * 60 * 60 * 24); // 24 hours

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
} catch (PDOException $e) {
    error_log("Database connection error: " . $e->getMessage());
    die("Database connection failed");
}

// Helper function to check if user is authenticated
function isAuthenticated() {
    return isset($_SESSION['userId']) && !empty($_SESSION['userId']);
}

// Helper function to redirect to signin if not authenticated
function requireAuth() {
    if (!isAuthenticated()) {
        header('Location: /signin.php');
        exit();
    }
}

// Helper function to log out
function logout() {
    session_destroy();
    header('Location: /signin.php');
    exit();
}
?>
