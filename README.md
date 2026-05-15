# BCS350 Project 2

## Requirements

- PHP 7.4+ with PDO and MySQL support
- MySQL 5.7+ or MariaDB

## Installation

### 1. Create Database

```bash
mysql -u root -p < db_schema.sql
```

Or manually create the database and run the SQL from `db_schema.sql`.

### 2. Configure Environment

Create a `.env` file in the project root (optional, defaults provided):

```env
DB_HOST=localhost
DB_USER=root
DB_PASS=your_password
DB_NAME=quiz_app
SESSION_SECRET=your-secret-key
```

### 3. Set Up Web Server

#### Using PHP Built-in Server (Development Only)

```bash
php -S localhost:8000
```

#### Using Apache/Nginx

Configure your web server to serve from the project root. Update the `.htaccess` file or web server configuration to route all requests to `index.php`.

#### For Apache (if .htaccess is not working):

Make sure `mod_rewrite` is enabled:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

Create/update `.htaccess`:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php [L]
</IfModule>
```

## File Structure

```
project-root/
├── index.php              # Main router
├── config.php             # Database and config
├── db_schema.sql          # Database schema
├── models/
│   ├── User.php          # User model
│   └── Quiz.php          # Quiz model
├── routes/
│   ├── auth.php          # Authentication routes
│   └── main.php          # Quiz and main routes
├── views/
│   ├── signin.php        # Sign in page
│   ├── signup.php        # Sign up page
│   └── main.php          # Main app page
└── public/
    ├── styles.css
    ├── output.css
    └── [other static files]
```

## API Endpoints

### Authentication
- `POST /signup` - Register new user
- `POST /signin` - Sign in user
- `GET /signout` - Sign out user

### Quiz Operations
- `GET /` - Main app page (requires authentication)
- `POST /api/generate-quiz` - Generate quiz using JSON questions file
- `POST /api/submit-quiz` - Submit quiz answers
- `GET /api/quiz-history` - Get user's quiz history
- `GET /api/quiz/{id}` - Get specific quiz details
- `GET /api/leaderboard` - Get global leaderboard with top 10 players

## Key Changes from Express/MongoDB

### Routing
- Express routing → PHP manual routing in `index.php`
- Route handlers in separate files under `routes/`

### Database
- MongoDB → MySQL with PDO
- Mongoose schemas → SQL table definitions
- JSON documents → Normalized relational tables

### Models
- Mongoose models → PHP classes with PDO queries
- Hooks → Regular class methods

### Authentication
- express-session → PHP native `$_SESSION`
- bcryptjs → PHP `password_hash()` and `password_verify()`

### Sessions
- In-memory (Express) → File/DB based (PHP)
- Quiz data stored in `$_SESSION['currentQuiz']`

### Quiz Generation
- Gemini API → JSON file (`questions.json`)
- Dynamic generation → Pre-loaded questions with keyword-based filtering
- No external API calls needed

### API Responses
- Express JSON → PHP `json_encode()` with proper headers

## Security Notes

- Passwords are hashed using PHP's `password_hash()` with BCRYPT
- Session timeout: 24 hours
- SQL injection prevention via prepared statements with PDO

## Environment Variables (Optional)

- `DB_HOST` - MySQL host (default: localhost)
- `DB_USER` - MySQL user (default: root)
- `DB_PASS` - MySQL password (default: empty)
- `DB_NAME` - MySQL database name (default: quiz_app)
- `SESSION_SECRET` - Session secret key (default: your-secret-key)

## Troubleshooting

### Database Connection Failed
- Verify MySQL is running
- Check credentials in `.env`
- Ensure database and tables exist

### 404 Errors on Routes
- For Apache: Enable `mod_rewrite` and create `.htaccess`
- For Nginx: Configure server block to route all requests to `index.php`
- For built-in server: Make sure you're using `php -S localhost:8000`

### Questions Not Loading
- Verify `questions.json` exists in the project root
- Ensure JSON file is valid (can be tested with online JSON validator)
- Check file permissions are readable

## Features

### Core Features
- **User Authentication**: Secure signup/signin with password hashing
- **Quiz Generation**: Dynamically select questions from local question bank
- **Quiz Submission**: Submit answers and receive instant feedback with score and grade
- **Quiz History**: Track all quizzes taken with scores, grades, and dates
- **Global Leaderboard**: View top 10 players ranked by average score with personal ranking displayed

### Leaderboard Features
- **Top 10 Ranking**: See the best performing users
- **Personal Ranking**: Your current rank among all players
- **Detailed Stats**: Average score, number of quizzes, best score, and accuracy
- **Real-time Updates**: Rankings update as new quizzes are submitted
- **Medal System**: Gold, silver, and bronze medals for top 3 positions

### User Interface
- **Microsoft Fluent Design**: Modern, clean interface with soft shadows and smooth transitions
- **Responsive Layout**: Works on desktop and tablet devices
- **Tab Navigation**: Easy switching between New Quiz, History, and Leaderboard
- **Progress Tracking**: Visual progress bar while taking quizzes
