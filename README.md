# Sunshine Insurance AI Policy Service Admin

An AI-assisted insurance policy customer service admin system built with **PHP 8.2**, **ThinkPHP 6.1**, **MySQL 8.0**, **Redis**, and **LayUI 2.9**.

This project is designed as a portfolio-ready backend management system for PHP/backend developer interviews. It demonstrates practical enterprise features such as RBAC authorization, customer and policy management, AI customer service integration, Redis rate limiting, operation auditing, Excel import/export, data masking, and secure API design.

## Resume Summary

Developed an AI-powered insurance policy customer service admin platform for Sunshine Insurance business scenarios, covering auto insurance, critical illness insurance, and medical insurance. The system supports customer and policy management, RBAC-based permission control, operation logging, sensitive data masking, Excel import/export, Redis caching, sliding-window rate limiting, and Qwen API integration for policy-aware AI Q&A. The backend follows ThinkPHP layered architecture and provides LayUI-compatible API responses.

## Tech Stack

- Backend: PHP 8.2, ThinkPHP 6.1
- Database: MySQL 8.0
- Cache and Rate Limiting: Redis
- Frontend Admin UI: LayUI 2.9
- AI Provider: Alibaba Cloud Qwen / Tongyi Qianwen API
- Excel Processing: PhpSpreadsheet
- Security: bcrypt password hashing, CSRF token validation, input filtering, RBAC middleware

## Key Features

### AI Customer Service

- Encapsulated Qwen API service class.
- Supports policy-aware context injection for AI Q&A.
- Uses Redis sliding-window rate limiting to prevent high-frequency model calls.
- Automatically retries failed AI requests up to two times.
- Blocks sensitive questions before calling the AI provider.
- Stores AI chat records in `ai_chat_log`.
- Tracks prompt tokens, completion tokens, total tokens, request status, and error messages.
- Caches repeated AI Q&A results in Redis to reduce model cost.

### RBAC Permission System

- User-role-menu permission model.
- Login middleware blocks unauthenticated requests.
- Permission middleware checks whether the current role can access the requested API.
- Menu permissions support directory, menu, and button/API levels.
- All create, update, delete, import, export, and AI operations are written to `system_operate_log`.

### Customer and Policy Management

- One customer can own multiple insurance policies.
- Policy types include auto insurance, critical illness insurance, and medical insurance.
- Policy status supports pending review, active, surrendered, and claim states.
- Customer and policy saving uses database transactions.
- ID card numbers and phone numbers are stored in plain text but displayed with masking.
- Supports Excel customer import and policy export.
- Redis caches policy statistics for better performance.

### Security and Performance

- Admin passwords are stored with bcrypt.
- CSRF form token middleware is enabled.
- Input filtering is included to reduce XSS risk.
- ThinkPHP ORM query builder is used to reduce SQL injection risk.
- Redis is used for AI rate limiting, AI answer cache, and policy statistics cache.
- Unified JSON response format is compatible with LayUI tables and popup forms.
- Global exception handling returns friendly API errors.

## Project Structure

```text
sunshine-ai-insurance-admin/
├── app/
│   ├── controller/admin/       # Admin controllers
│   ├── middleware/             # Auth, permission, security, rate limit middleware
│   ├── model/                  # Models for the 8 business tables
│   ├── service/                # Qwen AI, RBAC, policy, Redis services
│   ├── utils/                  # Excel import/export utility
│   ├── helper.php              # Data masking, response helpers, operation log helper
│   └── ExceptionHandle.php     # Global exception handler
├── config/                     # Database, Redis, AI, app configuration
├── database/                   # MySQL schema and password reset SQL
├── public/                     # Web entry and LayUI demo page
├── route/                      # API route definitions
├── composer.json
├── .env.example
├── .gitignore
└── README.md
```

## Database Schema

The full MySQL schema is available at:

[database/schema.sql](./database/schema.sql)

The project includes 8 tables:

- `admin_user`
- `admin_role`
- `admin_role_permission`
- `admin_menu`
- `insurance_customer`
- `insurance_policy`
- `ai_chat_log`
- `system_operate_log`

All tables use `utf8mb4` and include business-oriented field comments.

## Configuration

Main configuration files:

- [config/database.php](./config/database.php)
- [config/redis.php](./config/redis.php)
- [config/ai_config.php](./config/ai_config.php)

Environment variables are defined in:

[.env.example](./.env.example)

Important values:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sunshine_insurance_ai
DB_USERNAME=root
DB_PASSWORD=root

REDIS_HOST=127.0.0.1
REDIS_PORT=6379

DASHSCOPE_API_KEY=sk-your-qwen-api-key
DASHSCOPE_MODEL=qwen-plus
```

## API Response Format

All admin APIs return a LayUI-compatible JSON structure:

```json
{
  "code": 0,
  "msg": "success",
  "count": 100,
  "data": []
}
```

## Deployment

### 1. Install Environment

Required environment:

- PHP 8.2
- Composer
- MySQL 8.0
- Redis
- Nginx or Apache

### 2. Install Dependencies

```bash
composer install
```

### 3. Create Database

```bash
mysql -uroot -p -e "CREATE DATABASE sunshine_insurance_ai DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -uroot -p sunshine_insurance_ai < database/schema.sql
```

### 4. Configure Environment

```bash
cp .env.example .env
```

Update database, Redis, and Qwen API settings in `.env`.

### 5. Run Locally

```bash
php think run
```

Then open the local address printed by ThinkPHP, usually:

```text
http://127.0.0.1:8000
```

### 6. Nginx Example

```nginx
server {
    listen 80;
    server_name insurance-admin.local;
    root /www/sunshine-ai-insurance-admin/public;
    index index.php index.html;

    location / {
        if (!-e $request_filename) {
            rewrite ^(.*)$ /index.php?s=$1 last;
            break;
        }
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
}
```

## Default Admin Account

- Username: `admin`
- Password: `123456`

If you need to reset the default password, generate a bcrypt hash:

```bash
php -r "echo password_hash('123456', PASSWORD_BCRYPT), PHP_EOL;"
```

Then update the `admin_user.password` field in MySQL.

## GitHub Upload Notes

This repository should include source code and documentation only.

Do not upload:

- `vendor/`
- `.env`
- `runtime/`
- `node_modules/`
- generated EXE files
- packaged desktop output
- local IDE files

The included `.gitignore` already excludes these files.

## Suggested Interview Talking Points

- Designed a ThinkPHP layered backend with controller, service, model, middleware, helper, and utility modules.
- Implemented RBAC permission verification through middleware.
- Used Redis sorted sets to implement sliding-window AI rate limiting.
- Added AI request retry, sensitive word blocking, context-aware prompts, token statistics, and chat logging.
- Used database transactions to keep customer and policy data consistent.
- Applied global data masking for ID card and phone number display.
- Built Excel import/export with PhpSpreadsheet.

