#!/bin/bash

echo "Pawonkos799 Backup Tool"
sleep 1
echo "Working..."
sleep 1

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

echo

echo "Exporting database..."
mysqldump -u root -p pawonkos799 > backup-pawonkos799.sql
echo "Done exporting."
sleep 1

echo

echo "Compressing srv file..."
tar -cf backup-pawonkos799.tar pawonkos799
echo "Done compressing."
sleep 1

echo

while true; do
    read -p "Move all to public directory? (y/n): " yn
    case $yn in
    [Yy]*)
        mv backup-pawonkos799.* pawonkos799/other/backup/
        echo "File moved to pawonkos799/other/backup/---"
        ;;
    [Nn]*)
        printf "File not move. Done.\n"
        exit
        ;;
    *) printf "Please answer 'Y/y' or 'N/n'.\n" ;;
    esac
done
