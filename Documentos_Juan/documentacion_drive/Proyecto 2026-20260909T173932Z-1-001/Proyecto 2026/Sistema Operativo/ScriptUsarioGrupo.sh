#!/bin/bash

menu(){
clear
echo "Debe ser usuario root para ejecutar este script!"
echo "1) Alta grupo"
echo "2) Listar grupo"
echo "3) Modificar grupo"
echo "4) Alta usuario"
echo "5) Listar usuario"
echo "6) Modificar usuario"
echo "7) Eliminar grupo"
echo "8) Eliminar usuario"
echo "9) Salir"
read op

case $op in
1) alta_grupo;;
2) lista_grupo;;
3) modificar_grupo;;
4) alta_usuario;;
5) listar_usuario;;
6) modificar_usuario;;
7) eliminar_grupo;;
8) eliminar_usuario;;
9) salir;;
*) echo "Opción inválida"; sleep 2; menu;;
esac
}

# =======================
# ALTA GRUPO
# =======================
alta_grupo(){
clear
echo "Alta Grupo"
echo ""

read -p "Ingrese el nombre del grupo: " grp

while [ -z "$grp" ] || [[ ! $grp =~ ^[a-z_][a-z0-9_-]{0,31}$ ]]
do
    if [ -z "$grp" ]; then
        echo "Error: el nombre del grupo no puede estar vacío."
    else
        echo "Error: nombre de grupo inválido."
        echo "Debe:"
        echo "- Empezar con una letra minúscula o _"
        echo "- Contener solo letras minúsculas, números, _ o -"
        echo "- No tener espacios ni símbolos"
        echo "- Tener máximo 32 caracteres"
    fi

    sleep 3
    clear
    echo "Alta Grupo"
    echo ""
    read -p "Ingrese el nombre del grupo: " grp
done

verif_grupo=$(cut -d: -f1 /etc/group | grep -w "$grp")

if [ -n "$verif_grupo" ]; then
    echo "Error: el grupo ya existe."
    sleep 2
    menu
fi

groupadd "$grp"

if [ $? -eq 0 ]; then
    echo "Grupo creado correctamente."
else
    echo "No fue posible crear el grupo."
fi

sleep 2
menu
}

# =======================
# LISTAR GRUPOS
# =======================
lista_grupo(){
clear
echo "Listar Grupo"
echo ""
echo "Grupos en el sistema:"
cut -d: -f1 /etc/group
sleep 4
menu
}

# =======================
# MODIFICAR GRUPO
# =======================
modificar_grupo(){
clear
echo "Modificar Grupo"
echo ""

grp=""

while [ -z "$grp" ]
do
    read -p "Ingrese el grupo a modificar: " grp

    if [ -z "$grp" ]; then
        clear
        echo "El campo no puede estar vacío."
        sleep 2
        clear
        echo "Modificar Grupo"
        echo ""
    fi
done

verif_grupo=$(cut -d: -f1 /etc/group | grep -w "$grp")

if [ -n "$verif_grupo" ]; then

    clear
    echo "Modificar Grupo"
    echo ""

    read -p "Ingrese el nuevo nombre del grupo: " newgrp

    while [ -z "$newgrp" ]
    do
        clear
        echo "El campo no puede estar vacío."
        sleep 2
        clear
        echo "Modificar Grupo"
        echo ""
        read -p "Ingrese el nuevo nombre del grupo: " newgrp
    done

    verif_grupo=$(cut -d: -f1 /etc/group | grep -w "$newgrp")

    if [ -n "$verif_grupo" ]; then
        echo "Error: el grupo ya existe."
        sleep 2
        menu
    fi

    groupmod -n "$newgrp" "$grp"

    if [ $? -eq 0 ]; then

        fecha_actual=$(date)

        echo "(MODIFICAR:GRUPO) (NUEVO GRUPO) $newgrp (ANTERIOR GRUPO) $grp (FECHA) $fecha_actual" >> Logs.txt

        echo "Grupo modificado correctamente."

    else

        echo "No fue posible modificar el grupo."

    fi

    sleep 3
    menu

else

    clear
    echo "El grupo ingresado no existe."
    sleep 3
    menu

fi
}

# =======================
# ALTA USUARIO
# =======================
alta_usuario(){

clear
echo "Alta Usuario"
echo ""

read -p "Ingrese el nombre del usuario: " username

while [ -z "$username" ] || [[ ! $username =~ ^[a-z_][a-z0-9_-]{0,31}$ ]]
do
    if [ -z "$username" ]; then
        echo "Error: el nombre de usuario no puede estar vacío."
    else
        echo "Error: nombre de usuario inválido."
        echo "Debe:"
        echo "- Empezar con una letra minúscula o _"
        echo "- Contener solo letras minúsculas, números, _ o -"
        echo "- No tener espacios ni símbolos"
        echo "- Tener máximo 32 caracteres"
    fi

    sleep 3
    clear
    echo "Alta Usuario"
    echo ""
    read -p "Ingrese el nombre del usuario: " username
done

verif_user=$(cut -d: -f1 /etc/passwd | grep -w "$username")

if [ -n "$verif_user" ]; then
    echo "Error: el usuario ya existe."
    sleep 2
    menu
fi

read -s -p "Ingrese contraseña: " pass
echo ""

while [ ${#pass} -lt 6 ]
do
    echo "Error: la contraseña debe tener al menos 6 caracteres."
    sleep 2
    read -s -p "Ingrese contraseña: " pass
    echo ""
done

read -p "Ingrese el grupo: " grp

while [ -z "$grp" ] || [[ ! $grp =~ ^[a-z_][a-z0-9_-]{0,31}$ ]]
do
    if [ -z "$grp" ]; then
        echo "Error: el grupo no puede estar vacío."
    else
        echo "Error: nombre de grupo inválido."
        echo "Use solo minúsculas, números, _ o - (máx 32 caracteres)."
    fi

    sleep 2
    read -p "Ingrese el grupo: " grp
done

verif_grupo=$(cut -d: -f1 /etc/group | grep -w "$grp")

if [ -z "$verif_grupo" ]; then
    echo "Error: el grupo no existe."
    sleep 2
    menu
fi

useradd -m -d /home/"$username" -s /bin/bash -g "$grp" "$username"
echo "$username:$pass" | chpasswd

if [ $? -eq 0 ]; then
    echo "Usuario creado correctamente."
else
    echo "No fue posible crear el usuario."
fi

sleep 3
menu
}
# =======================
# LISTAR USUARIOS
# =======================
listar_usuario(){
clear
echo "Listar Usuario"
echo ""
echo "Usuarios registrados:"
cut -d: -f1 /etc/passwd
sleep 5
menu
}

# =======================
# MODIFICAR USUARIO
# =======================
modificar_usuario(){
clear
echo "Modificar Usuario"
echo ""

user=""

while [ -z "$user" ]
do
    read -p "Ingrese usuario a modificar: " user

    if [ -z "$user" ]; then
        clear
        echo "El campo no puede estar vacío."
        sleep 2
        clear
        echo "Modificar Usuario"
        echo ""
    fi
done

verif_user=$(cut -d: -f1 /etc/passwd | grep -w "$user")

if [ -n "$verif_user" ]; then

    clear
    echo "Modificar Usuario"
    echo ""
    echo "1- Cambiar Nombre"
    echo "2- Cambiar Contraseña"
    echo ""
    echo "0- Volver atrás"
    echo ""

    read -p "Seleccione Opción: " x

    case $x in

    1)

        clear
        echo "Modificar Usuario"
        echo ""

        newUser=""

        while [ -z "$newUser" ]
        do
            read -p "Ingrese Nuevo Nombre: " newUser

            if [ -z "$newUser" ]; then
                clear
                echo "El campo no puede estar vacío."
                sleep 2
                clear
                echo "Modificar Usuario"
                echo ""
            fi
        done

        verif_user=$(cut -d: -f1 /etc/passwd | grep -w "$newUser")

        if [ -n "$verif_user" ]; then
            echo "Ese usuario ya existe."
            sleep 2
            menu
        fi

        usermod -l "$newUser" "$user"

        if [ $? -eq 0 ]; then

            fecha_actual=$(date)

            echo "(MODIFICAR:USUARIO:NOMBRE) (NUEVO USUARIO) $newUser (ANTERIOR USUARIO) $user (FECHA) $fecha_actual" >> Logs.txt

            echo "Nombre modificado correctamente."

        else

            echo "No fue posible modificar el usuario."

        fi

        sleep 3
        menu
        ;;

    2)

        clear
        echo "Modificar Usuario"
        echo ""
        echo "Ingrese Nueva Contraseña:"
        echo ""

        passwd "$user"

        if [ $? -eq 0 ]; then

            fecha_actual=$(date)

            echo "(MODIFICAR:USUARIO:CONTRASEÑA) (USUARIO) $user (FECHA) $fecha_actual" >> Logs.txt

            echo "Contraseña modificada correctamente."

        else

            echo "No fue posible modificar la contraseña."

        fi

        sleep 3
        menu
        ;;

    0)
        menu
        ;;

    *)
        clear
        echo "Opción incorrecta."
        sleep 3
        modificar_usuario
        ;;

    esac

else

    clear
    echo "El usuario ingresado no existe."
    sleep 3
    menu

fi
}

# =======================
# ELIMINAR GRUPO
# =======================
eliminar_grupo(){
clear
echo "Eliminar Grupo"
echo ""

read -p "Ingrese el nombre del grupo: " grp

while [ -z "$grp" ] || [[ ! $grp =~ ^[a-z_][a-z0-9_-]{0,31}$ ]]
do
    echo "Error: nombre inválido. Use minúsculas, números, _ o - (máx 32 caracteres)."
    sleep 2
    read -p "Ingrese el nombre del grupo: " grp
done

verif_grupo=$(cut -d: -f1 /etc/group | grep -w "$grp")

if [ -z "$verif_grupo" ]; then
    echo "Error: el grupo no existe."
    sleep 2
    menu
fi

gid=$(grep "^$grp:" /etc/group | cut -d: -f3)

usuarios=$(cut -d: -f1,4 /etc/passwd | grep ":$gid")

if [ -n "$usuarios" ]; then
    echo "No se puede eliminar el grupo."
    echo "Tiene usuarios asociados."
    sleep 3
    menu
fi

groupdel "$grp"

if [ $? -eq 0 ]; then
    echo "Grupo eliminado correctamente."
else
    echo "No fue posible eliminar el grupo."
fi

sleep 2
menu
}

# =======================
# ELIMINAR USUARIO
# =======================
eliminar_usuario(){
clear
echo "Eliminar Usuario"
echo ""

read -p "Ingrese el nombre del usuario: " username

while [ -z "$username" ] || [[ ! $username =~ ^[a-z_][a-z0-9_-]{0,31}$ ]]
do
    echo "Error: nombre inválido. Use minúsculas, números, _ o - (máx 32 caracteres)."
    sleep 2
    read -p "Ingrese el nombre del usuario: " username
done

verif_user=$(cut -d: -f1 /etc/passwd | grep -w "$username")

if [ -z "$verif_user" ]; then
    echo "Error: el usuario no existe."
    sleep 2
    menu
fi

userdel -r "$username"

if [ $? -eq 0 ]; then
    echo "Usuario eliminado correctamente."
else
    echo "No fue posible eliminar el usuario."
fi

sleep 2
menu
}

# =======================
# SALIR
# =======================
salir(){
echo "Saliendo..."
sleep 2
exit
}

menu