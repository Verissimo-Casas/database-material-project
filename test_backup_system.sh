#!/bin/bash

# Test script for backup functionality
echo "=== Testing Backup System ==="

# Check if Docker is running
if ! docker-compose ps | grep -q "Up"; then
    echo "❌ Docker containers are not running. Please start them first:"
    echo "   docker-compose up -d"
    exit 1
fi

echo "✅ Docker containers are running"

# Test database connection
if docker-compose exec -T db mysql -u academia_user -pacademia_pass -e "SELECT 1" academiabd > /dev/null 2>&1; then
    echo "✅ Database connection successful"
else
    echo "❌ Database connection failed"
    exit 1
fi

# Test backup creation
echo "🔄 Creating test backup..."
BACKUP_FILE="backups/test_backup_$(date +%Y%m%d_%H%M%S).sql"

if docker-compose exec -T db mysqldump -u academia_user -pacademia_pass academiabd > "$BACKUP_FILE" 2>/dev/null; then
    if [ -f "$BACKUP_FILE" ] && [ -s "$BACKUP_FILE" ]; then
        echo "✅ Backup created successfully: $BACKUP_FILE"
        echo "   Size: $(du -h "$BACKUP_FILE" | cut -f1)"
        
        # Clean up test backup
        rm "$BACKUP_FILE"
        echo "🧹 Test backup cleaned up"
    else
        echo "❌ Backup file is empty or not created"
        exit 1
    fi
else
    echo "❌ Failed to create backup"
    exit 1
fi

echo ""
echo "=== Backup System Test Complete ==="
echo "✅ All tests passed!"
echo ""
echo "You can now access the backup system at:"
echo "   http://localhost:8080/backup"
echo ""
echo "Make sure to log in as an administrator to access the backup functionality."
