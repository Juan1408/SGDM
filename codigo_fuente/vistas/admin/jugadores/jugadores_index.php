<?php
    /**
     * @var array $datos Contiene los datos pasados desde el AdminControlador
     * Sin esta declaracion en la linea 18 $datos nos daria error al intentar acceder a $datos['totalUsuarios'];
     */
?>

<section class="modulo-admin">
    <h2 class="titulo-pantalla">Jugadores Registrados</h2>

    <section>
        <table class="tabla-estandar">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre Completo</th>
                    <th>Email</th>
                    <th>Estado</th>
                    <th>Fecha de Registro</th>
                    <th>Acciones (Suplantacion)</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($datos['jugadores'] as $jugador):?>
                    <tr>
                        <td><?php echo $jugador['id']; ?></td>
                        <td><?php echo $jugador['nombre_completo']; ?></td>
                        <td><?php echo $jugador['email']; ?></td>
                        <td>
                            <?php if ($jugador['esta_activo'] == 1) : ?>
                                <span class="etiqueta-activo">Activo</span>
                            <?php else : ?>
                                <span class="etiqueta-inactivo">Bloqueado</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $jugador['fecha_registro']; ?></td>
                        <td>
                            <a href="<?php echo URL_BASE; ?>index.php?c=usuario&a=verComo&id=<?php echo $jugador['id']; ?>" class="boton-accion">Ver como</a>
                            <a href="<?php echo URL_BASE; ?>index.php?c=usuario&a=editarUsuario&id=<?php echo $jugador['id']; ?>" class="boton-accion">Editar</a>
                            <a href="<?php echo URL_BASE; ?>index.php?c=usuario&a=cambiarEstadoUsuario&id=<?php echo $jugador['id']; ?>" class="boton-secundario">
                                <?php echo $jugador['esta_activo'] ? 'Bloquear' : 'Activar'; ?>
                            </a>
                            <!--Añadir un modal confirmando si desea eliminar la cuenta-->
                            <a href="<?php echo URL_BASE; ?>index.php?c=usuario&a=eliminarUsuario&id=<?php echo $jugador['id']; ?>&rol_id=<?php echo $jugador['rol_id']; ?>" class="boton-peligro">Eliminar Cuenta</a>
                            <!--En caso de eliminar cuenta, hay que validar que no tenga ningun evento registrado con suelta de ficha. Si tiene, mostramos un error y no le dejamos eliminarla-->
                            <!--FUNCION PENDIENTE A PROGRAMAR-->
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if(empty($datos['jugadores'])):?>
                    <tr>
                        <td colspan="6" class="celda-vacia">No se encontraron jugadores registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>

        </table>
    </section>

</section>
