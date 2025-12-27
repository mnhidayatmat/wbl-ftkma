#!/bin/bash
echo "==================================="
echo "PHP Upload Configuration Check"
echo "==================================="
echo ""
php -r "echo 'upload_max_filesize: ' . ini_get('upload_max_filesize') . PHP_EOL;"
php -r "echo 'post_max_size: ' . ini_get('post_max_size') . PHP_EOL;"
echo ""
UPLOAD=$(php -r "echo ini_get('upload_max_filesize');")
if [[ "$UPLOAD" == *"15M"* ]] || [[ "$UPLOAD" == *"20M"* ]]; then
    echo "✅ Upload settings are CORRECT!"
else
    echo "❌ Upload settings are still WRONG (showing $UPLOAD)"
    echo "Please edit /opt/homebrew/etc/php/8.3/php.ini"
fi
