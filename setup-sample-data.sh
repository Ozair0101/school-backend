#!/bin/bash

# Setup script for the exam system
# This script will migrate the database and seed it with sample data

echo "Setting up the exam system database..."

# Run migrations
echo "Running migrations..."
php artisan migrate

# Seed the database
echo "Seeding the database with sample data..."
php artisan db:seed

echo "Setup complete! The database has been populated with sample data."
echo "You can now run the application and log in with the test user."
