#!/bin/bash

#======================================
# MENÚ PRINCIPAL
#======================================

menu(){
    clear
    echo "========================================"
    echo "    GESTOR DE LOGS DEL SISTEMA (SO)    "
    echo "========================================"
    echo "1) Ver logs del sistema en vivo (journalctl)"
    echo "2) Filtrar errores críticos (ERROR / FAIL)"
    echo "3) Generar respaldo comprimido (.tar.gz)"
    echo "4) Listar respaldos existentes"
    echo "5) Limpiar backups mayores a 7 días"
    echo "6) Ver estado del servicio systemd-journald"
    echo "7) Salir"
    echo "========================================"

    read -p "Seleccione una opción: " opcion

    case $opcion in
        1) ver_logs_vivo ;;
        2) filtrar_errores ;;
        3) respaldar_logs ;;
        4) listar_respaldos ;;
        5) limpiar_respaldos ;;
        6) estado_servicio ;;
        7) salir ;;
        *)
            echo "Opción inválida."
            sleep 2
            ;;
    esac
}

#======================================
# FUNCIONES DE GESTIÓN DE LOGS
#======================================

ver_logs_vivo(){
    clear
    echo "--- Mostrando las últimas 30 líneas de journalctl ---"
    sudo journalctl -n 30 --no-pager

    echo
    read -p "Presione [Enter] para continuar..."
}

filtrar_errores(){
    clear
    echo "--- Buscando errores críticos ---"

    if [ -f /var/log/messages ]; then
        sudo grep -Ei "error|fail|critical" /var/log/messages | tail -n 25
    else
        sudo journalctl -p 0..3 -n 25 --no-pager
    fi

    echo
    read -p "Presione [Enter] para continuar..."
}

respaldar_logs(){
    DIR_BACKUP="/var/log/backups_sistema"
    FECHA=$(date +%Y%m%d_%H%M%S)
    ARCHIVO_BACKUP="$DIR_BACKUP/log_backup_$FECHA.tar.gz"

    sudo mkdir -p "$DIR_BACKUP"

    echo "Creando respaldo comprimido de /var/log..."

    sudo tar -czf "$ARCHIVO_BACKUP" \
        --exclude="$DIR_BACKUP" \
        /var/log 2>/dev/null

    if [ -f "$ARCHIVO_BACKUP" ]; then
        echo "Respaldo creado con éxito en:"
        echo "$ARCHIVO_BACKUP"
    else
        echo "Error al crear el respaldo."
    fi

    echo
    sleep 3
}

listar_respaldos(){
    DIR_BACKUP="/var/log/backups_sistema"

    clear
    echo "--- Archivos de respaldo almacenados ---"

    if [ -d "$DIR_BACKUP" ] && [ "$(ls -A "$DIR_BACKUP" 2>/dev/null)" ]; then
        ls -lh "$DIR_BACKUP"
    else
        echo "No hay respaldos almacenados en $DIR_BACKUP."
    fi

    echo
    read -p "Presione [Enter] para continuar..."
}

limpiar_respaldos(){
    DIR_BACKUP="/var/log/backups_sistema"

    clear
    echo "Eliminando respaldos con más de 7 días de antigüedad..."

    if [ -d "$DIR_BACKUP" ]; then
        sudo find "$DIR_BACKUP" \
            -type f \
            -name "*.tar.gz" \
            -mtime +7 \
            -exec rm -f {} \;

        echo "Limpieza completada."
    else
        echo "El directorio de respaldos no existe."
    fi

    echo
    sleep 3
}

estado_servicio(){
    clear
    echo "--- Estado del servicio systemd-journald ---"
    systemctl status systemd-journald --no-pager

    echo
    read -p "Presione [Enter] para continuar..."
}

#======================================
# SALIR
#======================================

salir(){
    echo "Saliendo del programa..."
    sleep 2
    exit 0
}

#======================================
# INICIO DEL PROGRAMA
#======================================

while true
do
    menu
done
