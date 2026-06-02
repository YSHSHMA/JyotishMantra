<div align="center">

# 🕉️ JyotishMantra

### India's Spiritual Marketplace for Puja, Temple Services, Astrology & Religious Products

<p align="center">
  <img src="./docs/jyotishmantra-homepage.png" alt="JyotishMantra Homepage" width="100%">
</p>

</div>

---

## 📖 About

JyotishMantra is a comprehensive spiritual and religious platform that connects devotees with temples, pandits, astrology services, puja bookings, donations, darshan services, religious products, yatras, and various Hindu spiritual offerings.

The platform provides:

- 🛕 Temple Booking & Darshan
- 🔱 Online & Offline Puja Services
- 📿 Rudraksha & Spiritual Products
- 🔮 Astrology Consultation
- 🎁 Religious Gift Cards
- 🚕 Tour & Travel Services
- 🎥 Live Darshan
- 🙏 Donations & Chadhava
- 📚 Spiritual Content & Blogs
- 👨‍🏫 Verified Pandits & Priests

---

## 🚀 Features

### Temple Services
- Temple Listings
- Darshan Booking
- Temple Donations
- Chadhava Services
- VIP Temple Services

### Puja Services
- Online Puja
- Offline Puja
- Pandit Booking
- Customized Rituals
- Festival Special Puja

### Astrology
- Kundli Services
- Horoscope
- Astrology Consultation
- Birth Journal
- Rashifal

### Marketplace
- Religious Products
- Rudraksha
- Yantra
- Prasad Delivery
- Spiritual Accessories

### Tours & Events
- Religious Tours
- Temple Tours
- Event Booking
- Spiritual Retreats

---

# 🏗️ Tech Stack

| Layer | Technology |
|---------|------------|
| Backend | Laravel 10 |
| Frontend | Blade Templates |
| Database | MySQL 8 |
| Web Server | Nginx |
| Containerization | Docker |
| Admin Panel | Laravel Admin |
| Authentication | Laravel Auth |
| File Storage | Laravel Storage |

---

# 🐳 Docker Setup

## Prerequisites

- Docker
- Docker Compose

Verify installation:

```bash
docker --version
docker compose version
```

---

## Start Containers

```bash
docker compose up -d
```

Build again if required:

```bash
docker compose up -d --build
```

Check running containers:

```bash
docker ps
```

---

## Application URLs

| Service | URL |
|----------|------|
| Website | http://localhost:8000 |
| PhpMyAdmin | http://localhost:8080 |

---

# 🗄️ Database Configuration

## Main Database

```env
DB_CONNECTION=mysql
DB_HOST=database
DB_PORT=3306
DB_DATABASE=mahakal_admin
DB_USERNAME=root
DB_PASSWORD=root
```

---

## Blog Database

```env
DB_CONNECTION_SECOND=mysql
DB_HOST_SECOND=database
DB_PORT_SECOND=3306
DB_DATABASE_SECOND=mahakal_blog
DB_USERNAME_SECOND=root
DB_PASSWORD_SECOND=root
```

---

# 📦 Create Required Databases

Enter MySQL container:

```bash
docker exec -it database mysql -uroot -proot
```

Create databases:

```sql
CREATE DATABASE IF NOT EXISTS mahakal_admin;
CREATE DATABASE IF NOT EXISTS mahakal_blog;
```

Verify:

```sql
SHOW DATABASES;
```

---

# 🔄 Laravel Installation

Enter container:

```bash
docker exec -it application bash
```

Install dependencies:

```bash
composer install
```

Generate application key:

```bash
php artisan key:generate
```

---

# 🧱 Database Migration

Run main database migrations:

```bash
php artisan migrate
```

---

## Blog Database Migration

```bash
php artisan migrate --database=mysql2
```

---

## Fresh Migration

```bash
php artisan migrate:fresh
```

Seed database:

```bash
php artisan db:seed
```

Or:

```bash
php artisan migrate:fresh --seed
```

---

# 🔧 Cache Commands

Clear config cache:

```bash
php artisan config:clear
```

Clear application cache:

```bash
php artisan cache:clear
```

Clear route cache:

```bash
php artisan route:clear
```

Clear views:

```bash
php artisan view:clear
```

Rebuild config:

```bash
php artisan config:cache
```

---

# 📁 Storage Permissions

If using Linux:

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

For Docker:

```bash
docker exec -it application chmod -R 775 storage bootstrap/cache
```

---

# 🐛 Common Issues

## SQLSTATE[HY000] [2002]

Check database connection:

```bash
php artisan config:clear
php artisan cache:clear
```

Verify environment:

```bash
php artisan tinker

config('database.connections.mysql')
config('database.connections.mysql2')
```

---

## Permission Denied

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

---

## Container Not Running

```bash
docker compose ps
```

Restart:

```bash
docker compose restart
```

---

# 📸 Screenshots

## Home Page

![Homepage](./public/jyotishmantra-homepage.png)

---

# 🤝 Contributing

1. Fork repository
2. Create feature branch

```bash
git checkout -b feature/new-feature
```

3. Commit changes

```bash
git commit -m "Add new feature"
```

4. Push

```bash
git push origin feature/new-feature
```

5. Create Pull Request

---

# 📄 License

This project is proprietary software owned by JyotishMantra.

---

<div align="center">

### Made with ❤️ for Devotees and Spiritual Communities

**JyotishMantra**

</div>