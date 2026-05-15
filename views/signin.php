<?php
require_once __DIR__ . '/../config.php';

if (isAuthenticated()) {
    header('Location: /');
    exit();
}

$error = $error ?? null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In - Study Quiz</title>
    <link rel="stylesheet" href="/output.css">
    <style>
        * {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .auth-container {
            width: 100%;
            max-width: 420px;
        }

        .fluent-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 8px;
            border: 1px solid rgba(0, 0, 0, 0.06);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            padding: 32px;
        }

        h1 {
            font-size: 24px;
            font-weight: 600;
            color: #242424;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .subtitle {
            font-size: 14px;
            color: #616161;
            margin-bottom: 24px;
            line-height: 1.5;
        }

        .fluent-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #242424;
            margin-bottom: 8px;
            letter-spacing: -0.2px;
        }

        .fluent-input {
            width: stretch;
            padding: 10px 12px;
            font-size: 14px;
            border: 1px solid #d0d0d0;
            border-radius: 4px;
            background: white;
            color: #242424;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-group .fluent-input {
            margin-bottom: 0;
        }

        .fluent-input:hover {
            border-color: #b3b3b3;
        }

        .fluent-input:focus {
            outline: none;
            border-color: #0078d4;
            box-shadow: 0 0 0 2px rgba(0, 120, 212, 0.1);
        }

        .fluent-button {
            width: 100%;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 500;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s ease;
            letter-spacing: -0.2px;
            background: #0078d4;
            color: white;
            margin-top: 1.5rem;
            margin-bottom: 0;
        }

        .fluent-button:hover:not(:disabled) {
            background: #106ebe;
            box-shadow: 0 4px 12px rgba(0, 120, 212, 0.2);
        }

        .error-message {
            padding: 12px;
            background: rgba(232, 17, 35, 0.08);
            border-left: 3px solid #e81123;
            border-radius: 2px;
            color: #e81123;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .auth-footer {
            text-align: center;
            font-size: 13px;
            color: #616161;
        }

        .auth-footer a {
            color: #0078d4;
            text-decoration: none;
            font-weight: 500;
        }

        .auth-footer a:hover {
            text-decoration: underline;
        }

        /* Form Group Spacing for better vertical rhythm */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-group input {
            margin-bottom: 0;
        }
    </style>
</head>

<body>
    <div class="auth-container">
        <div class="fluent-card">
            <h1>Study Quiz</h1>
            <p class="subtitle">Sign in to your account</p>

            <?php if ($error): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/signin">
                <div class="form-group">
                    <label for="email" class="fluent-label">Email</label>
                    <input type="email" id="email" name="email" required autofocus class="fluent-input" placeholder="you@example.com">
                </div>

                <div class="form-group">
                    <label for="password" class="fluent-label">Password</label>
                    <input type="password" id="password" name="password" required class="fluent-input" placeholder="Enter your password">
                </div>

                <button type="submit" class="fluent-button">
                    Sign In
                </button>
            </form>

            <div class="auth-footer">
                Don't have an account? <a href="/signup">Sign up</a>
            </div>
        </div>
    </div>
</body>

</html>
