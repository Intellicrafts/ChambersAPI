# Deploying ChambersAPI to Google Cloud Run

This document provides instructions for deploying the ChambersAPI Laravel application to Google Cloud Run as a staging server.

## Prerequisites

- Google Cloud Platform account with billing enabled
- Google Cloud SDK installed locally
- Git repository connected to Google Cloud Build
- Basic knowledge of Docker and Laravel

## Deployment Steps

### 1. Set Up Google Cloud Environment

Run the provided setup script to create the necessary resources in Google Cloud:

```bash
# Make the script executable
chmod +x setup-gcp.sh

# Edit the script to update variables with your actual values
nano setup-gcp.sh

# Run the script
./setup-gcp.sh
```

### 2. Configure Environment Variables

Update the `.env.production` file with the correct values for your environment:

```bash
# Generate an application key
php artisan key:generate --show

# Update the .env.production file with the key and other values
nano .env.production
```

### 3. Update Cloud Build Configuration

Update the `cloudbuild.yaml` file with the correct substitution values:

```bash
# Edit the cloudbuild.yaml file
nano cloudbuild.yaml
```

### 4. Connect Git Repository to Cloud Build

1. Go to the Google Cloud Console
2. Navigate to Cloud Build > Triggers
3. Click "Create Trigger"
4. Connect your Git repository
5. Configure the trigger to build on push to the main branch
6. Use the cloudbuild.yaml file for the build configuration

### 5. Deploy the Application

Push your code to the main branch to trigger the deployment:

```bash
# Add all files
git add .

# Commit changes
git commit -m "Prepare for Cloud Run deployment"

# Push to main branch
git push origin main
```

### 6. Verify Deployment

1. Go to the Google Cloud Console
2. Navigate to Cloud Run
3. Click on the "chambers-api-staging" service
4. Check the deployment status and logs
5. Test the API endpoints

## Troubleshooting

### Database Connection Issues

If you encounter database connection issues:

1. Check the Cloud SQL instance status
2. Verify the database user credentials
3. Ensure the Cloud Run service has the correct IAM permissions
4. Check the VPC connector configuration

### Storage Issues

If you encounter storage issues:

1. Verify the Cloud Storage bucket exists
2. Check the IAM permissions for the service account
3. Ensure the correct filesystem disk is configured in .env.production

### Deployment Failures

If the deployment fails:

1. Check the Cloud Build logs for errors
2. Verify the Dockerfile is correct
3. Ensure all required environment variables are set
4. Check the service account permissions

## Maintenance

### Running Migrations

To run migrations manually:

```bash
gcloud run jobs execute chambers-api-migrations --region=us-central1
```

### Viewing Logs

To view application logs:

```bash
gcloud logging read "resource.type=cloud_run_revision AND resource.labels.service_name=chambers-api-staging" --limit=50
```

### Scaling

To adjust the scaling configuration:

```bash
gcloud run services update chambers-api-staging --min-instances=1 --max-instances=10
```