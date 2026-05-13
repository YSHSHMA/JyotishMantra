# jotishkendra.com – Web Application

> **Dev Branch**

This is the core web application for [Mahakal.com](https://jotishkendra.com), a spiritual and religious services platform that offers online pooja bookings, consultations, donations, and more.

## 🔧 Tech Stack

- **Framework:** Laravel (PHP)
- **Frontend:** Bootstrap, Core JavaScript
- **Backend:** PHP (Laravel MVC), Firebase (real-time features)
- **Database:** MySQL
- **Realtime:** Firebase + WhatsApp API
- **AI Integration:** OpenAI (Spiritual GPT Model)
- **DevOps:** Hostinger Shared Hosting (currently)
- **Third-party APIs:** Google Maps, WhatsApp Business

---

## 📁 Folder Structure

mahakal.com/
├── app/
├── bootstrap/
├── blog/ # Separate Laravel blog module
├── config/
├── database/
├── public/
├── resources/
├── routes/
├── storage/
│ ├── app/
│ ├── framework/ # Temp files (sessions, cache, views)
│ └── logs/
├── tests/
└── vendor/


---

## 🛠️ Requirements

- PHP >= 8.1
- Composer
- Node.js + NPM
- MySQL 5.7+
- Laravel 10+

---

## 🔐 Missing or Ignored Files

For security and performance, the following files/folders are **not included** in version control (`.gitignore`):

| File/Folder | Purpose |
|-------------|---------|
| `.env` | Application environment settings (DB, keys, etc.) |
| `blog/.env` | Separate environment for the blog module |
| `storage/framework/*` | Laravel temp files (views, sessions, cache) |
| `storage/logs/*` | Log files |
| `node_modules/` | Frontend dependencies |
| `vendor/` | PHP dependencies (Composer) |
| `.idea/`, `.vscode/` | Local IDE/editor configs |
| `messages` | Hindi and English message File |

> ⚠️ **You must create or copy these files manually** from the deployment or from secure backups before running the project locally.

---

## 🚀 Getting Started (Development Setup)

```bash
git clone https://github.com/your-org/mahakal.com.git
cd mahakal.com

# Install dependencies
composer install
npm install && npm run dev

# Set up environment
cp .env.example .env
php artisan key:generate

# Set permissions
chmod -R 775 storage bootstrap/cache

# Migrate DB
php artisan migrate
