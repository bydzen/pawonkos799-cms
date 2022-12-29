#!/bin/bash

echo "Pawonkos799 Backup Tool"
sleep 1
echo "Working..."
sleep 1

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
echo "Exec: mysqldump -u root -p pawonkos799 > backup-pawonkos799.sql"
mysqldump -u root -p pawonkos799 > backup-pawonkos799.sql
echo "Status code $?: Done exporting."
sleep 1

echo

echo "Compressing file..."
echo "Exec: tar -cf backup-pawonkos799.tar pawonkos799"
tar -cf backup-pawonkos799.tar pawonkos799
echo "Status code $?: Done compressing."
sleep 1

echo

echo "All done."
