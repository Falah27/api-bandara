#!/bin/bash

# ===================================
# BACKUP SCRIPT - RUN SEBELUM UPGRADE
# ===================================

TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_DIR="backups/$TIMESTAMP"

echo "🔵 Starting Backup Process..."
echo "📅 Timestamp: $TIMESTAMP"

# 1. Buat folder backup
mkdir -p $BACKUP_DIR

# 2. Backup Database
echo "💾 Backing up database..."
mysqldump -u root api_safety > $BACKUP_DIR/database_backup.sql

if [ $? -eq 0 ]; then
    echo "✅ Database backup successful!"
else
    echo "❌ Database backup failed!"
    exit 1
fi

# 3. Backup .env
echo "📄 Backing up .env file..."
cp .env $BACKUP_DIR/.env.backup

# 4. Backup migrations (jaga-jaga)
echo "📦 Backing up migrations..."
cp -r database/migrations $BACKUP_DIR/migrations_backup

# 5. Backup seeders
echo "🌱 Backing up seeders..."
cp -r database/seeders $BACKUP_DIR/seeders_backup

# 6. Create restore script
cat > $BACKUP_DIR/RESTORE.sh << 'EOF'
#!/bin/bash
echo "🔄 Restoring from backup..."
mysql -u root api_safety < database_backup.sql
echo "✅ Database restored!"
echo "⚠️  Jangan lupa rollback migration dengan: php artisan migrate:rollback"
EOF

chmod +x $BACKUP_DIR/RESTORE.sh

echo ""
echo "✅ BACKUP COMPLETED!"
echo "📁 Location: $BACKUP_DIR"
echo "🔄 To restore: cd $BACKUP_DIR && bash RESTORE.sh"
echo ""