# PostgreSQL Connection Guide for Cloud Run

This guide provides instructions for connecting your Laravel application to a PostgreSQL database on Google Cloud SQL.

## Cloud SQL Connection Details

- **Instance Name**: master-db
- **Connection Name**: intellicrafts-dev:asia-south1:master-db
- **Public IP Address**: 34.100.232.139
- **Port**: 5432

## Laravel Configuration

### 1. Update .env File

Make sure your `.env` file has the following configuration:

```
DB_CONNECTION=pgsql
DB_HOST=34.100.232.139
DB_PORT=5432
DB_DATABASE=chambers_api
DB_USERNAME=postgres
DB_PASSWORD=your_password_here
```

### 2. Update config/database.php

Make sure your `config/database.php` file has the correct PostgreSQL configuration:

```php
'pgsql' => [
    'driver' => 'pgsql',
    'url' => env('DATABASE_URL'),
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '5432'),
    'database' => env('DB_DATABASE', 'forge'),
    'username' => env('DB_USERNAME', 'forge'),
    'password' => env('DB_PASSWORD', ''),
    'charset' => 'utf8',
    'prefix' => '',
    'prefix_indexes' => true,
    'search_path' => 'public',
    'sslmode' => 'prefer',
],
```

## Cloud Run Configuration

When deploying to Cloud Run, you need to set the following environment variables:

- `DB_CONNECTION`: pgsql
- `DB_HOST`: 34.100.232.139
- `DB_PORT`: 5432
- `DB_DATABASE`: chambers_api
- `DB_USERNAME`: postgres
- `DB_PASSWORD`: your_password_here

You also need to set the Cloud SQL connection name:

- `CLOUDSQL_CONNECTION_NAME`: intellicrafts-dev:asia-south1:master-db

## Connecting from Local Development Environment

### Using Cloud SQL Proxy

1. Install the Cloud SQL Proxy:
   ```
   curl -o cloud-sql-proxy https://storage.googleapis.com/cloud-sql-connectors/cloud-sql-proxy/v2.0.0/cloud-sql-proxy.linux.amd64
   chmod +x cloud-sql-proxy
   ```

2. Start the proxy:
   ```
   ./cloud-sql-proxy intellicrafts-dev:asia-south1:master-db
   ```

3. Update your `.env` file to connect to the proxy:
   ```
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=chambers_api
   DB_USERNAME=postgres
   DB_PASSWORD=your_password_here
   ```

### Using Direct Connection

You can also connect directly to the database using the public IP address:

```
psql -h 34.100.232.139 -p 5432 -U postgres -d chambers_api
```

## Creating the Database

If the database doesn't exist yet, you can create it using the following command:

```
psql -h 34.100.232.139 -p 5432 -U postgres -c "CREATE DATABASE chambers_api;"
```

## Running Migrations

To run migrations on the PostgreSQL database:

```
php artisan migrate
```

## Troubleshooting

### Connection Issues

If you're having trouble connecting to the database:

1. Make sure the public IP address is correct
2. Make sure the database user has the correct permissions
3. Make sure the database exists
4. Check if the Cloud SQL instance has the "Public IP" option enabled
5. Check if the Cloud SQL instance has authorized your IP address

### Cloud Run Connection Issues

If your Cloud Run service can't connect to the database:

1. Make sure the `CLOUDSQL_CONNECTION_NAME` is correct
2. Make sure the service account has the necessary permissions
3. Make sure the Cloud SQL Admin API is enabled
4. Check the Cloud Run logs for error messages