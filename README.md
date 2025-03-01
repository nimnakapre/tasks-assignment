# Web Application Setup Guide

## Prerequisites

Ensure you have the following installed on your system:

- Docker
- Docker Compose
- PHP
- Composer
- Git
- Node.js & npm
- Angular CLI (Angular 19)

## Installation Steps

### 1. Clone the Repository

```sh
git clone <repo-url>
cd <repo-folder>
```

### 2. Install the Application

Run the following command to start the installation process:

```sh
php install.php
```

During the installation, you will be prompted to enter the following details:

- **Host Name**
- **Root User**
- **Root User Password**
- **Database Name**

### 3. Build and Start the Application

```sh
docker-compose build
docker-compose up -d
```

### 4. Install Angular Dependencies

```sh
cd frontend
npm install
```

### 5. Start the Frontend Server

```sh
ng serve --open
```

### 6. Start the Backend Server

```sh
cd backend
php -S localhost:8000
```

### 7. Running Tests

Run PHPUnit tests inside the Docker container:

```sh
docker-compose exec app bash -c "cd /var/www/html && ./vendor/bin/phpunit"
```

## Additional Notes

- Ensure that all environment variables are correctly set up in your `.env` file.
- If there are database migrations, ensure they are executed correctly.
- The backend runs on `localhost:8000`. Adjust accordingly if needed.
- The frontend runs on `localhost:8080` by default.

## Troubleshooting

- If you encounter permission issues, try running the commands with `sudo`.
- Ensure Docker and PHP services are running before executing the commands.
- Check container logs using `docker-compose logs` if the application fails to start.
- If the Angular app does not start, ensure you have the correct Node.js and npm versions installed.

## License

This project is open sourced.

