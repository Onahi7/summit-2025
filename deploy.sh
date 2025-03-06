#!/bin/bash

# Exit on error
set -e

echo "Starting deployment process..."

# 1. Pull latest changes
echo "Pulling latest changes..."
git pull origin main

# 2. Install/update Composer dependencies
echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

# 3. Install/update NPM dependencies
echo "Installing NPM dependencies..."
npm install
npm run build

# 4. Clear all caches
echo "Clearing caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 5. Put application in maintenance mode
echo "Enabling maintenance mode..."
php artisan down

# 6. Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# 7. Cache bootstrap files
echo "Caching bootstrap files..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 8. Create storage link if not exists
echo "Creating storage link..."
php artisan storage:link

# 9. Optimize Composer's class autoloader
echo "Optimizing Composer's class autoloader..."
composer dump-autoload --optimize

# 10. Restart queue workers
echo "Restarting queue workers..."
php artisan queue:restart

# 11. Take application out of maintenance mode
echo "Disabling maintenance mode..."
php artisan up

# 12. Clear PHP OPcache
echo "Clearing PHP OPcache..."
curl -X GET http://localhost/clear-cache

echo "Deployment completed successfully!"
