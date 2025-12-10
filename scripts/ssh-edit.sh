#!/bin/bash
# ===========================================
# Åpne SSH-session til Kinsta for direkte arbeid
# ===========================================

# Last inn config
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
source "$SCRIPT_DIR/../.env"

echo "=== Kobler til Kinsta Live ==="
echo "Working directory: $REMOTE_PATH/wp-content"
echo ""

ssh -t $SSH_HOST "cd $REMOTE_PATH/wp-content && bash"
