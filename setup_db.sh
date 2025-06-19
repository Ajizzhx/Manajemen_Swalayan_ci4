#!/bin/bash

echo "Initializing Swalayan CI4 Database..."
echo

# Run the direct PHP setup script first
php setup_database.php
echo

# Run the CI4 migrations
php spark migrate

# Run the seeds
php spark db:seed InitialDataSeeder

echo
echo "Setup complete! You can now start using the application."
echo
echo "Default login credentials:"
echo
echo "  Admin:"
echo "    Email: admin@swalayan.com"
echo "    Password: admin123"
echo
echo "  Owner:"
echo "    Email: owner@swalayan.com"
echo "    Password: owner123"
echo
echo "  Kasir:"
echo "    Email: kasir@swalayan.com"
echo "    Password: kasir123"

read -p "Press Enter to continue..."
