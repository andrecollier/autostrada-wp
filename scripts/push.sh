#!/bin/bash
# ===========================================
# Push egenutviklet kode til Kinsta (Live)
# ===========================================

set -e

# Last inn config
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/../.env"

echo "=== ADVARSEL: Du pusher til LIVE ==="
echo ""
read -p "Er du sikker? (y/N): " confirm
if [[ "$confirm" != "y" && "$confirm" != "Y" ]]; then
    echo "Avbrutt."
    exit 0
fi

echo ""

# Push child theme
echo ">> Pusher child theme: $CHILD_THEME"
rsync -avz --delete \
    -e "ssh" \
    "$LOCAL_THEMES/$CHILD_THEME/" \
    "$SSH_HOST:$REMOTE_PATH/wp-content/themes/$CHILD_THEME/"

echo ""

# Push egenutviklede plugins
for plugin in $CUSTOM_PLUGINS; do
    echo ">> Pusher plugin: $plugin"
    rsync -avz --delete \
        -e "ssh" \
        "$LOCAL_PLUGINS/$plugin/" \
        "$SSH_HOST:$REMOTE_PATH/wp-content/plugins/$plugin/"
done

echo ""
echo "=== Push fullført ==="
