#!/bin/bash
# Script to fix the AUTO_INCREMENT issue in avaliacao_fisica table

echo "Applying database fix for avaliacao_fisica table..."

# Run the SQL fix inside the database container
docker-compose exec db mysql -u academia_user -pacademia_pass academiabd < fix_avaliacao_autoincrement.sql

if [ $? -eq 0 ]; then
    echo "✓ Database fix applied successfully!"
    echo "The avaliacao/create page should now work correctly."
else
    echo "✗ Error applying database fix. Please check Docker services are running."
fi
