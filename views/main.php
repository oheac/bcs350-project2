<?php
require_once __DIR__ . '/../config.php';
requireAuth();

$username = $_SESSION['username'] ?? 'User';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Study Quiz</title>
    <style>
        * {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);
            min-height: 100vh;
        }

        /* Fluent Design Header */
        header {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        header > div {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            width: 100%;
        }

        header h1 {
            color: #0078d4;
            font-size: 20px;
            font-weight: 600;
            letter-spacing: -0.3px;
            margin: 0;
        }

        /* Fluent Tabs */
        .fluent-tabs {
            display: flex;
            gap: 32px;
            border-bottom: 1px solid #e1e1e1;
        }

        .fluent-tab {
            padding: 12px 0;
            color: #424242;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            background: none;
            position: relative;
            transition: color 0.2s ease;
        }

        .fluent-tab:hover {
            color: #0078d4;
        }

        .fluent-tab.active {
            color: #0078d4;
        }

        .fluent-tab.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 2px;
            background: #0078d4;
        }

        /* Fluent Card */
        .fluent-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border-radius: 8px;
            border: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            padding: 32px;
            margin-bottom: 20px;
        }

        /* Fluent Labels */
        .fluent-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #242424;
            margin-bottom: 8px;
            letter-spacing: -0.2px;
        }

        .fluent-description {
            font-size: 13px;
            color: #616161;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        /* Fluent Select */
        .fluent-select {
            width: 100%;
            padding: 10px 12px;
            font-size: 14px;
            border: 1px solid #d0d0d0;
            border-radius: 4px;
            background: white;
            color: #242424;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-group .fluent-select {
            margin-bottom: 0;
        }

        .fluent-select:hover {
            border-color: #b3b3b3;
        }

        .fluent-select:focus {
            outline: none;
            border-color: #0078d4;
            box-shadow: 0 0 0 2px rgba(0, 120, 212, 0.1);
        }

        /* Fluent Button */
        .fluent-button {
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 500;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s ease;
            letter-spacing: -0.2px;
        }

        .fluent-button-primary {
            background: #0078d4;
            color: white;
        }

        .fluent-button-primary:hover:not(:disabled) {
            background: #106ebe;
            box-shadow: 0 4px 12px rgba(0, 120, 212, 0.2);
        }

        .fluent-button-primary:disabled {
            background: #d0d0d0;
            color: #808080;
            cursor: not-allowed;
        }

        .fluent-button.full-width {
            width: 100%;
        }

        /* Fluent Radio */
        .fluent-radio-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
            contain: layout style paint;
        }

        .fluent-radio-option {
            display: flex;
            align-items: flex-start;
            padding: 12px;
            border: 1px solid #e1e1e1;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s ease;
            background: white;
        }

        .fluent-radio-option:hover {
            background: #f5f5f5;
            border-color: #0078d4;
        }

        .fluent-radio-option:has(input[type="radio"]:checked) {
            background: rgba(0, 120, 212, 0.15);
            border-color: #0078d4;
        }

        .fluent-radio-option.selected span {
            color: #0078d4;
            font-weight: 500;
        }

        .fluent-radio {
            width: 20px;
            height: 20px;
            min-width: 20px;
            min-height: 20px;
            border: 2px solid #d0d0d0;
            border-radius: 50%;
            cursor: pointer;
            margin-right: 12px;
            background: white;
            transition: all 0.2s ease;
            margin-top: 2px;
        }

        .fluent-radio:hover {
            border-color: #0078d4;
        }

        .fluent-radio-option.selected .fluent-radio {
            border-color: #0078d4;
            background: #0078d4;
            box-shadow: inset 0 0 0 4px white;
        }

        /* Fluent Progress Bar */
        .fluent-progress {
            height: 3px;
            background: #e1e1e1;
            border-radius: 2px;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .fluent-progress-bar {
            height: 100%;
            background: #0078d4;
            transition: width 0.3s ease;
        }

        /* Fluent Headings */
        h2 {
            font-size: 24px;
            font-weight: 600;
            color: #242424;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        h3 {
            font-size: 16px;
            font-weight: 500;
            color: #242424;
            margin-bottom: 12px;
            letter-spacing: -0.3px;
        }

        /* Fluent Results Card */
        .fluent-score-card {
            background: linear-gradient(135deg, #0078d4 0%, #106ebe 100%);
            color: white;
            border-radius: 8px;
            padding: 32px;
            text-align: center;
            margin-bottom: 24px;
        }

        .fluent-score-card .score-value {
            font-size: 48px;
            font-weight: 600;
            margin: 16px 0;
        }

        .fluent-score-card .score-label {
            font-size: 13px;
            opacity: 0.9;
        }

        .fluent-score-card .grade-value {
            font-size: 32px;
            font-weight: 600;
            margin: 12px 0;
        }

        .result-item {
            padding: 16px;
            border-left: 3px solid #0078d4;
            border-radius: 2px;
            margin-bottom: 12px;
            background: white;
        }

        .result-item.correct {
            border-left-color: #107c10;
            background: rgba(16, 124, 16, 0.02);
        }

        .result-item.incorrect {
            border-left-color: #e81123;
            background: rgba(232, 17, 35, 0.02);
        }

        .result-status {
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .result-status.correct {
            color: #107c10;
        }

        .result-status.incorrect {
            color: #e81123;
        }

        /* Utility Classes */
        .hidden {
            display: none !important;
        }

        .mb-8 {
            margin-bottom: 2rem;
        }

        /* Form Group Spacing */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            margin-bottom: 0;
        }

        /* Improved spacing for headings */
        h2 {
            margin-bottom: 1rem;
        }

        /* Improved card spacing */
        .fluent-card {
            margin-bottom: 2rem;
        }

        /* Selected quiz item styling */
        .quiz-history-item {
            padding: 16px;
            background: white;
            border: 1px solid #e1e1e1;
            border-radius: 4px;
            margin-bottom: 12px;
            transition: all 0.2s;
            cursor: pointer;
        }

        .quiz-history-item:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border-color: #0078d4;
        }

        .quiz-history-item.selected {
            background: rgba(0, 120, 212, 0.08);
            border-color: #0078d4;
            box-shadow: 0 4px 12px rgba(0, 120, 212, 0.2);
        }
    </style>
</head>

<body>
    <header style="padding: 1rem 2rem;">
        <div style="max-width: 900px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center;">
            <h1>Study Quiz</h1>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <span style="font-size: 14px; color: #424242;">Welcome, <span style="font-weight: 500;"><?php echo htmlspecialchars($username); ?></span></span>
                <a href="/signout"
                    class="fluent-button fluent-button-primary">
                    Sign Out</a>
            </div>
        </div>
    </header>

    <div style="max-width: 900px; margin: 2rem auto; padding: 0 1rem;">
        <!-- Navigation Tabs -->
        <div class="fluent-tabs mb-8" style="margin-left: 0; margin-right: 0; padding: 0 0 12px 0;">
            <button onclick="switchTab('newQuiz')" id="newQuizTab" class="fluent-tab active">
                New Quiz
            </button>
            <button onclick="switchTab('history')" id="historyTab" class="fluent-tab">
                Quiz History
            </button>
            <button onclick="switchTab('leaderboard')" id="leaderboardTab" class="fluent-tab">
                Leaderboard
            </button>
        </div>

        <!-- New Quiz Screen -->
        <div id="topicScreen">
            <div class="fluent-card">
                <h2>Create a Study Quiz</h2>
                <p class="fluent-description">Select the number of questions and start your quiz from our question bank.</p>

                <!-- Number of Questions -->
                <div class="form-group">
                    <label for="numQuestions" class="fluent-label">Number of Questions</label>
                    <select id="numQuestions" class="fluent-select">
                        <option value="5">5 Questions</option>
                        <option value="10">10 Questions</option>
                        <option value="15">15 Questions</option>
                        <option value="20" selected>20 Questions</option>
                        <option value="30">30 Questions</option>
                    </select>
                </div>

                <!-- Generate Button -->
                <button onclick="generateQuiz()" class="fluent-button fluent-button-primary full-width" id="generateBtn">
                    Start Quiz
                </button>
            </div>
        </div>

        <!-- Quiz Display Screen (hidden initially) -->
        <div id="quizScreen" class="hidden">
            <div class="fluent-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <div>
                        <h2 id="quizTopic" style="margin-bottom: 4px;"></h2>
                        <p class="fluent-description" style="margin-bottom: 0;">Question <span id="currentQuestion">1</span> of <span id="totalQuestions">20</span></p>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="fluent-progress">
                    <div id="progressBar" class="fluent-progress-bar" style="width: 0%"></div>
                </div>

                <!-- Questions Container -->
                <div id="questionsContainer" class="fluent-radio-group" style="margin-bottom: 24px;"></div>

                <!-- Submit Button -->
                <button onclick="submitQuiz()" class="fluent-button fluent-button-primary full-width" id="submitBtn">
                    Submit Quiz
                </button>
            </div>
        </div>

        <!-- Results Screen (hidden initially) -->
        <div id="resultsScreen" class="hidden">
            <div class="fluent-card">
                <h2 style="margin-bottom: 20px;">Quiz Results</h2>

                <!-- Score Card -->
                <div class="fluent-score-card">
                    <div class="score-label">Your Score</div>
                    <div class="score-value" id="scoreDisplay">0%</div>
                    <div class="grade-value" id="gradeDisplay">F</div>
                    <div class="score-label" id="correctDisplay">0 / 0 correct</div>
                </div>

                <!-- Review Section -->
                <div style="margin-bottom: 24px;">
                    <h3 style="margin-bottom: 16px;">Question Review</h3>
                    <div id="reviewContainer"></div>
                </div>

                <!-- Back Button -->
                <button onclick="backToHome()" class="fluent-button fluent-button-primary full-width">
                    Create Another Quiz
                </button>
            </div>
        </div>

        <!-- History Screen (hidden initially) -->
        <div id="historyScreen" class="hidden">
            <div class="fluent-card">
                <h2 style="margin-bottom: 20px;">Quiz History</h2>
                <div id="historyContainer"></div>
            </div>
        </div>

        <!-- Leaderboard Screen (hidden initially) -->
        <div id="leaderboardScreen" class="hidden">
            <div class="fluent-card">
                <h2 style="margin-bottom: 20px;">Global Leaderboard</h2>
                <p class="fluent-description">Top 10 players ranked by average score.</p>

                <!-- Current User's Rank Card -->
                <div id="userRankCard" style="background: linear-gradient(135deg, rgba(0, 120, 212, 0.1) 0%, rgba(16, 110, 190, 0.05) 100%); border: 2px solid #0078d4; border-radius: 8px; padding: 16px; margin-bottom: 24px; display: none;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <p style="font-size: 12px; color: #616161; margin-bottom: 4px;">YOUR RANKING</p>
                            <p id="userRankText" style="font-size: 16px; font-weight: 600; color: #0078d4; margin-bottom: 4px;">Rank #--</p>
                            <p id="userStatsText" style="font-size: 13px; color: #616161;"></p>
                        </div>
                        <div style="text-align: right;">
                            <p style="font-size: 32px; font-weight: 600; color: #0078d4;" id="userAvgScore">--%</p>
                            <p style="font-size: 12px; color: #616161;">Average Score</p>
                        </div>
                    </div>
                </div>

                <!-- Leaderboard Table -->
                <div id="leaderboardContainer"></div>
            </div>
        </div>
    </div>

    <script>
        let currentQuiz = null;
        let userAnswers = [];

        /* Leaderboard Styles */
        const leaderboardStyles = `
            .leaderboard-row {
                display: flex;
                align-items: center;
                padding: 16px;
                border: 1px solid #e1e1e1;
                border-radius: 4px;
                margin-bottom: 12px;
                background: white;
                transition: all 0.2s;
            }

            .leaderboard-row:hover {
                background: #f5f5f5;
                border-color: #0078d4;
                box-shadow: 0 2px 8px rgba(0, 120, 212, 0.1);
            }

            .leaderboard-rank {
                font-size: 20px;
                font-weight: 700;
                color: #0078d4;
                min-width: 50px;
                text-align: center;
                margin-right: 16px;
            }

            .leaderboard-rank.rank-1 {
                color: #FFD700;
                font-size: 24px;
            }

            .leaderboard-rank.rank-2 {
                color: #C0C0C0;
                font-size: 22px;
            }

            .leaderboard-rank.rank-3 {
                color: #CD7F32;
                font-size: 22px;
            }

            .leaderboard-user-info {
                flex: 1;
            }

            .leaderboard-username {
                font-weight: 600;
                color: #242424;
                font-size: 14px;
                margin-bottom: 4px;
            }

            .leaderboard-stats {
                font-size: 12px;
                color: #616161;
                display: flex;
                gap: 16px;
            }

            .leaderboard-score {
                text-align: right;
                min-width: 120px;
            }

            .leaderboard-avg {
                font-size: 18px;
                font-weight: 600;
                color: #0078d4;
                margin-bottom: 4px;
            }

            .leaderboard-label {
                font-size: 11px;
                color: #616161;
            }
        `;

        // Inject leaderboard styles
        if (document.head) {
            const style = document.createElement('style');
            style.textContent = leaderboardStyles;
            document.head.appendChild(style);
        }


        async function generateQuiz() {
            const numQuestions = document.getElementById('numQuestions').value;
            const generateBtn = document.getElementById('generateBtn');

            generateBtn.disabled = true;
            generateBtn.textContent = 'Starting Quiz...';

            try {
                const response = await fetch('/api/generate-quiz', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        topic: 'General Knowledge',
                        numQuestions: parseInt(numQuestions)
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.error || 'Failed to generate quiz');
                }

                currentQuiz = data;
                userAnswers = new Array(data.quiz.length).fill(null);
                displayQuiz();
            } catch (error) {
                alert('Error: ' + error.message);
            } finally {
                generateBtn.disabled = false;
                generateBtn.textContent = 'Start Quiz';
            }
        }

        function displayQuiz() {
            document.getElementById('topicScreen').classList.add('hidden');
            document.getElementById('quizScreen').classList.remove('hidden');
            document.getElementById('quizTopic').textContent = currentQuiz.topic;
            document.getElementById('totalQuestions').textContent = currentQuiz.quiz.length;

            const container = document.getElementById('questionsContainer');
            container.innerHTML = ''; // Clear existing
            
            // Use DocumentFragment for batch insertion - much faster!
            const fragment = document.createDocumentFragment();

            currentQuiz.quiz.forEach((q, index) => {
                const questionDiv = document.createElement('div');
                questionDiv.style.marginBottom = '24px';
                
                // Create choices HTML
                const choicesHTML = q.choices.map((choice, choiceIndex) => `
                    <label class="fluent-radio-option ${userAnswers[index] === choiceIndex ? 'selected' : ''}" onclick="selectAnswer(${index}, ${choiceIndex})">
                        <input type="radio" class="fluent-radio" name="question-${index}" value="${choiceIndex}" ${userAnswers[index] === choiceIndex ? 'checked' : ''} style="display: none;">
                        <span style="color: #242424; font-size: 14px;">${choice}</span>
                    </label>
                `).join('');
                
                questionDiv.innerHTML = `
                    <h3 style="margin-bottom: 12px;">${index + 1}. ${q.question}</h3>
                    <div class="fluent-radio-group">
                        ${choicesHTML}
                    </div>
                `;
                fragment.appendChild(questionDiv);
            });
            
            // Add all at once instead of one by one
            container.appendChild(fragment);

            updateProgressBar();
        }

        function selectAnswer(questionIndex, choiceIndex) {
            userAnswers[questionIndex] = choiceIndex;
            updateProgressBar();
            
            // Update visual selection - optimized with event delegation
            const container = document.getElementById('questionsContainer');
            const options = container.querySelectorAll(`label[name="question-${questionIndex}"]`);
            options.forEach((opt, idx) => {
                if (idx === choiceIndex) {
                    opt.classList.add('selected');
                } else {
                    opt.classList.remove('selected');
                }
            });
        }

        function updateProgressBar() {
            const answeredCount = userAnswers.filter(a => a !== null).length;
            const progress = (answeredCount / userAnswers.length) * 100;
            document.getElementById('progressBar').style.width = progress + '%';
            document.getElementById('currentQuestion').textContent = answeredCount;
        }

        async function submitQuiz() {
            if (userAnswers.includes(null)) {
                alert('Please answer all questions before submitting');
                return;
            }

            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Submitting...';

            try {
                const response = await fetch('/api/submit-quiz', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        answers: userAnswers
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.error || 'Failed to submit quiz');
                }

                displayResults(data.results);
            } catch (error) {
                alert('Error: ' + error.message);
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Submit Quiz';
            }
        }

        function displayResults(results) {
            document.getElementById('quizScreen').classList.add('hidden');
            document.getElementById('resultsScreen').classList.remove('hidden');

            const score = results.score;
            const grade = results.grade;
            const correct = results.correctCount;
            const total = results.totalCount;

            document.getElementById('scoreDisplay').textContent = score + '%';
            document.getElementById('gradeDisplay').textContent = grade;
            document.getElementById('correctDisplay').textContent = `${correct} / ${total} correct`;

            const reviewContainer = document.getElementById('reviewContainer');
            reviewContainer.innerHTML = '';

            results.review.forEach((result, index) => {
                const reviewDiv = document.createElement('div');
                reviewDiv.className = `result-item ${result.isCorrect ? 'correct' : 'incorrect'}`;
                reviewDiv.innerHTML = `
                    <div class="result-status ${result.isCorrect ? 'correct' : 'incorrect'}">
                        ${result.isCorrect ? '✓ Correct' : '✗ Incorrect'}
                    </div>
                    <p style="font-weight: 500; color: #242424; margin-bottom: 8px; font-size: 14px;">${index + 1}. ${result.question}</p>
                    <div style="font-size: 13px; color: #616161; line-height: 1.6;">
                        <p style="margin-bottom: 4px;"><span style="font-weight: 500;">Your answer:</span> ${result.choices[result.userAnswer]}</p>
                        ${!result.isCorrect ? `<p style="margin-bottom: 4px;"><span style="font-weight: 500;">Correct answer:</span> ${result.choices[result.correctAnswer]}</p>` : ''}
                        ${result.explanation ? `<p><span style="font-weight: 500;">Explanation:</span> ${result.explanation}</p>` : ''}
                    </div>
                `;
                reviewContainer.appendChild(reviewDiv);
            });
        }

        function backToHome() {
            document.getElementById('topicScreen').classList.remove('hidden');
            document.getElementById('resultsScreen').classList.add('hidden');
            document.getElementById('quizScreen').classList.add('hidden');
            currentQuiz = null;
            userAnswers = [];
        }

        function switchTab(tab) {
            const topicScreen = document.getElementById('topicScreen');
            const quizScreen = document.getElementById('quizScreen');
            const resultsScreen = document.getElementById('resultsScreen');
            const historyScreen = document.getElementById('historyScreen');
            const leaderboardScreen = document.getElementById('leaderboardScreen');
            const newQuizTab = document.getElementById('newQuizTab');
            const historyTab = document.getElementById('historyTab');
            const leaderboardTab = document.getElementById('leaderboardTab');

            // Hide all screens and remove active state from all tabs
            topicScreen.classList.add('hidden');
            quizScreen.classList.add('hidden');
            resultsScreen.classList.add('hidden');
            historyScreen.classList.add('hidden');
            leaderboardScreen.classList.add('hidden');
            newQuizTab.classList.remove('active');
            historyTab.classList.remove('active');
            leaderboardTab.classList.remove('active');

            // Show selected screen and activate tab
            if (tab === 'newQuiz') {
                topicScreen.classList.remove('hidden');
                newQuizTab.classList.add('active');
            } else if (tab === 'history') {
                historyScreen.classList.remove('hidden');
                historyTab.classList.add('active');
                loadHistory();
            } else if (tab === 'leaderboard') {
                leaderboardScreen.classList.remove('hidden');
                leaderboardTab.classList.add('active');
                loadLeaderboard();
            }
        }

        async function loadHistory() {
            const historyContainer = document.getElementById('historyContainer');
            historyContainer.innerHTML = '<p class="fluent-description">Loading...</p>';

            try {
                const response = await fetch('/api/quiz-history');
                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.error || 'Failed to load history');
                }

                historyContainer.innerHTML = '';

                if (data.quizzes.length === 0) {
                    historyContainer.innerHTML = '<p class="fluent-description">No quizzes taken yet.</p>';
                    return;
                }

                data.quizzes.forEach(quiz => {
                    const quizDiv = document.createElement('div');
                    quizDiv.className = 'quiz-history-item';
                    quizDiv.innerHTML = `
                        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                            <div style="flex: 1;">
                                <p style="font-weight: 500; font-size: 14px; color: #242424; margin-bottom: 4px;">${quiz.topic}</p>
                                <p style="font-size: 12px; color: #616161;">${new Date(quiz.completed_at).toLocaleDateString()}</p>
                            </div>
                            <div style="text-align: right;">
                                <p style="font-size: 24px; font-weight: 600; color: #0078d4; margin-bottom: 4px;">${quiz.score}%</p>
                                <p style="font-size: 12px; color: #616161;">Grade: <span style="font-weight: 600; color: #242424;">${quiz.grade}</span></p>
                                <p style="font-size: 12px; color: #616161; margin-top: 4px;">${quiz.correct_count}/${quiz.total_count} correct</p>
                            </div>
                        </div>
                    `;
                    quizDiv.addEventListener('click', () => {
                        document.querySelectorAll('.quiz-history-item').forEach(item => {
                            item.classList.remove('selected');
                        });
                        quizDiv.classList.add('selected');
                    });
                    historyContainer.appendChild(quizDiv);
                });
            } catch (error) {
                historyContainer.innerHTML = '<p style="color: #e81123; font-size: 13px;">Error loading history: ' + error.message + '</p>';
            }
        }

        async function loadLeaderboard() {
            const leaderboardContainer = document.getElementById('leaderboardContainer');
            const userRankCard = document.getElementById('userRankCard');
            leaderboardContainer.innerHTML = '<p class="fluent-description">Loading leaderboard...</p>';

            try {
                const response = await fetch('/api/leaderboard');
                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.error || 'Failed to load leaderboard');
                }

                leaderboardContainer.innerHTML = '';

                if (!data.topPlayers || data.topPlayers.length === 0) {
                    leaderboardContainer.innerHTML = '<p class="fluent-description">No players yet.</p>';
                    userRankCard.style.display = 'none';
                    return;
                }

                // Display top 10 players
                data.topPlayers.forEach((player, index) => {
                    const rank = index + 1;
                    let rankClass = '';
                    let rankDisplay = rank;

                    if (rank === 1) rankClass = 'rank-1', rankDisplay = '🥇';
                    else if (rank === 2) rankClass = 'rank-2', rankDisplay = '🥈';
                    else if (rank === 3) rankClass = 'rank-3', rankDisplay = '🥉';

                    const row = document.createElement('div');
                    row.className = 'leaderboard-row';
                    row.innerHTML = `
                        <div class="leaderboard-rank ${rankClass}">${rankDisplay}</div>
                        <div class="leaderboard-user-info">
                            <div class="leaderboard-username">${player.username}</div>
                            <div class="leaderboard-stats">
                                <span>${player.quiz_count} ${player.quiz_count === 1 ? 'quiz' : 'quizzes'}</span>
                                <span>Best: ${player.best_score}%</span>
                                <span>${player.total_correct}/${player.total_questions} correct</span>
                            </div>
                        </div>
                        <div class="leaderboard-score">
                            <div class="leaderboard-avg">${player.average_score}%</div>
                            <div class="leaderboard-label">Average</div>
                        </div>
                    `;
                    leaderboardContainer.appendChild(row);
                });

                // Display user's rank if they have taken quizzes
                if (data.userRank && data.userRank.quiz_count > 0) {
                    userRankCard.style.display = 'block';
                    document.getElementById('userRankText').textContent = `Rank #${data.userRank.rank}`;
                    document.getElementById('userAvgScore').textContent = `${data.userRank.average_score}%`;
                    document.getElementById('userStatsText').textContent = `${data.userRank.quiz_count} ${data.userRank.quiz_count === 1 ? 'quiz' : 'quizzes'} • Best: ${data.userRank.best_score}%`;
                } else {
                    userRankCard.style.display = 'none';
                }
            } catch (error) {
                leaderboardContainer.innerHTML = '<p style="color: #e81123; font-size: 13px;">Error loading leaderboard: ' + error.message + '</p>';
                userRankCard.style.display = 'none';
            }
        }
    </script>
</body>

</html>
