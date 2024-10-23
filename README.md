# Product Management System

This project is a Laravel-based product management system designed to import, manage, and synchronize product data from a CSV file and an external API.

## Table of Contents

- [Installation](#installation)
- [Environment Setup](#environment-setup)
- [Running Migrations](#running-migrations)
- [Importing Products](#importing-products)
- [Scheduling API Synchronization](#scheduling-api-synchronization)
- [Testing](#testing)

## Installation

1. **Clone the repository**:
   ```bash
   git clone https://github.com/Abencherif/imense.git
   cd product-management-system
## Environment Setup
2. **Install dependencies**:
   ```bash
   composer install
   ```
   The Project is using sqlite database is by default


3. **Set up .env file**:
   ```bash
   cp .env.example .env
## Running Migrations
4. **Run Migrations**:
   ```bash
   php artisan migrate
## Importing Products
5. **Importing Products**:
   ```bash
    php artisan import:products
    ```
   'products.csv' is located on 'storage/csv/products.csv'
## Scheduling API Synchronization
6. **API Synchronization Command**
    ```bash
    php artisan sync:products
    ```
   This Command is schedule to run daily  at 12am.
## Testing
7. **Testing**
    ```bash
    php artisan test
