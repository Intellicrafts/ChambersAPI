# Deploying to Google Cloud Run - Simple Guide

This guide provides simple instructions for deploying your Laravel application to Google Cloud Run.

## Prerequisites

- Google Cloud Platform account with billing enabled
- Google Cloud SDK installed locally (optional)
- Git repository connected to Google Cloud Build

## Deployment Steps

### 1. Manual Deployment (No CI/CD)

If you want to deploy manually without setting up CI/CD:

1. Go to Google Cloud Console
2. Navigate to Cloud Run
3. Click "Create Service"
4. Choose "Deploy one revision from a source repository"
5. Connect your repository
6. Select the branch you want to deploy
7. Configure the build:
   - Build Type: Dockerfile
   - Dockerfile path: Dockerfile
8. Configure the service:
   - Service name: chambers-api-staging
   - Region: us-central1
   - CPU allocation: 1 (or as needed)
   - Memory: 512MiB (or as needed)
   - Request timeout: 300 seconds
   - Maximum number of instances: 10
   - Minimum number of instances: 0
9. Click "Create"

### 2. CI/CD Deployment

If you want to set up CI/CD:

1. Go to Google Cloud Console
2. Navigate to Cloud Build > Triggers
3. Click "Create Trigger"
4. Configure the trigger:
   - Name: chambers-api-staging
   - Event: Push to a branch
   - Source: ^main$ (regex for main branch)
   - Configuration: Cloud Build configuration file
   - Location: Repository
   - Cloud Build configuration file location: cloudbuild.yaml
5. Click "Create"

Now, whenever you push to the main branch, your application will be automatically deployed to Cloud Run.

### 3. Environment Variables

To set environment variables for your Cloud Run service:

1. Go to Cloud Run
2. Click on your service
3. Click "Edit and Deploy New Revision"
4. Scroll down to "Container, Networking, Security"
5. Click "Variables & Secrets"
6. Add your environment variables:
   - APP_KEY: Your Laravel application key
   - APP_ENV: production
   - APP_DEBUG: false
   - DB_CONNECTION: mysql
   - DB_HOST: Your database host
   - DB_PORT: 3306
   - DB_DATABASE: Your database name
   - DB_USERNAME: Your database username
   - DB_PASSWORD: Your database password
7. Click "Deploy"

### 4. Database Setup

For the database, you have several options:

1. **Cloud SQL**:
   - Create a Cloud SQL instance
   - Create a database and user
   - Configure your Laravel application to connect to it

2. **External Database**:
   - Use an existing database server
   - Configure your Laravel application to connect to it

### 5. Storage

For file storage, you have several options:

1. **Cloud Storage**:
   - Create a Cloud Storage bucket
   - Configure your Laravel application to use it

2. **Local Storage**:
   - Use the local filesystem (not recommended for production)
   - Note that Cloud Run instances are ephemeral, so files will be lost when the instance is restarted

## Troubleshooting

### Application Not Starting

If your application is not starting, check the logs:

1. Go to Cloud Run
2. Click on your service
3. Click on the latest revision
4. Click "Logs"

### Database Connection Issues

If you're having issues connecting to the database:

1. Make sure your database is accessible from Cloud Run
2. Check your environment variables
3. If using Cloud SQL, make sure you've configured the connection correctly

### Other Issues

For other issues, refer to the Google Cloud Run documentation:
https://cloud.google.com/run/docs/troubleshooting