#!/bin/bash
echo "Copying SQLite DB to /tmp..."
cp database/database.sqlite /tmp/database.sqlite

php artisan migrate:fresh --seed --force