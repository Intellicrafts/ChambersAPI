#!/bin/bash

# This script helps set up the GCP environment for deploying the Laravel application
# Make sure to run this script with appropriate permissions

# Variables - replace these with your actual values
PROJECT_ID="your-project-id"
REGION="us-central1"
DB_INSTANCE="chambers-db-instance"
DB_NAME="bakil_laravel"
DB_USER="bakil_user"
DB_PASSWORD="your-secure-password"
STORAGE_BUCKET="chambers-storage-bucket"
SERVICE_NAME="chambers-api-staging"
VPC_CONNECTOR_NAME="chambers-vpc-connector"
SERVICE_ACCOUNT_NAME="chambers-service-account"

# Enable required APIs
echo "Enabling required APIs..."
gcloud services enable cloudbuild.googleapis.com \
    run.googleapis.com \
    sqladmin.googleapis.com \
    secretmanager.googleapis.com \
    vpcaccess.googleapis.com \
    storage.googleapis.com \
    artifactregistry.googleapis.com

# Create a VPC connector
echo "Creating VPC connector..."
gcloud compute networks vpc-access connectors create $VPC_CONNECTOR_NAME \
    --region=$REGION \
    --network=default \
    --range=10.8.0.0/28

# Create a Cloud SQL instance
echo "Creating Cloud SQL instance..."
gcloud sql instances create $DB_INSTANCE \
    --database-version=MYSQL_8_0 \
    --tier=db-f1-micro \
    --region=$REGION \
    --root-password=$DB_PASSWORD \
    --availability-type=zonal \
    --storage-size=10GB

# Create a database
echo "Creating database..."
gcloud sql databases create $DB_NAME --instance=$DB_INSTANCE

# Create a database user
echo "Creating database user..."
gcloud sql users create $DB_USER \
    --instance=$DB_INSTANCE \
    --password=$DB_PASSWORD

# Create a storage bucket
echo "Creating storage bucket..."
gsutil mb -l $REGION gs://$STORAGE_BUCKET

# Create a service account
echo "Creating service account..."
gcloud iam service-accounts create $SERVICE_ACCOUNT_NAME \
    --display-name="Chambers API Service Account"

# Grant permissions to the service account
echo "Granting permissions to service account..."
gcloud projects add-iam-policy-binding $PROJECT_ID \
    --member="serviceAccount:$SERVICE_ACCOUNT_NAME@$PROJECT_ID.iam.gserviceaccount.com" \
    --role="roles/cloudsql.client"

gcloud projects add-iam-policy-binding $PROJECT_ID \
    --member="serviceAccount:$SERVICE_ACCOUNT_NAME@$PROJECT_ID.iam.gserviceaccount.com" \
    --role="roles/storage.objectAdmin"

# Create secrets
echo "Creating secrets..."
echo -n "$DB_PASSWORD" | gcloud secrets create chambers-db-password --data-file=-
echo -n "$(php artisan key:generate --show)" | gcloud secrets create chambers-app-key --data-file=-

# Grant access to secrets
echo "Granting access to secrets..."
gcloud secrets add-iam-policy-binding chambers-db-password \
    --member="serviceAccount:$SERVICE_ACCOUNT_NAME@$PROJECT_ID.iam.gserviceaccount.com" \
    --role="roles/secretmanager.secretAccessor"

gcloud secrets add-iam-policy-binding chambers-app-key \
    --member="serviceAccount:$SERVICE_ACCOUNT_NAME@$PROJECT_ID.iam.gserviceaccount.com" \
    --role="roles/secretmanager.secretAccessor"

echo "Setup complete! Next steps:"
echo "1. Update your .env.production file with the correct values"
echo "2. Update cloudbuild.yaml with the correct substitution values"
echo "3. Connect your Git repository to Cloud Build"
echo "4. Push your code to trigger the deployment"