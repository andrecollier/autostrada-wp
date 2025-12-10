#!/bin/bash
# ===========================================
# Database backup fra Kinsta (Live)
# ===========================================

set -e

# Last inn config
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/../.env"

TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_FILE="$SCRIPT_DIR/../database/exports/autostrada_${TIMESTAMP}.sql"

echo "=== Database Backup ==="
echo ""

# Kjør wp db export på Kinsta og hent filen
echo ">> Eksporterer database..."
ssh $SSH_HOST "cd $REMOTE_PATH && wp db export --add-drop-table - 2>/dev/null" > "$BACKUP_FILE"

# Komprimer
echo ">> Komprimerer..."
gzip "$BACKUP_FILE"

echo ""
echo "=== Backup lagret: ${BACKUP_FILE}.gz ==="
