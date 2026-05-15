<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/User.php';

function handleSignup() {
    global $pdo;

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Show signup form
        include __DIR__ . '/../views/signup.php';
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirmPassword'] ?? '';
        $error = null;

        // Validate input
        if (!$username || !$email || !$password || !$confirmPassword) {
            $error = 'All fields are required';
        } elseif ($password !== $confirmPassword) {
            $error = 'Passwords do not match';
        } else {
            $userModel = new User($pdo);

            // Check if user exists
            if ($userModel->usernameExists($username)) {
                $error = 'Username already exists';
            } elseif ($userModel->emailExists($email)) {
                $error = 'Email already exists';
            } else {
                try {
                    // Create new user
                    $userId = $userModel->create($username, $email, $password);

                    // Set session
                    $_SESSION['userId'] = $userId;
                    $_SESSION['username'] = $username;

                    header('Location: /');
                    exit();
                } catch (Exception $e) {
                    $error = 'Error creating account: ' . $e->getMessage();
                }
            }
        }

        include __DIR__ . '/../views/signup.php';
    }
}

function handleSignin() {
    global $pdo;

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Show signin form
        include __DIR__ . '/../views/signin.php';
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $error = null;

        if (!$email || !$password) {
            $error = 'Email and password are required';
        } else {
            $userModel = new User($pdo);
            $user = $userModel->findByEmail($email);

            if (!$user) {
                $error = 'Invalid email or password';
            } elseif (!$userModel->verifyPassword($password, $user['password'])) {
                $error = 'Invalid email or password';
            } else {
                // Set session
                $_SESSION['userId'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                header('Location: /');
                exit();
            }
        }

        include __DIR__ . '/../views/signin.php';
    }
}

function handleSignout() {
    session_destroy();
    header('Location: /signin.php');
    exit();
}
?>
