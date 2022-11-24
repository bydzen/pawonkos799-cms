#!bin/bash

echo "Working..."
sleep 3

# Change user as root!
if [ "$EUID" -ne 0 ]
  then echo "Please run as root"
  exit
fi

echo "Exporting database..."
mysqldump -p -u root pawonkos799 > pawonkos799.sql
echo "Done exporting."
sleep 3

echo

echo "Compressing srv file..."
tar --exclude='backup-file' -vcf pawonkos799.tar /srv/www/pawonkos799/
echo "Done compressing."
sleep 3
