#!/bin/bash
# respaldo.sh
# Script para volcar la base de datos a un archivo SQL de respaldo

mysqldump -h db -u root -pexample ascend > backups/ascend_$(date +%F).sql
