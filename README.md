

# Laravel Project Setup with Filament and iSeed

This repository contains a Laravel-based project with **Filament Admin Panel** and **iSeed** for database seeding. Follow these steps to set up the project locally.

---

## 🚀 Installation Guide

### **1. Clone the Repository**
```bash
git clone https://github.com/your-username/your-repository.git
cd your-repository

2. Install Dependencies

composer install
npm install

🛠️ Environment Setup

3. Copy the .env File

cp .env.example .env

Update the .env file with your database credentials.

Example:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=root
DB_PASSWORD=

🔑 Generate App Key

php artisan key:generate

🏗️ Database Setup

4. Run Migrations and Seeders

php artisan migrate:fresh --seed

php artisan storage:link



php artisan serve

Visit http://127.0.0.1:8000 to view the application.

5. acess admin panel via http://127.0.0.1:8000/admin

6. login  to filament with email:test@admin.com password:test@admin.com


// To us with docker:
1. Build and Start the Containers
docker-compose up -d

2. Install Laravel Dependencies
docker-compose exec php bash
cd /var/www/html
composer install

3. Generate a new app key
php artisan key:generate

4. Database setup
php artisan migrate:fresh --seed

5. Storage symbolic link
php artisan storage:link

php artisan vendor:publish --tag=maps-views


#if you are facing issues with docker try the following command 
docker compose build --no-cache



