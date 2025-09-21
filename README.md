
## Task Management documentation

## clone the repository
```bash
git clone <git-repo-url>
```

## Navigate to the project directory
```bash 
cd task-management
```

## Install dependencies
```bash
composer install
npm install
``` 

## Set up environment variables
```bash
cp .env.example .env
``` 

## Generate application key
```bash
php artisan key:generate
```

## Set up the database
```bash
- Create a new database (e.g., `task_management`).
- Update the `.env` file with your database credentials.

- OR Optionally, you can use Setup XAMPP :
  - Download and install XAMPP from [https://www.apachefriends.org/index.html](https://www.apachefriends.org/index.html).
  - Start the Apache and MySQL services from the XAMPP control panel.
  - Open phpMyAdmin by navigating to `http://localhost/phpmyadmin` in your web browser.
  - Create a new database named `task_management`.
  - Update the `.env` file in your Laravel project with the database credentials
    
```

## Run migrations
```bash
php artisan migrate
```

## Database seeding
```bash
php artisan db:seed
```

## Start the development server
```bash
composer run dev
```

## Access the application
```bash
- Open your web browser and navigate to `http://localhost:8000`.
```
## Default Admin Credentials
```bash
- Email: admin@gmail.com
- Password: Password123
```

## Default Reguler User Credentials
```bash
- Email: user@gmail.com
- Password: Password123
```