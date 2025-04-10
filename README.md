<!-- # Slim Framework 4 Skeleton Application

[![Coverage Status](https://coveralls.io/repos/github/slimphp/Slim-Skeleton/badge.svg?branch=master)](https://coveralls.io/github/slimphp/Slim-Skeleton?branch=master)

Use this skeleton application to quickly setup and start working on a new Slim Framework 4 application. This application uses the latest Slim 4 with Slim PSR-7 implementation and PHP-DI container implementation. It also uses the Monolog logger.

This skeleton application was built for Composer. This makes setting up a new Slim Framework application quick and easy.

## Install the Application

Run this command from the directory in which you want to install your new Slim Framework application. You will require PHP 7.4 or newer.

```bash
composer create-project slim/slim-skeleton [my-app-name]
```

Replace `[my-app-name]` with the desired directory name for your new application. You'll want to:

* Point your virtual host document root to your new application's `public/` directory.
* Ensure `logs/` is web writable.

To run the application in development, you can run these commands 

```bash
cd [my-app-name]
composer start
```

Or you can use `docker-compose` to run the app with `docker`, so you can run these commands:
```bash
cd [my-app-name]
docker-compose up -d
```
After that, open `http://localhost:8080` in your browser.

Run this command in the application directory to run the test suite

```bash
composer test
```

That's it! Now go build something cool. -->

# 🧩 Slim 4 + PostgreSQL Task Manager API

A simple RESTful Task Manager API built with SlimPHP, PostgreSQL, and Docker.

---

## ✅ Features

- Dockerized with `serversideup/php` + PostgreSQL
- Full CRUD on `/tasks` endpoint
- Pagination: `?page=1&limit=5`
- API Key Authentication (via `X-API-Key` header)
- `.env` config support

---

## 🚀 Quick Start

### 1. Clone the repository

```bash
git clone https://github.com/your-username/slim-task-api.git
cd slim-task-api






