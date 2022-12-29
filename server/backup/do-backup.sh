#!/bin/bash

echo "Working..."
sleep 3

# Change user as root!
if ! [ $(id -u) = 0 ]; then
    printf "Please run as root!\n"
    exit 1
fi

while true; do
    read -p "Ready to backup, continue? (y/n): " yn
    case $yn in
    [Yy]*)
        break
        ;;
    [Nn]*)
        printf "\nExiting tool.\n"
        exit
        ;;
    *) printf "Please answer 'Y/y' or 'N/n'.\n" ;;
    esac
done

echo "Exporting database..."
mysqldump -p -u root pawonkos799 > pawonkos799.sql
echo "Done exporting."
sleep 3

echo

echo "Compressing srv file..."
tar --exclude='backup-file' -cf pawonkos799.tar /srv/www/pawonkos799/*
echo "Done compressing."
sleep 3
