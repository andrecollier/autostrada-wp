#!/bin/bash
# ===========================================
# Pull egenutviklet kode fra Kinsta (Live)
# ===========================================

set -e

# Last inn config
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/../.env"

echo "=== Pulling fra Kinsta Live ==="
echo ""

# Pull child theme
echo ">> Henter child theme: $CHILD_THEME"
rsync -avz --delete \
    -e "ssh" \
    "$SSH_HOST:$REMOTE_PATH/wp-content/themes/$CHILD_THEME/" \
    "$LOCAL_THEMES/$CHILD_THEME/"

echo ""

# Pull egenutviklede plugins
for plugin in $CUSTOM_PLUGINS; do
    echo ">> Henter plugin: $plugin"
    rsync -avz --delete \
        -e "ssh" \
        "$SSH_HOST:$REMOTE_PATH/wp-content/plugins/$plugin/" \
        "$LOCAL_PLUGINS/$plugin/"
done

echo ""
echo "=== Pull fullført ==="
