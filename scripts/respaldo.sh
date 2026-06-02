#!/bin/bash
# respaldo.sh
# Script para volcar la base de datos a un archivo SQL de respaldo

mysqldump -h db -u root -pexample sgdm > backups/sgdm_$(date +%F).sql
