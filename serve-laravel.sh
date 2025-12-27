#!/bin/bash

# Laravel Development Server with Custom PHP Settings
# This script runs the Laravel server with increased file upload limits

php -c php-custom.ini artisan serve --host=127.0.0.1 --port=8000
