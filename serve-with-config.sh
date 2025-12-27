#!/bin/bash
# Serve Laravel with custom PHP configuration (15MB upload limit)
php -d upload_max_filesize=15M -d post_max_size=20M -d memory_limit=256M artisan serve
