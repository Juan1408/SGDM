#!/bin/bash

#======================================
# CONFIGURACIÓN
#======================================

LOG_PROCESOS="/var/log/ascend_procesos.log"

#======================================
# REGISTRO DE PROCESOS
#======================================

registrar_procesos(){
    clear

    echo "========================================"
    echo "       REGISTRO DE PROCESOS             "
    echo "========================================"

    FECHA=$(date '+%Y-%m-%d %H:%M:%S')

    echo "========================================" >> "$LOG_PROCESOS"
    echo "Fecha y hora: $FECHA" >> "$LOG_PROCESOS"
    echo "Usuario: $(whoami)" >> "$LOG_PROCESOS"
    echo "Procesos principales:" >> "$LOG_PROCESOS"

    ps -eo pid,user,%cpu,%mem,comm --sort=-%cpu | head -n 11 >> "$LOG_PROCESOS"

    echo "Registro guardado correctamente."
    echo
    echo "Archivo:"
    echo "$LOG_PROCESOS"

    sleep 3
    menu
}

#======================================
# VER LOG DE PROCESOS
#======================================

ver_log_procesos(){
    clear

    echo "========================================"
    echo "          LOG DE PROCESOS               "
    echo "========================================"

    if [ -f "$LOG_PROCESOS" ]; then
        cat "$LOG_PROCESOS"
    else
        echo "No existen registros de procesos."
    fi

    echo
    read -p "Presione [Enter] para continuar..."
    menu
}

#======================================
# MOSTRAR PROCESOS
#======================================

mostrar_procesos(){
    clear

    echo "========================================"
    echo "          PROCESOS DEL SISTEMA          "
    echo "========================================"

    ps aux

    echo
    read -p "Presione [Enter] para continuar..."
    menu
}

#======================================
# TERMINAR PROCESO
#======================================

terminar_proceso(){
    clear

    echo "========================================"
    echo "          TERMINAR PROCESO              "
    echo "========================================"

    read -p "Ingrese el PID del proceso: " PID

    if ps -p "$PID" > /dev/null 2>&1; then

        echo "Proceso encontrado:"
        ps -p "$PID" -o pid,user,%cpu,%mem,comm

        echo
        read -p "¿Desea terminar este proceso? (s/n): " confirmacion

        if [ "$confirmacion" = "s" ] || [ "$confirmacion" = "S" ]; then

            kill -9 "$PID"

            if [ $? -eq 0 ]; then
                echo "Proceso terminado correctamente."

                FECHA=$(date '+%Y-%m-%d %H:%M:%S')

                echo "$FECHA | Usuario: $(whoami) | Acción: TERMINAR PROCESO | PID: $PID | Resultado: EXITOSA" >> "$LOG_PROCESOS"
            else
                echo "No se pudo terminar el proceso."

                FECHA=$(date '+%Y-%m-%d %H:%M:%S')

                echo "$FECHA | Usuario: $(whoami) | Acción: TERMINAR PROCESO | PID: $PID | Resultado: ERROR" >> "$LOG_PROCESOS"
            fi

        else
            echo "Operación cancelada."

            FECHA=$(date '+%Y-%m-%d %H:%M:%S')

            echo "$FECHA | Usuario: $(whoami) | Acción: TERMINAR PROCESO | PID: $PID | Resultado: CANCELADA" >> "$LOG_PROCESOS"
        fi

    else
        echo "No existe un proceso con el PID indicado."

        FECHA=$(date '+%Y-%m-%d %H:%M:%S')

        echo "$FECHA | Usuario: $(whoami) | Acción: TERMINAR PROCESO | PID: $PID | Resultado: PID INEXISTENTE" >> "$LOG_PROCESOS"
    fi

    sleep 3
    menu
}

#======================================
# MOSTRAR MEMORIA
#======================================

mostrar_memoria(){
    clear

    echo "========================================"
    echo "          USO DE MEMORIA                "
    echo "========================================"

    free -h

    echo
    read -p "Presione [Enter] para continuar..."
    menu
}

#======================================
# MOSTRAR DISCO
#======================================

mostrar_disco(){
    clear

    echo "========================================"
    echo "          USO DE DISCO                  "
    echo "========================================"

    df -h

    echo
    read -p "Presione [Enter] para continuar..."
    menu
}

#======================================
# LIMPIAR CACHE
#======================================

limpiar_cache(){
    clear

    echo "========================================"
    echo "          LIMPIAR CACHE                 "
    echo "========================================"

    echo "Memoria antes de limpiar:"
    free -h

    echo
    echo "Limpiando cache..."

    sync
    echo 3 > /proc/sys/vm/drop_caches

    echo
    echo "Memoria después de limpiar:"
    free -h

    echo
    read -p "Presione [Enter] para continuar..."
    menu
}

#======================================
# USUARIOS CONECTADOS
#======================================

usuarios_conectados(){
    clear

    echo "========================================"
    echo "          USUARIOS CONECTADOS            "
    echo "========================================"

    who

    echo
    read -p "Presione [Enter] para continuar..."
    menu
}

#======================================
# INFORMACIÓN DEL SISTEMA
#======================================

informacion_sistema(){
    clear

    echo "========================================"
    echo "        INFORMACIÓN DEL SISTEMA         "
    echo "========================================"

    echo "Hostname:"
    hostname

    echo
    echo "Kernel:"
    uname -r

    echo
    echo "Tiempo encendido:"
    uptime

    echo
    read -p "Presione [Enter] para continuar..."
    menu
}

#======================================
# MENÚ PRINCIPAL
#======================================

menu(){
    clear

    echo "========================================"
    echo "       GESTIÓN DE PROCESOS              "
    echo "========================================"
    echo "1) Mostrar procesos"
    echo "2) Terminar proceso"
    echo "3) Mostrar uso de memoria"
    echo "4) Mostrar uso de disco"
    echo "5) Limpiar cache"
    echo "6) Mostrar usuarios conectados"
    echo "7) Información del sistema"
    echo "8) Registrar procesos en log"
    echo "9) Ver log de procesos"
    echo "0) Salir"
    echo "========================================"

    read -p "Seleccione una opción: " opcion

    case $opcion in
        1) mostrar_procesos ;;
        2) terminar_proceso ;;
        3) mostrar_memoria ;;
        4) mostrar_disco ;;
        5) limpiar_cache ;;
        6) usuarios_conectados ;;
        7) informacion_sistema ;;
        8) registrar_procesos ;;
        9) ver_log_procesos ;;
        0) salir ;;
        *)
            echo "Opción inválida."
            sleep 2
            menu
            ;;
    esac
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

touch "$LOG_PROCESOS"

menu
