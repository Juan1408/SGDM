#!/bin/bash

# ==========================================
# CIFRADO Y DESCIFRADO DE TEXTOS AES
# ==========================================

LOG="/var/log/seguridad.log"

# ==========================================
# MENÚ PRINCIPAL
# ==========================================

menu(){
    clear
    echo "========================================"
    echo "       CIFRADOR DE TEXTOS AES"
    echo "========================================"
    echo "1) Cifrar texto (AES-128)"
    echo "2) Descifrar texto (AES-128)"
    echo "3) Cifrar texto (AES-192)"
    echo "4) Descifrar texto (AES-192)"
    echo "5) Cifrar texto (AES-256)"
    echo "6) Descifrar texto (AES-256)"
    echo "7) Salir"
    echo "========================================"

    read -p "Seleccione una opción: " opcion

    case $opcion in
        1) cifrado128 ;;
        2) descifrado128 ;;
        3) cifrado192 ;;
        4) descifrado192 ;;
        5) cifrado256 ;;
        6) descifrado256 ;;
        7) salir ;;
        *)
            echo "Opción inválida."
            sleep 2
            ;;
    esac
}

# ==========================================
# FUNCIÓN PARA REGISTRAR OPERACIONES
# ==========================================

registrar_log(){
    echo "$(date '+%d/%m/%Y %H:%M:%S') | Usuario: $USER | Acción: $1 | Archivo: $2 | Algoritmo: $3 | Resultado: $4" >> "$LOG"
}

# ==========================================
# FUNCIONES DE CIFRADO
# ==========================================

cifrado128(){
    read -p "Ingrese el texto a cifrar: " texto
    read -s -p "Ingrese contraseña: " clave
    echo

    echo -n "$texto" | \
    openssl enc -aes-128-cbc -a -salt -pbkdf2 -pass pass:"$clave" > textocifrado128

    if [ $? -eq 0 ]; then
        echo "Texto cifrado correctamente."
        registrar_log "Cifrado" "textocifrado128" "AES-128" "EXITOSO"
    else
        echo "Error al cifrar."
        registrar_log "Cifrado" "textocifrado128" "AES-128" "ERROR"
    fi

    unset clave
    sleep 3
}

cifrado192(){
    read -p "Ingrese el texto a cifrar: " texto
    read -s -p "Ingrese contraseña: " clave
    echo

    echo -n "$texto" | \
    openssl enc -aes-192-cbc -a -salt -pbkdf2 -pass pass:"$clave" > textocifrado192

    if [ $? -eq 0 ]; then
        echo "Texto cifrado correctamente."
        registrar_log "Cifrado" "textocifrado192" "AES-192" "EXITOSO"
    else
        echo "Error al cifrar."
        registrar_log "Cifrado" "textocifrado192" "AES-192" "ERROR"
    fi

    unset clave
    sleep 3
}

cifrado256(){
    read -p "Ingrese el texto a cifrar: " texto
    read -s -p "Ingrese contraseña: " clave
    echo

    echo -n "$texto" | \
    openssl enc -aes-256-cbc -a -salt -pbkdf2 -pass pass:"$clave" > textocifrado256

    if [ $? -eq 0 ]; then
        echo "Texto cifrado correctamente."
        registrar_log "Cifrado" "textocifrado256" "AES-256" "EXITOSO"
    else
        echo "Error al cifrar."
        registrar_log "Cifrado" "textocifrado256" "AES-256" "ERROR"
    fi

    unset clave
    sleep 3
}

# ==========================================
# FUNCIONES DE DESCIFRADO
# ==========================================

descifrado128(){
    read -s -p "Ingrese la contraseña: " clave
    echo

    openssl enc -d -aes-128-cbc -a -pbkdf2 -pass pass:"$clave" -in textocifrado128

    if [ $? -eq 0 ]; then
        echo "Texto descifrado correctamente."
        registrar_log "Descifrado" "textocifrado128" "AES-128" "EXITOSO"
    else
        echo "Error: clave incorrecta o archivo inválido."
        registrar_log "Descifrado" "textocifrado128" "AES-128" "ERROR"
    fi

    unset clave
    sleep 3
}

descifrado192(){
    read -s -p "Ingrese la contraseña: " clave
    echo

    openssl enc -d -aes-192-cbc -a -pbkdf2 -pass pass:"$clave" -in textocifrado192

    if [ $? -eq 0 ]; then
        echo "Texto descifrado correctamente."
        registrar_log "Descifrado" "textocifrado192" "AES-192" "EXITOSO"
    else
        echo "Error: clave incorrecta o archivo inválido."
        registrar_log "Descifrado" "textocifrado192" "AES-192" "ERROR"
    fi

    unset clave
    sleep 3
}

descifrado256(){
    read -s -p "Ingrese la contraseña: " clave
    echo

    openssl enc -d -aes-256-cbc -a -pbkdf2 -pass pass:"$clave" -in textocifrado256

    if [ $? -eq 0 ]; then
        echo "Texto descifrado correctamente."
        registrar_log "Descifrado" "textocifrado256" "AES-256" "EXITOSO"
    else
        echo "Error: clave incorrecta o archivo inválido."
        registrar_log "Descifrado" "textocifrado256" "AES-256" "ERROR"
    fi

    unset clave
    sleep 3
}

# ==========================================
# SALIR
# ==========================================

salir(){
    echo "Saliendo del programa..."
    exit 0
}

# ==========================================
# INICIO DEL PROGRAMA
# ==========================================

while true
do
    menu
done
