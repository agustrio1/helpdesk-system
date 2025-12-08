#!/bin/bash

# Script untuk set permission yang benar
# Jalankan: bash fix-permissions.sh

# Warna
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m'

echo -e "${GREEN}╔════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║   Fixing Permissions & Ownership      ║${NC}"
echo -e "${GREEN}╚════════════════════════════════════════╝${NC}"
echo ""

# Detect web server user
WEB_USER=""
if [ -f "/etc/debian_version" ]; then
    WEB_USER="www-data"
elif [ -f "/etc/redhat-release" ]; then
    WEB_USER="apache"
else
    WEB_USER="www-data"
fi

echo -e "${YELLOW}[INFO] Detected web server user: $WEB_USER${NC}"
echo ""

# Root directory permission
echo -e "${YELLOW}[STEP 1/6] Setting root directory permissions...${NC}"
chmod 755 .
echo -e "${GREEN}✓ Root directory: 755${NC}"

# Public directory dan subdirectories
echo -e "${YELLOW}[STEP 2/6] Setting public directory permissions...${NC}"
if [ -d "public" ]; then
    chmod 755 public
    find public -type d -exec chmod 755 {} \;
    find public -type f -exec chmod 644 {} \;
    echo -e "${GREEN}✓ Public directory: 755${NC}"
    echo -e "${GREEN}✓ Public files: 644${NC}"
else
    echo -e "${RED}✗ Public directory not found!${NC}"
fi

# Assets directory
echo -e "${YELLOW}[STEP 3/6] Setting assets permissions...${NC}"
if [ -d "public/assets" ]; then
    chmod -R 755 public/assets
    find public/assets -type f -exec chmod 644 {} \;
    echo -e "${GREEN}✓ Assets directory: 755${NC}"
    echo -e "${GREEN}✓ Assets files: 644${NC}"
    
    # List assets
    echo -e "${YELLOW}   Found assets:${NC}"
    if [ -d "public/assets/css" ]; then
        echo -e "   - CSS: $(find public/assets/css -type f | wc -l) files"
    fi
    if [ -d "public/assets/js" ]; then
        echo -e "   - JS: $(find public/assets/js -type f | wc -l) files"
    fi
else
    echo -e "${RED}✗ Assets directory not found!${NC}"
fi

# Storage/uploads directories (writable)
echo -e "${YELLOW}[STEP 4/6] Setting writable directories...${NC}"
WRITABLE_DIRS=("storage" "storage/logs" "storage/cache" "storage/uploads" "public/uploads")

for dir in "${WRITABLE_DIRS[@]}"; do
    if [ -d "$dir" ]; then
        chmod -R 775 "$dir"
        echo -e "${GREEN}✓ $dir: 775 (writable)${NC}"
    else
        mkdir -p "$dir"
        chmod -R 775 "$dir"
        echo -e "${GREEN}✓ Created $dir: 775${NC}"
    fi
done

# Protected files
echo -e "${YELLOW}[STEP 5/6] Protecting sensitive files...${NC}"
PROTECTED_FILES=(".env" "composer.json" "composer.lock")

for file in "${PROTECTED_FILES[@]}"; do
    if [ -f "$file" ]; then
        chmod 600 "$file"
        echo -e "${GREEN}✓ Protected $file: 600${NC}"
    fi
done

# Set ownership (optional, requires sudo)
echo -e "${YELLOW}[STEP 6/6] Setting ownership...${NC}"
if [ "$EUID" -eq 0 ]; then
    chown -R $WEB_USER:$WEB_USER .
    echo -e "${GREEN}✓ Owner set to $WEB_USER${NC}"
else
    echo -e "${YELLOW}⚠ Skipped (run with sudo for ownership change)${NC}"
    echo -e "${YELLOW}   To set owner: sudo chown -R $WEB_USER:$WEB_USER .${NC}"
fi

echo ""
echo -e "${GREEN}╔════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║         Permissions Fixed! ✓           ║${NC}"
echo -e "${GREEN}╚════════════════════════════════════════╝${NC}"
echo ""

# Verify assets are readable
echo -e "${YELLOW}[VERIFY] Checking assets accessibility...${NC}"
if [ -f "public/assets/css/app.css" ]; then
    echo -e "${GREEN}✓ public/assets/css/app.css exists${NC}"
    ls -lh public/assets/css/app.css
else
    echo -e "${RED}✗ public/assets/css/app.css NOT FOUND!${NC}"
fi

if [ -f "public/assets/js/app.js" ]; then
    echo -e "${GREEN}✓ public/assets/js/app.js exists${NC}"
    ls -lh public/assets/js/app.js
else
    echo -e "${RED}✗ public/assets/js/app.js NOT FOUND!${NC}"
fi

echo ""
echo -e "${YELLOW}[TIPS]${NC}"
echo "1. Restart web server: sudo systemctl restart apache2"
echo "2. Clear browser cache: Ctrl+Shift+R"
echo "3. Check file exists: ls -la public/assets/"
echo "4. Test URL directly: curl http://localhost:8000/assets/css/app.css"
echo ""