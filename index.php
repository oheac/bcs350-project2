<?php
require_once 'config.php';

// Get the requested URI
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove the base path if needed
$base_path = '/';
if (strpos($uri, $base_path) === 0) {
    $uri = substr($uri, strlen($base_path));
}

// Parse the URI into segments
$segments = explode('/', trim($uri, '/'));
$route = !empty($segments[0]) ? $segments[0] : 'home';

// Route handling
switch ($route) {
    // Auth routes
    case 'signup':
        require 'routes/auth.php';
        handleSignup();
        break;

    case 'signin':
        require 'routes/auth.php';
        handleSignin();
        break;

    case 'signout':
        require 'routes/auth.php';
        handleSignout();
        break;

    // Main app routes
    case '':
    case 'home':
        require 'routes/main.php';
        handleHome($pdo);
        break;

    case 'api':
        // API endpoints
        $action = $segments[1] ?? '';

        switch ($action) {
            case 'generate-quiz':
                require 'routes/main.php';
                handleGenerateQuiz($pdo);
                break;

            case 'submit-quiz':
                require 'routes/main.php';
                handleSubmitQuiz($pdo);
                break;

            case 'quiz-history':
                require 'routes/main.php';
                handleQuizHistory($pdo);
                break;

            case 'quiz':
                $quizId = $segments[2] ?? null;
                require 'routes/main.php';
                handleGetQuiz($pdo, $quizId);
                break;

            case 'leaderboard':
                require 'routes/main.php';
                handleLeaderboard($pdo);
                break;

            default:
                http_response_code(404);
                echo json_encode(['error' => 'API endpoint not found']);
                break;
        }
        break;

    default:
        http_response_code(404);
        echo "Page not found";
        break;
}
?>
