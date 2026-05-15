# Deployment Guide

## Quick Start - Clone and Deploy

### Prerequisites
- Docker and Docker Compose installed
- Git

### Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd bcs350-project2
   ```

2. **Set up environment**
   ```bash
   cp .env.example .env
   # Edit .env and change SESSION_SECRET and passwords if needed
   ```

3. **Start the application**
   ```bash
   docker-compose up -d
   ```

4. **Access the application**
   Open your browser and go to `http://localhost`

5. **Stop the application**
   ```bash
   docker-compose down
   ```

---

## Environment Configuration

The `.env` file controls all configuration. Key variables:

- **DB_USER**: MySQL username (default: `quiz_user`)
- **DB_PASS**: MySQL password (default: `quiz_password`)
- **DB_NAME**: Database name (default: `quiz_app`)
- **DB_ROOT_PASSWORD**: MySQL root password (default: `root_password`)
- **APP_PORT**: Port to access the app (default: `80`)
- **SESSION_SECRET**: Change this to a random string in production

### Production Setup

Before deploying to production:

1. Edit `.env` and change all default passwords:
   ```bash
   DB_USER=secure_username
   DB_PASS=strong_password_here
   DB_ROOT_PASSWORD=another_strong_password
   SESSION_SECRET=random-secret-key-min-32-chars
   ```

2. Set appropriate port:
   ```bash
   APP_PORT=8080  # or your desired port
   ```

3. Change APP_ENV to production (optional):
   ```bash
   APP_ENV=production
   ```

---

## Database Management

### Access MySQL
```bash
docker-compose exec db mysql -u root -p quiz_app
```

### View Logs
```bash
docker-compose logs app
docker-compose logs db
```

### Reset Database
```bash
docker-compose down -v
docker-compose up -d
```

---

## Troubleshooting

### Port already in use
Change `APP_PORT` in `.env` to an available port.

### Database connection errors
Wait a few seconds for MySQL to initialize, then refresh the page.

### Application won't start
Check logs: `docker-compose logs app`

---

## Files Included

- `Dockerfile` - PHP/Apache container configuration
- `docker-compose.yml` - Orchestrates PHP app and MySQL database
- `.env` - Environment variables (customize for your deployment)
- `.env.example` - Template for environment variables
- `.dockerignore` - Files to exclude from Docker build
