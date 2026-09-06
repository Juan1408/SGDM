<?php
    /**
     * @var array $datos Contiene la lista de organizadores
     */
?>


<section class="modulo-admin">
    <h2 class="titulo-pantalla">Organizadores Registrados</h2>

    <section>
        <table class="tabla-estandar">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($datos['organizadores'] as $organizador): ?>
                    <tr>
                        <td><?php echo $organizador['id']; ?></td>
                        <td><?php echo $organizador['nombre_completo']; ?></td>
                        <td><?php echo $organizador['email']; ?></td>
                        <td><?php echo $organizador['telefono']; ?></td>
                        <td><?php echo $organizador['esta_activo']; ?></td>
                        <td>
                            <a href="<?php echo URL_BASE; ?>index.php?c=usuario&a=verComo&id=<?php echo $organizador['id']; ?>" class="boton-accion">Ver Como</a>
                            <a href="<?php echo URL_BASE; ?>index.php?c=usuario&a=editarUsuario&id=<?php echo $organizador['id']; ?>" class="boton-accion">Editar</a>
                            <a href="<?php echo URL_BASE; ?>index.php?c=usuario&a=cambiarEstadoUsuario&id=<?php echo $organizador['id']; ?>" class="boton-secundario">
                                <?php echo $organizador['esta_activo'] ? 'Activar' : 'Bloquear'; ?>
                            </a>
                            <!--Añadir un modal confirmando si desea eliminar la cuenta-->
                            <a href="<?php echo URL_BASE; ?>index.php?c=usuario&a=eliminarUsuario&id=<?php echo $organizador['id']; ?>" class="boton-peligro">Eliminar Cuenta</a>
                            <!--En caso de eliminar cuenta, hay que validar que no tenga ningun evento registrado con suelta de ficha. Si tiene, mostramos un error y no le dejamos eliminarla-->
                            <!--FUNCION PENDIENTE A PROGRAMAR-->
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($datos['organizadores'])): ?>
                    <tr>
                        <td colspan="6" class="celda-vacia">No se encontraron organizadores registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
</section>