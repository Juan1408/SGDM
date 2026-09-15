#!/bin/bash

# ==========================================
# HASHING DE TEXTOS Y ARCHIVOS
# ==========================================

LOG="/var/log/seguridad.log"

# ==========================================
# MENÚ PRINCIPAL
# ==========================================

menu(){
    clear
    echo "=========================================="
    echo "        HASHING DE TEXTOS Y ARCHIVOS"
    echo "=========================================="
    echo "1) Generar hash"
    echo "2) Verificar hash"
    echo "3) Ver historial"
    echo "4) Salir"
    echo "=========================================="

    read -p "Seleccione una opción: " op

    case $op in
        1) generar_hash ;;
        2) verificar_hash ;;
        3) ver_log ;;
        4) salir ;;
        *)
            echo "Opción inválida."
            sleep 2
            ;;
    esac
}

# ==========================================
# SELECCIONAR ALGORITMO
# ==========================================

seleccionar_algoritmo(){

    clear

    echo "=========================================="
    echo "       SELECCIONAR ALGORITMO"
    echo "=========================================="
    echo "1) MD5"
    echo "2) SHA-256"
    echo "3) SHA-512"
    echo "=========================================="

    read -p "Seleccione una opción: " op

    case $op in
        1)
            algoritmo="md5sum"
            nombre_algoritmo="MD5"
            ;;
        2)
            algoritmo="sha256sum"
            nombre_algoritmo="SHA-256"
            ;;
        3)
            algoritmo="sha512sum"
            nombre_algoritmo="SHA-512"
            ;;
        *)
            echo "Opción inválida."
            sleep 2
            return 1
            ;;
    esac

    return 0
}

# ==========================================
# GENERAR HASH
# ==========================================

generar_hash(){

    clear

    echo "=========================================="
    echo "           GENERAR HASH"
    echo "=========================================="
    echo "1) Hash de un texto"
    echo "2) Hash de un archivo"
    echo "=========================================="

    read -p "Seleccione una opción: " tipo

    case $tipo in

        # ----------------------------------
        # HASH DE TEXTO
        # ----------------------------------

        1)

            read -p "Ingrese el texto: " texto

            seleccionar_algoritmo

            if [ $? -ne 0 ]; then
                sleep 2
                return
            fi

            hash=$(echo -n "$texto" | $algoritmo | awk '{print $1}')

            echo ""
            echo "=========================================="
            echo "Hash generado correctamente"
            echo "=========================================="
            echo "Algoritmo: $nombre_algoritmo"
            echo "Hash: $hash"
            echo "=========================================="

            registrar_log \
                "Hash de texto" \
                "Texto ingresado por usuario" \
                "$nombre_algoritmo" \
                "EXITOSO"

            ;;

        # ----------------------------------
        # HASH DE ARCHIVO
        # ----------------------------------

        2)

            read -p "Ingrese el nombre del archivo: " archivo

            if [ ! -f "$archivo" ]; then
                echo "El archivo no existe."

                registrar_log \
                    "Generación de hash" \
                    "$archivo" \
                    "N/A" \
                    "ERROR: ARCHIVO NO EXISTE"

                sleep 3
                return
            fi

            seleccionar_algoritmo

            if [ $? -ne 0 ]; then
                sleep 2
                return
            fi

            hash=$($algoritmo "$archivo" | awk '{print $1}')

            archivo_hash="$archivo.$(echo "$nombre_algoritmo" | tr '[:upper:]' '[:lower:]' | tr -d '-')".hash

            echo "$nombre_algoritmo:$hash" > "$archivo_hash"

            echo ""
            echo "=========================================="
            echo "Hash generado correctamente"
            echo "=========================================="
            echo "Archivo: $archivo"
            echo "Algoritmo: $nombre_algoritmo"
            echo "Hash: $hash"
            echo "Guardado en: $archivo_hash"
            echo "=========================================="

            registrar_log \
                "Generación de hash" \
                "$archivo" \
                "$nombre_algoritmo" \
                "EXITOSO"

            ;;

        *)

            echo "Opción inválida."
            ;;

    esac

    sleep 4
}

# ==========================================
# VERIFICAR HASH
# ==========================================

verificar_hash(){

    clear

    echo "=========================================="
    echo "         VERIFICAR HASH"
    echo "=========================================="

    read -p "Ingrese el nombre del archivo: " archivo

    if [ ! -f "$archivo" ]; then

        echo "El archivo no existe."

        registrar_log \
            "Verificación de integridad" \
            "$archivo" \
            "N/A" \
            "ERROR: ARCHIVO NO EXISTE"

        sleep 3
        return

    fi

    echo ""
    echo "Seleccione el algoritmo utilizado para generar el hash:"
    echo "1) MD5"
    echo "2) SHA-256"
    echo "3) SHA-512"

    read -p "Seleccione una opción: " op

    case $op in

        1)
            algoritmo="md5sum"
            nombre_algoritmo="MD5"
            archivo_hash="$archivo.md5.hash"
            ;;

        2)
            algoritmo="sha256sum"
            nombre_algoritmo="SHA-256"
            archivo_hash="$archivo.sha256.hash"
            ;;

        3)
            algoritmo="sha512sum"
            nombre_algoritmo="SHA-512"
            archivo_hash="$archivo.sha512.hash"
            ;;

        *)
            echo "Opción inválida."
            sleep 2
            return
            ;;

    esac

    if [ ! -f "$archivo_hash" ]; then

        echo ""
        echo "No existe un hash de referencia para este algoritmo."
        echo "Primero debe generar el hash utilizando $nombre_algoritmo."

        registrar_log \
            "Verificación de integridad" \
            "$archivo" \
            "$nombre_algoritmo" \
            "ERROR: HASH DE REFERENCIA NO EXISTE"

        sleep 4
        return

    fi

    hash_guardado=$(cut -d ':' -f 2 "$archivo_hash")
    hash_actual=$($algoritmo "$archivo" | awk '{print $1}')

    echo ""

    if [ "$hash_actual" = "$hash_guardado" ]; then

        echo "=========================================="
        echo "INTEGRIDAD VERIFICADA"
        echo "=========================================="
        echo "Archivo: $archivo"
        echo "Algoritmo: $nombre_algoritmo"
        echo "El archivo no presenta modificaciones."
        echo "=========================================="

        registrar_log \
            "Verificación de integridad" \
            "$archivo" \
            "$nombre_algoritmo" \
            "OK"

    else

        echo "=========================================="
        echo "ALERTA DE SEGURIDAD"
        echo "=========================================="
        echo "Archivo: $archivo"
        echo "Algoritmo: $nombre_algoritmo"
        echo "El archivo fue modificado."
        echo "=========================================="

        registrar_log \
            "Verificación de integridad" \
            "$archivo" \
            "$nombre_algoritmo" \
            "MODIFICADO"

    fi

    sleep 4
}

# ==========================================
# REGISTRAR OPERACIONES
# ==========================================

registrar_log(){

    echo "$(date '+%d/%m/%Y %H:%M:%S') | Usuario: $USER | Acción: $1 | Archivo: $2 | Algoritmo: $3 | Resultado: $4" >> "$LOG"

}

# ==========================================
# VER LOG
# ==========================================

ver_log(){

    clear

    echo "=========================================="
    echo "             HISTORIAL"
    echo "=========================================="
    echo ""

    if [ -f "$LOG" ]; then
        cat "$LOG"
    else
        echo "Todavía no existen registros."
    fi

    echo ""
    read -p "Presione ENTER para continuar..."
}

# ==========================================
# SALIR
# ==========================================

salir(){

    clear

    echo "=========================================="
    echo "          SALIENDO DEL PROGRAMA"
    echo "=========================================="

    sleep 2

    exit 0
}

# ==========================================
# INICIO DEL PROGRAMA
# ==========================================

while true
do
    menu
done
