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
                <?php foreach ($datos['jugadores'] as $jugador): ?>
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
                            <form method="POST" action="<?php echo URL_BASE; ?>index.php?c=usuario&a=verComo&id=<?php echo $jugador['id']; ?>" style="display:inline-block;">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8'); ?>">
                                <button type="submit" class="boton-accion">Ver como</button>
                            </form>
                            <a href="<?php echo URL_BASE; ?>index.php?c=usuario&a=editarUsuario&id=<?php echo $jugador['id']; ?>" class="boton-accion">Editar</a>
                            <form method="POST" action="<?php echo URL_BASE; ?>index.php?c=usuario&a=cambiarEstadoUsuario" style="display:inline-block;">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="hidden" name="id" value="<?php echo $jugador['id']; ?>">
                                <button type="submit" class="boton-secundario">
                                    <?php echo $jugador['esta_activo'] ? 'Bloquear' : 'Activar'; ?>
                                </button>
                            </form>
                            <!--Añadir un modal confirmando si desea eliminar la cuenta-->
                            <form method="POST"
                                action="<?php echo URL_BASE; ?>index.php?c=usuario&a=eliminarUsuario"
                                onsubmit="return confirm('¿Eliminar este usuario?');">
                                <input type="hidden" name="id" value="<?php echo (int) $jugador['id']; ?>">
                                <input type="hidden" name="csrf_token"
                                    fix.md 2026-10-07
                                    6 / 11
                                    value="<?php echo
                                            htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8');
                                            ?>">
                                <button type="submit" class="boton-peligro">Eliminar cuenta</button>
                            </form>

                            <!--En caso de eliminar cuenta, hay que validar que no tenga ningun evento registrado con suelta de ficha. Si tiene, mostramos un error y no le dejamos eliminarla-->
                            <!--FUNCION PENDIENTE A PROGRAMAR-->
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($datos['jugadores'])): ?>
                    <tr>
                        <td colspan="6" class="celda-vacia">No se encontraron jugadores registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>

        </table>
    </section>

</section>