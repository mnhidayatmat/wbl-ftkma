#!/bin/bash
echo "Checking PHP server configuration..."
echo ""
curl -s http://127.0.0.1:8000/phpinfo-check.php | grep -E "(upload_max_filesize|post_max_size|Configuration)" | sed 's/<[^>]*>//g'
