#!/bin/bash
# Script untuk fix permission Laravel di WSL + Docker
# Jalankan setiap kali ada error permission

sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R $USER:$USER . --exclude=storage --exclude=bootstrap 2>/dev/null

echo "✅ Permission di-fix"
