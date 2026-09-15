#!/bin/bash

#======================================
# CONFIGURACIÓN
#======================================

LOG_AUDITORIA="/var/log/ascend_usuarios.log"

#======================================
# VERIFICAR USUARIO ROOT
#======================================

if [ "$EUID" -ne 0 ]; then
    echo "========================================"
    echo " Este script debe ejecutarse como ROOT"
    echo "========================================"
    echo
    echo "Ejecute:"
    echo "sudo ./usuarios_grupos.sh"
    exit 1
fi

#======================================
# REGISTRO DE AUDITORÍA
#======================================

registrar_log(){
    FECHA=$(date '+%Y-%m-%d %H:%M:%S')
    USUARIO=$(whoami)

    echo "$FECHA | Usuario: $USUARIO | Acción: $1 | Objetivo: $2 | Resultado: $3" >> "$LOG_AUDITORIA"
}

#======================================
# MENÚ PRINCIPAL
#======================================

menu(){
    clear
    echo "========================================"
    echo "       GESTIÓN DE USUARIOS Y GRUPOS    "
    echo "========================================"
    echo "1) Alta grupo"
    echo "2) Listar grupos"
    echo "3) Alta usuario"
    echo "4) Listar usuarios"
    echo "5) Eliminar grupo"
    echo "6) Eliminar usuario"
    echo "7) Ver log de auditoría"
    echo "8) Salir"
    echo "========================================"

    read -p "Seleccione una opción: " op

    case $op in
        1) alta_grupo ;;
        2) lista_grupo ;;
        3) alta_usuario ;;
        4) listar_usuario ;;
        5) eliminar_grupo ;;
        6) eliminar_usuario ;;
        7) ver_auditoria ;;
        8) salir ;;
        *)
            echo "Opción inválida."
            sleep 2
            menu
            ;;
    esac
}

#======================================
# ALTA DE GRUPO
#======================================

alta_grupo(){
    clear

    echo "========================================"
    echo "             ALTA DE GRUPO              "
    echo "========================================"

    read -p "Ingrese el nombre del grupo: " grp

    if [ -z "$grp" ]; then
        echo "El nombre del grupo no puede estar vacío."
        registrar_log "ALTA GRUPO" "Sin nombre" "ERROR"
    elif getent group "$grp" > /dev/null; then
        echo "El grupo ya existe."
        registrar_log "ALTA GRUPO" "$grp" "ERROR - YA EXISTE"
    else
        groupadd "$grp"

        if [ $? -eq 0 ]; then
            echo "Grupo creado correctamente."
            registrar_log "ALTA GRUPO" "$grp" "EXITOSA"
        else
            echo "Error al crear el grupo."
            registrar_log "ALTA GRUPO" "$grp" "ERROR"
        fi
    fi

    sleep 3
    menu
}

#======================================
# LISTAR GRUPOS
#======================================

lista_grupo(){
    clear

    echo "========================================"
    echo "             GRUPOS DEL SISTEMA         "
    echo "========================================"

    cut -d: -f1 /etc/group

    registrar_log "LISTAR GRUPOS" "Todos los grupos" "EXITOSA"

    echo
    read -p "Presione [Enter] para continuar..."
    menu
}

#======================================
# ALTA DE USUARIO
#======================================

alta_usuario(){
    clear

    echo "========================================"
    echo "             ALTA DE USUARIO             "
    echo "========================================"

    read -p "Ingrese el nombre del usuario: " username

    if [ -z "$username" ]; then
        echo "El nombre de usuario no puede estar vacío."
        registrar_log "ALTA USUARIO" "Sin nombre" "ERROR"
        sleep 3
        menu
        return
    fi

    if id "$username" > /dev/null 2>&1; then
        echo "El usuario ya existe."
        registrar_log "ALTA USUARIO" "$username" "ERROR - YA EXISTE"
        sleep 3
        menu
        return
    fi

    read -s -p "Ingrese contraseña: " pass
    echo

    read -p "Ingrese el grupo: " grp

    if ! getent group "$grp" > /dev/null; then
        echo "El grupo indicado no existe."
        registrar_log "ALTA USUARIO" "$username" "ERROR - GRUPO INEXISTENTE"
        sleep 3
        menu
        return
    fi

    useradd -m -d "/home/$username" -s /bin/bash -g "$grp" "$username"

    if [ $? -eq 0 ]; then
        echo "$username:$pass" | chpasswd

        if [ $? -eq 0 ]; then
            echo "Usuario $username creado correctamente."
            registrar_log "ALTA USUARIO" "$username | Grupo: $grp" "EXITOSA"
        else
            echo "Usuario creado, pero ocurrió un error al establecer la contraseña."
            registrar_log "ALTA USUARIO" "$username | Grupo: $grp" "ERROR - CONTRASEÑA"
        fi
    else
        echo "Error al crear el usuario."
        registrar_log "ALTA USUARIO" "$username | Grupo: $grp" "ERROR"
    fi

    unset pass

    sleep 4
    menu
}

#======================================
# LISTAR USUARIOS
#======================================

listar_usuario(){
    clear

    echo "========================================"
    echo "            USUARIOS DEL SISTEMA        "
    echo "========================================"

    cut -d: -f1 /etc/passwd

    registrar_log "LISTAR USUARIOS" "Todos los usuarios" "EXITOSA"

    echo
    read -p "Presione [Enter] para continuar..."
    menu
}

#======================================
# ELIMINAR GRUPO
#======================================

eliminar_grupo(){
    clear

    echo "========================================"
    echo "            ELIMINAR GRUPO              "
    echo "========================================"

    read -p "Ingrese el nombre del grupo a eliminar: " grp

    if ! getent group "$grp" > /dev/null; then
        echo "El grupo no existe."
        registrar_log "BAJA GRUPO" "$grp" "ERROR - NO EXISTE"
    else
        groupdel "$grp"

        if [ $? -eq 0 ]; then
            echo "Grupo eliminado correctamente."
            registrar_log "BAJA GRUPO" "$grp" "EXITOSA"
        else
            echo "No se pudo eliminar el grupo."
            registrar_log "BAJA GRUPO" "$grp" "ERROR"
        fi
    fi

    sleep 3
    menu
}

#======================================
# ELIMINAR USUARIO
#======================================

eliminar_usuario(){
    clear

    echo "========================================"
    echo "            ELIMINAR USUARIO            "
    echo "========================================"

    read -p "Ingrese el nombre del usuario a eliminar: " username

    if ! id "$username" > /dev/null 2>&1; then
        echo "El usuario no existe."
        registrar_log "BAJA USUARIO" "$username" "ERROR - NO EXISTE"
    else
        userdel -r "$username"

        if [ $? -eq 0 ]; then
            echo "Usuario eliminado correctamente."
            registrar_log "BAJA USUARIO" "$username" "EXITOSA"
        else
            echo "No se pudo eliminar el usuario."
            registrar_log "BAJA USUARIO" "$username" "ERROR"
        fi
    fi

    sleep 3
    menu
}

#======================================
# VER LOG DE AUDITORÍA
#======================================

ver_auditoria(){
    clear

    echo "========================================"
    echo "          LOG DE AUDITORÍA              "
    echo "========================================"

    if [ -f "$LOG_AUDITORIA" ]; then
        cat "$LOG_AUDITORIA"
    else
        echo "No existen registros de auditoría."
    fi

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

touch "$LOG_AUDITORIA"
chmod 600 "$LOG_AUDITORIA"

menu