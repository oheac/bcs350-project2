<?php

class Quiz {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Create a new quiz record
     */
    public function create($userId, $topic, $score, $grade, $correctCount, $totalCount, $questions) {
        try {
            $questionsJson = json_encode($questions);

            $stmt = $this->pdo->prepare("
                INSERT INTO quizzes (user_id, topic, score, grade, correct_count, total_count, questions)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $userId,
                $topic,
                $score,
                $grade,
                $correctCount,
                $totalCount,
                $questionsJson
            ]);

            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error creating quiz: " . $e->getMessage());
            throw new Exception("Error creating quiz");
        }
    }

    /**
     * Get quiz by ID
     */
    public function getById($id) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM quizzes WHERE id = ?");
            $stmt->execute([$id]);
            $quiz = $stmt->fetch();

            if ($quiz) {
                $quiz['questions'] = json_decode($quiz['questions'], true);
            }

            return $quiz;
        } catch (PDOException $e) {
            error_log("Error fetching quiz: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all quizzes for a user, ordered by recent first
     */
    public function getByUserId($userId) {
        try {
            $stmt = $this->pdo->prepare("
                SELECT id, topic, score, grade, correct_count, total_count, completed_at
                FROM quizzes
                WHERE user_id = ?
                ORDER BY completed_at DESC
            ");

            $stmt->execute([$userId]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error fetching user quizzes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Check if quiz belongs to user
     */
    public function belongsToUser($quizId, $userId) {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM quizzes WHERE id = ? AND user_id = ?");
            $stmt->execute([$quizId, $userId]);
            $result = $stmt->fetch();
            return $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("Error checking quiz ownership: " . $e->getMessage());
            return false;
        }
    }
}
?>
