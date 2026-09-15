#!/bin/bash

#======================================
# MENÚ PRINCIPAL
#======================================

menu(){
    clear

    echo "========================================"
    echo "       GESTOR DE SERVICIOS LINUX        "
    echo "========================================"
    echo "1) Ver estado de un servicio"
    echo "2) Iniciar un servicio"
    echo "3) Detener un servicio"
    echo "4) Reiniciar un servicio"
    echo "5) Habilitar servicio al iniciar Linux"
    echo "6) Deshabilitar servicio al iniciar Linux"
    echo "7) Listar servicios activos"
    echo "8) Ver logs de un servicio"
    echo "9) Salir"
    echo "========================================"

    read -p "Seleccione una opción: " opcion

    case $opcion in
        1) estado_servicio ;;
        2) iniciar_servicio ;;
        3) detener_servicio ;;
        4) reiniciar_servicio ;;
        5) habilitar_servicio ;;
        6) deshabilitar_servicio ;;
        7) listar_servicios ;;
        8) logs_servicio ;;
        9) salir ;;
        *)
            echo "Opción inválida."
            sleep 2
            menu
            ;;
    esac
}

#======================================
# VER ESTADO
#======================================

estado_servicio(){
    clear

    read -p "Ingrese el nombre del servicio: " servicio

    echo
    echo "--- Estado del servicio $servicio ---"

    systemctl status "$servicio" --no-pager

    echo
    read -p "Presione [Enter] para continuar..."
    menu
}

#======================================
# INICIAR SERVICIO
#======================================

iniciar_servicio(){
    clear

    read -p "Ingrese el nombre del servicio: " servicio

    echo
    echo "Iniciando servicio $servicio..."

    sudo systemctl start "$servicio"

    if [ $? -eq 0 ]; then
        echo "Servicio iniciado correctamente."
    else
        echo "No se pudo iniciar el servicio."
    fi

    sleep 3
    menu
}

#======================================
# DETENER SERVICIO
#======================================

detener_servicio(){
    clear

    read -p "Ingrese el nombre del servicio: " servicio

    echo
    echo "Deteniendo servicio $servicio..."

    sudo systemctl stop "$servicio"

    if [ $? -eq 0 ]; then
        echo "Servicio detenido correctamente."
    else
        echo "No se pudo detener el servicio."
    fi

    sleep 3
    menu
}

#======================================
# REINICIAR SERVICIO
#======================================

reiniciar_servicio(){
    clear

    read -p "Ingrese el nombre del servicio: " servicio

    echo
    echo "Reiniciando servicio $servicio..."

    sudo systemctl restart "$servicio"

    if [ $? -eq 0 ]; then
        echo "Servicio reiniciado correctamente."
    else
        echo "No se pudo reiniciar el servicio."
    fi

    sleep 3
    menu
}

#======================================
# HABILITAR SERVICIO
#======================================

habilitar_servicio(){
    clear

    read -p "Ingrese el nombre del servicio: " servicio

    echo
    echo "Habilitando $servicio para el inicio de Linux..."

    sudo systemctl enable "$servicio"

    if [ $? -eq 0 ]; then
        echo "Servicio habilitado correctamente."
    else
        echo "No se pudo habilitar el servicio."
    fi

    sleep 3
    menu
}

#======================================
# DESHABILITAR SERVICIO
#======================================

deshabilitar_servicio(){
    clear

    read -p "Ingrese el nombre del servicio: " servicio

    echo
    echo "Deshabilitando $servicio del inicio de Linux..."

    sudo systemctl disable "$servicio"

    if [ $? -eq 0 ]; then
        echo "Servicio deshabilitado correctamente."
    else
        echo "No se pudo deshabilitar el servicio."
    fi

    sleep 3
    menu
}

#======================================
# LISTAR SERVICIOS ACTIVOS
#======================================

listar_servicios(){
    clear

    echo "========================================"
    echo "        SERVICIOS ACTIVOS               "
    echo "========================================"

    systemctl list-units --type=service --state=running --no-pager

    echo
    read -p "Presione [Enter] para continuar..."
    menu
}

#======================================
# LOGS DE SERVICIO
#======================================

logs_servicio(){
    clear

    read -p "Ingrese el nombre del servicio: " servicio

    echo
    echo "--- Logs del servicio $servicio ---"

    journalctl -u "$servicio" -n 30 --no-pager

    echo
    read -p "Presione [Enter] para continuar..."
    menu
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