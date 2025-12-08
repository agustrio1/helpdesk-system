#!/bin/bash
# Kill semua PHP server di port 8000

PORT=8000

echo "🔍 Checking port $PORT..."

# Method 1: lsof
if command -v lsof &> /dev/null; then
    PIDS=$(lsof -ti:$PORT 2>/dev/null)
    if [ ! -z "$PIDS" ]; then
        echo "📍 Found process(es): $PIDS"
        echo "💀 Killing..."
        kill -9 $PIDS 2>/dev/null
        echo "✓ Killed with lsof"
    fi
fi

# Method 2: fuser
if command -v fuser &> /dev/null; then
    fuser -k $PORT/tcp 2>/dev/null
    echo "✓ Killed with fuser"
fi

# Method 3: pkill
pkill -9 -f "php -S.*:$PORT" 2>/dev/null
echo "✓ Killed with pkill"

# Verify
sleep 1
CHECK=$(lsof -ti:$PORT 2>/dev/null)
if [ -z "$CHECK" ]; then
    echo "✅ Port $PORT is now free!"
else
    echo "❌ Port $PORT still in use!"
    echo "Try manually: pkill -9 php"
fi
