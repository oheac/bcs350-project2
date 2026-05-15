<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/Quiz.php';

function handleHome($pdo) {
    requireAuth();
    $username = $_SESSION['username'] ?? 'User';
    include __DIR__ . '/../views/main.php';
}

function handleGenerateQuiz($pdo) {
    requireAuth();

    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        return;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $topic = trim($input['topic'] ?? '');
    $numQuestions = max(5, min(50, (int)($input['numQuestions'] ?? 20)));

    if (!$topic) {
        http_response_code(400);
        echo json_encode(['error' => 'Topic is required']);
        return;
    }

    try {
        // Load questions from JSON file
        $questionsFile = __DIR__ . '/../questions.json';
        if (!file_exists($questionsFile)) {
            http_response_code(500);
            echo json_encode(['error' => 'Questions file not found']);
            return;
        }

        $allQuestions = json_decode(file_get_contents($questionsFile), true);

        if (!is_array($allQuestions) || empty($allQuestions)) {
            http_response_code(500);
            echo json_encode(['error' => 'Invalid questions format']);
            return;
        }

        // Filter questions by topic (case-insensitive search in question text)
        $topicLower = strtolower($topic);
        $filteredQuestions = array_filter($allQuestions, function ($q) use ($topicLower) {
            return stripos($q['question'], $topicLower) !== false;
        });

        // If no questions match the topic, use all questions
        if (empty($filteredQuestions)) {
            $filteredQuestions = $allQuestions;
        }

        // Shuffle and select the requested number of questions
        shuffle($filteredQuestions);
        $selectedQuestions = array_slice(array_values($filteredQuestions), 0, $numQuestions);

        error_log("answers loaded: " . print_r($selectedQuestions, true));

        // Convert from JSON format to quiz format
        $questions = array_map(function ($q) {
            return [
                'question' => $q['question'],
                'choices' => [$q['A'], $q['B'], $q['C'], $q['D']],
                'answer' => array_search($q['answer'], ['A', 'B', 'C', 'D'], true),
                'explanation' => ''
            ];
        }, $selectedQuestions);

        error_log("questions loaded with answers: " . print_r($questions, true));

        // Store quiz in session (answers hidden from client)
        $_SESSION['currentQuiz'] = [
            'topic' => $topic,
            'questions' => array_map(function ($q) {
                return [
                    'question' => $q['question'],
                    'choices' => $q['choices'],
                    'explanation' => $q['explanation']
                ];
            }, $questions),
            'answers' => array_map(function ($q) {
                return $q['answer'];
            }, $questions),
            'userAnswers' => [],
            'completed' => false
        ];

        error_log("Quiz stored in session: " . print_r($_SESSION['currentQuiz'], true));

        // Send only questions and choices to client (no answers)
        $quizForClient = array_map(function ($q, $idx) {
            return [
                'id' => $idx,
                'question' => $q['question'],
                'choices' => $q['choices']
            ];
        }, $_SESSION['currentQuiz']['questions'], array_keys($_SESSION['currentQuiz']['questions']));

        echo json_encode([
            'success' => true,
            'quiz' => $quizForClient,
            'topic' => $topic
        ]);
    } catch (Exception $e) {
        error_log('Error generating quiz: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Error generating quiz: ' . $e->getMessage()]);
    }
}

function handleSubmitQuiz($pdo) {
    requireAuth();

    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        return;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $answers = $input['answers'] ?? [];

    if (!isset($_SESSION['currentQuiz'])) {
        http_response_code(400);
        echo json_encode(['error' => 'No active quiz']);
        return;
    }

    if (!is_array($answers) || count($answers) !== count($_SESSION['currentQuiz']['answers'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid answers format']);
        return;
    }

    try {
        // Calculate score
        $correctCount = 0;
        $results = [];

        foreach ($answers as $idx => $userAnswer) {
            $isCorrect = $userAnswer === $_SESSION['currentQuiz']['answers'][$idx];
            if ($isCorrect) $correctCount++;

            $results[] = [
                'id' => $idx,
                'question' => $_SESSION['currentQuiz']['questions'][$idx]['question'],
                'choices' => $_SESSION['currentQuiz']['questions'][$idx]['choices'],
                'userAnswer' => $userAnswer,
                'correctAnswer' => $_SESSION['currentQuiz']['answers'][$idx],
                'explanation' => $_SESSION['currentQuiz']['questions'][$idx]['explanation'],
                'isCorrect' => $isCorrect
            ];
        }

        $score = round(($correctCount / count($answers)) * 100);
        $grade = getGrade($score);

        // Save quiz to database
        $quizModel = new Quiz($pdo);
        $quizId = $quizModel->create(
            $_SESSION['userId'],
            $_SESSION['currentQuiz']['topic'],
            $score,
            $grade,
            $correctCount,
            count($answers),
            $results
        );

        // Store results in session
        $_SESSION['currentQuiz']['completed'] = true;
        $_SESSION['quizResults'] = [
            'topic' => $_SESSION['currentQuiz']['topic'],
            'score' => $score,
            'correctCount' => $correctCount,
            'totalCount' => count($answers),
            'grade' => $grade,
            'results' => $results,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        echo json_encode([
            'success' => true,
            'results' => [
                'score' => $score,
                'correctCount' => $correctCount,
                'totalCount' => count($answers),
                'grade' => $grade,
                'review' => $results
            ]
        ]);
    } catch (Exception $e) {
        error_log('Error submitting quiz: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Error submitting quiz']);
    }
}

function handleQuizHistory($pdo) {
    requireAuth();

    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        return;
    }

    try {
        $quizModel = new Quiz($pdo);
        $quizzes = $quizModel->getByUserId($_SESSION['userId']);

        echo json_encode([
            'success' => true,
            'quizzes' => $quizzes
        ]);
    } catch (Exception $e) {
        error_log('Error fetching quiz history: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Error fetching quiz history']);
    }
}

function handleGetQuiz($pdo, $quizId) {
    requireAuth();

    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        return;
    }

    if (!$quizId) {
        http_response_code(400);
        echo json_encode(['error' => 'Quiz ID required']);
        return;
    }

    try {
        $quizModel = new Quiz($pdo);
        $quiz = $quizModel->getById($quizId);

        if (!$quiz) {
            http_response_code(404);
            echo json_encode(['error' => 'Quiz not found']);
            return;
        }

        // Verify quiz belongs to user
        if ($quiz['user_id'] != $_SESSION['userId']) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        echo json_encode([
            'success' => true,
            'quiz' => $quiz
        ]);
    } catch (Exception $e) {
        error_log('Error fetching quiz: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Error fetching quiz']);
    }
}

function handleLeaderboard($pdo) {
    requireAuth();

    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        return;
    }

    try {
        // Get current user's ID
        $currentUserId = $_SESSION['userId'];
        
        // 1. Query to get top 10 users with their statistics
        $query = "
            SELECT 
                u.id,
                u.username,
                COUNT(q.id) as quiz_count,
                COALESCE(ROUND(AVG(q.score), 2), 0) as average_score,
                COALESCE(MAX(q.score), 0) as best_score,
                COALESCE(MIN(q.score), 0) as worst_score,
                COALESCE(SUM(q.correct_count), 0) as total_correct,
                COALESCE(SUM(q.total_count), 0) as total_questions
            FROM users u
            LEFT JOIN quizzes q ON u.id = q.user_id
            GROUP BY u.id, u.username
            HAVING quiz_count > 0
            ORDER BY average_score DESC, quiz_count DESC
            LIMIT 10
        ";
        
        $stmt = $pdo->query($query);
        $topPlayers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // 2. Query to calculate the specific user's stats and actual leaderboard ranking
        $userStatsQuery = "
            SELECT * FROM (
                SELECT 
                    u.id,
                    u.username,
                    COUNT(q.id) as quiz_count,
                    COALESCE(ROUND(AVG(q.score), 2), 0) as average_score,
                    COALESCE(MAX(q.score), 0) as best_score,
                    COALESCE(MIN(q.score), 0) as worst_score,
                    COALESCE(SUM(q.correct_count), 0) as total_correct,
                    COALESCE(SUM(q.total_count), 0) as total_questions,
                    -- Using native MySQL 8.0 window function to compute positions safely
                    DENSE_RANK() OVER (ORDER BY AVG(q.score) DESC, COUNT(q.id) DESC) as `rank`
                FROM users u
                LEFT JOIN quizzes q ON u.id = q.user_id
                GROUP BY u.id, u.username
                HAVING quiz_count > 0
            ) as complete_leaderboard
            WHERE id = :userId
        ";
        
        $stmt = $pdo->prepare($userStatsQuery);
        $stmt->execute([':userId' => $currentUserId]);
        $userStats = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Fallback structure if the user has not taken any quizzes yet
        if (!$userStats) {
            $userStats = [
                'id' => $currentUserId,
                'quiz_count' => 0,
                'average_score' => 0,
                'rank' => 'Unranked'
            ];
        }
        
        echo json_encode([
            'success' => true,
            'topPlayers' => $topPlayers,
            'userRank' => $userStats
        ]);

    } catch (Exception $e) {
        error_log('Error fetching leaderboard: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Error fetching leaderboard: ' . $e->getMessage()]);
    }
}

function getGrade($score) {
    if ($score >= 90) return 'A';
    if ($score >= 80) return 'B';
    if ($score >= 70) return 'C';
    if ($score >= 60) return 'D';
    return 'F';
}
?>
