#!/bin/sh

set -e # Exit script immediately on first error.
set -x # Print commands and their arguments as they are executed.

sudo a2ensite blueridgeapp.conf

cd /etc/ssl/crt/
openssl genrsa -out dev.blueridgeapp.com.key 2048
openssl req -new -x509 -key dev.blueridgeapp.com.key -out dev.blueridgeapp.com.cert -days 3650 -subj /CN=dev.blueridgeapp.com
# Start Apache
sudo service apache2 start

#init project 
cd /var/www/blueridgeapp
sudo composer update

# Start Apache
#sudo service mongodb start
