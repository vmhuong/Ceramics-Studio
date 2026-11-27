#!/usr/bin/env bash
# =============================================================
# BOOKVERSE - SCRIPT BACKUP DATABASE TU DONG HANG NGAY (VPS LINUX)
# =============================================================
# Cai dat chay tu dong luc 02:00 sang moi ngay bang cron:
#   chmod +x scripts/backup-db.sh
#   crontab -e
#   0 2 * * * /path/to/bookverse/scripts/backup-db.sh >> /var/log/bookverse-backup.log 2>&1
# =============================================================

set -euo pipefail

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
BACKUP_DIR="${BACKUP_DIR:-$PROJECT_DIR/backups}"
RETENTION_DAYS="${RETENTION_DAYS:-14}"
DATE_TAG="$(date +%Y-%m-%d_%H%M)"
OUT_FILE="$BACKUP_DIR/bookverse_${DATE_TAG}.sql.gz"

mkdir -p "$BACKUP_DIR"

# Doc bien DB tu file .env neu co
if [[ -f "$PROJECT_DIR/.env" ]]; then
    set -a
    # shellcheck disable=SC1091
    source "$PROJECT_DIR/.env"
    set +a
fi

DB_NAME="${DB_NAME:-bookverse}"
DB_USER="${DB_USER:-bookverse_user}"
DB_PASSWORD="${DB_PASSWORD:-}"

echo "[$(date)] Bat dau backup database '$DB_NAME'..."

docker exec bookverse_db mariadb-dump \
    --default-character-set=utf8mb4 \
    --single-transaction \
    -u "$DB_USER" \
    "-p${DB_PASSWORD}" \
    "$DB_NAME" | gzip > "$OUT_FILE"

echo "[$(date)] Backup thanh cong: $OUT_FILE ($(du -h "$OUT_FILE" | cut -f1))"

# Xoa cac ban backup cu hon RETENTION_DAYS ngay de khong day o cung
find "$BACKUP_DIR" -name "bookverse_*.sql.gz" -type f -mtime +"$RETENTION_DAYS" -delete
echo "[$(date)] Da don cac ban backup cu hon $RETENTION_DAYS ngay."
