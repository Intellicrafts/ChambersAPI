#!/bin/bash

# This script helps test the Docker container locally before deployment

# Exit on error
set -e

echo "Building Docker image..."
docker build -t chambers-api:local .

echo "Running container..."
docker run -p 8080:8080 chambers-api:local

# The container will start and you can access it at http://localhost:8080