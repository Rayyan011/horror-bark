

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



php artisan serve

Visit http://127.0.0.1:8000 to view the application.

