# ChambersAPI Deployment to Google Cloud Run

This document provides comprehensive instructions for deploying the ChambersAPI Laravel application to Google Cloud Run as a staging server.

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Project Setup](#project-setup)
3. [Google Cloud Setup](#google-cloud-setup)
4. [Database Setup](#database-setup)
5. [Storage Setup](#storage-setup)
6. [CI/CD Pipeline Setup](#cicd-pipeline-setup)
7. [Deployment](#deployment)
8. [Post-Deployment Tasks](#post-deployment-tasks)
9. [Monitoring and Maintenance](#monitoring-and-maintenance)
10. [Troubleshooting](#troubleshooting)

## Prerequisites

- Google Cloud Platform account with billing enabled
- Google Cloud SDK installed locally
- Git repository (GitHub, GitLab, Bitbucket, etc.)
- Docker installed locally (for testing)
- Composer installed locally
- PHP 8.2 or higher installed locally

## Project Setup

### 1. Prepare Your Laravel Application

The following files have been added or modified to prepare the application for Cloud Run deployment:

- `Dockerfile`: Defines the container image
- `docker/nginx/default.conf`: Nginx configuration
- `docker/supervisor/supervisord.conf`: Supervisor configuration
- `docker/startup.sh`: Container startup script
- `.dockerignore`: Excludes unnecessary files from the Docker image
- `.gcloudignore`: Excludes unnecessary files from Cloud Build
- `.env.production`: Production environment variables template
- `cloudbuild.yaml`: CI/CD pipeline configuration
- `deploy.sh`: Deployment preparation script
- `setup-gcp.sh`: Google Cloud resources setup script

### 2. Test Locally

Before deploying to Cloud Run, test the application locally using Docker:

```bash
# Build the Docker image
docker build -t chambers-api:local .

# Run the container
docker run -p 8080:8080 chambers-api:local

# Test the health endpoint
curl http://localhost:8080/api/health
```

## Google Cloud Setup

### 1. Create a Google Cloud Project

If you haven't already created a project:

```bash
# Create a new project
gcloud projects create PROJECT_ID --name="ChambersAPI Staging"

# Set the project as the default
gcloud config set project PROJECT_ID

# Enable billing
gcloud billing projects link PROJECT_ID --billing-account=BILLING_ACCOUNT_ID
```

### 2. Enable Required APIs

```bash
# Enable required APIs
gcloud services enable cloudbuild.googleapis.com \
    run.googleapis.com \
    sqladmin.googleapis.com \
    secretmanager.googleapis.com \
    vpcaccess.googleapis.com \
    storage.googleapis.com \
    artifactregistry.googleapis.com
```

### 3. Set Up Service Account

```bash
# Create a service account
gcloud iam service-accounts create chambers-service-account \
    --display-name="ChambersAPI Service Account"

# Grant necessary permissions
gcloud projects add-iam-policy-binding PROJECT_ID \
    --member="serviceAccount:chambers-service-account@PROJECT_ID.iam.gserviceaccount.com" \
    --role="roles/cloudsql.client"

gcloud projects add-iam-policy-binding PROJECT_ID \
    --member="serviceAccount:chambers-service-account@PROJECT_ID.iam.gserviceaccount.com" \
    --role="roles/storage.objectAdmin"

gcloud projects add-iam-policy-binding PROJECT_ID \
    --member="serviceAccount:chambers-service-account@PROJECT_ID.iam.gserviceaccount.com" \
    --role="roles/secretmanager.secretAccessor"
```

### 4. Create VPC Connector

```bash
# Create a VPC connector for private networking
gcloud compute networks vpc-access connectors create chambers-vpc-connector \
    --region=us-central1 \
    --network=default \
    --range=10.8.0.0/28
```

## Database Setup

### 1. Create Cloud SQL Instance

```bash
# Create a Cloud SQL instance
gcloud sql instances create chambers-db-instance \
    --database-version=MYSQL_8_0 \
    --tier=db-f1-micro \
    --region=us-central1 \
    --root-password=YOUR_ROOT_PASSWORD \
    --availability-type=zonal \
    --storage-size=10GB
```

### 2. Create Database and User

```bash
# Create a database
gcloud sql databases create bakil_laravel --instance=chambers-db-instance

# Create a database user
gcloud sql users create bakil_user \
    --instance=chambers-db-instance \
    --password=YOUR_DB_PASSWORD
```

### 3. Store Database Credentials in Secret Manager

```bash
# Create secrets for database credentials
echo -n "YOUR_DB_PASSWORD" | gcloud secrets create chambers-db-password --data-file=-

# Grant access to the service account
gcloud secrets add-iam-policy-binding chambers-db-password \
    --member="serviceAccount:chambers-service-account@PROJECT_ID.iam.gserviceaccount.com" \
    --role="roles/secretmanager.secretAccessor"
```

## Storage Setup

### 1. Create Cloud Storage Bucket

```bash
# Create a storage bucket
gsutil mb -l us-central1 gs://chambers-storage-bucket

# Set bucket permissions
gsutil iam ch serviceAccount:chambers-service-account@PROJECT_ID.iam.gserviceaccount.com:objectAdmin gs://chambers-storage-bucket
```

### 2. Configure Laravel for Cloud Storage

The `config/filesystems.php` file has been updated to include Google Cloud Storage configuration:

```php
'gcs' => [
    'driver' => 'gcs',
    'project_id' => env('GOOGLE_CLOUD_PROJECT_ID'),
    'key_file' => env('GOOGLE_CLOUD_KEY_FILE', null),
    'bucket' => env('GOOGLE_CLOUD_STORAGE_BUCKET'),
    'path_prefix' => env('GOOGLE_CLOUD_STORAGE_PATH_PREFIX', null),
    'storage_api_uri' => env('GOOGLE_CLOUD_STORAGE_API_URI', null),
    'visibility' => 'public',
],
```

## CI/CD Pipeline Setup

### 1. Connect Repository to Cloud Build

1. Go to the Google Cloud Console
2. Navigate to Cloud Build > Triggers
3. Click "Connect Repository"
4. Select your Git provider and follow the instructions
5. Select the repository containing your Laravel application

### 2. Create Cloud Build Trigger

1. Go to Cloud Build > Triggers
2. Click "Create Trigger"
3. Configure the trigger:
   - Name: `chambers-api-staging-deploy`
   - Event: `Push to a branch`
   - Source: `^main$` (regex for main branch)
   - Configuration: `Cloud Build configuration file (yaml or json)`
   - Location: `Repository`
   - Cloud Build configuration file location: `cloudbuild.yaml`
4. Click "Create"

### 3. Configure Cloud Build Substitution Variables

1. Go to Cloud Build > Triggers
2. Click on the trigger you created
3. Click "Edit"
4. Scroll down to "Substitution variables"
5. Add the following variables:
   - `_APP_KEY`: Your Laravel application key
   - `_DB_HOST`: Cloud SQL private IP or connection name
   - `_DB_DATABASE`: `bakil_laravel`
   - `_DB_USERNAME`: `bakil_user`
   - `_DB_PASSWORD`: Your database password
   - `_MAIL_HOST`: Your mail server host
   - `_MAIL_USERNAME`: Your mail server username
   - `_MAIL_PASSWORD`: Your mail server password
   - `_CLOUDSQL_CONNECTION_NAME`: Your Cloud SQL connection name (PROJECT_ID:REGION:INSTANCE_NAME)
   - `_VPC_CONNECTOR`: `chambers-vpc-connector`
   - `_SERVICE_ACCOUNT`: `chambers-service-account@PROJECT_ID.iam.gserviceaccount.com`

## Deployment

### 1. Push Code to Repository

```bash
# Add all files
git add .

# Commit changes
git commit -m "Prepare for Cloud Run deployment"

# Push to main branch
git push origin main
```

### 2. Monitor Build Progress

1. Go to Cloud Build > History
2. Click on the latest build
3. Monitor the build logs

### 3. Verify Deployment

1. Go to Cloud Run > Services
2. Click on "chambers-api-staging"
3. Click on the URL to access the service
4. Test the health endpoint: `/api/health`

## Post-Deployment Tasks

### 1. Run Database Migrations

```bash
# Run migrations using Cloud Run jobs
gcloud run jobs execute chambers-api-migrations --region=us-central1
```

### 2. Set Up Domain Mapping (Optional)

```bash
# Map a custom domain to the Cloud Run service
gcloud run domain-mappings create --service=chambers-api-staging \
    --domain=staging-api.merabakil.com \
    --region=us-central1
```

### 3. Configure SSL Certificate (Optional)

If you've set up a custom domain, Cloud Run will automatically provision an SSL certificate.

## Monitoring and Maintenance

### 1. View Logs

```bash
# View application logs
gcloud logging read "resource.type=cloud_run_revision AND resource.labels.service_name=chambers-api-staging" --limit=50
```

### 2. Set Up Alerts

1. Go to Monitoring > Alerting
2. Click "Create Policy"
3. Configure alerts for:
   - High error rates
   - High latency
   - High CPU/memory usage

### 3. Set Up Uptime Checks

1. Go to Monitoring > Uptime Checks
2. Click "Create Uptime Check"
3. Configure a check for the health endpoint: `/api/health`

## Troubleshooting

### Database Connection Issues

If you encounter database connection issues:

1. Check the Cloud SQL instance status
2. Verify the database user credentials
3. Ensure the Cloud Run service has the correct IAM permissions
4. Check the VPC connector configuration
5. Verify the Cloud SQL connection name

### Storage Issues

If you encounter storage issues:

1. Verify the Cloud Storage bucket exists
2. Check the IAM permissions for the service account
3. Ensure the correct filesystem disk is configured in .env.production
4. Check the Google Cloud Storage configuration in `config/filesystems.php`

### Deployment Failures

If the deployment fails:

1. Check the Cloud Build logs for errors
2. Verify the Dockerfile is correct
3. Ensure all required environment variables are set
4. Check the service account permissions
5. Verify the Cloud Build trigger configuration