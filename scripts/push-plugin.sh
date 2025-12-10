#!/bin/bash
# ===========================================
# Push en spesifikk plugin til Kinsta (Live)
# Bruk: ./push-plugin.sh sircon-finn-cars
# ===========================================

set -e

if [ -z "$1" ]; then
    echo "Bruk: ./push-plugin.sh <plugin-navn>"
    echo "Eksempel: ./push-plugin.sh sircon-finn-cars"
    exit 1
fi

PLUGIN=$1

# Last inn config
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/../.env"

# Sjekk at plugin finnes lokalt
if [ ! -d "$LOCAL_PLUGINS/$PLUGIN" ]; then
    echo "Feil: Plugin '$PLUGIN' finnes ikke lokalt i $LOCAL_PLUGINS/"
    exit 1
fi

echo "=== Pusher plugin: $PLUGIN til LIVE ==="
echo ""
read -p "Er du sikker? (y/N): " confirm
if [[ "$confirm" != "y" && "$confirm" != "Y" ]]; then
    echo "Avbrutt."
    exit 0
fi

rsync -avz --delete \
    -e "ssh" \
    "$LOCAL_PLUGINS/$PLUGIN/" \
    "$SSH_HOST:$REMOTE_PATH/wp-content/plugins/$PLUGIN/"

echo ""
echo "=== Plugin $PLUGIN pushet ==="
