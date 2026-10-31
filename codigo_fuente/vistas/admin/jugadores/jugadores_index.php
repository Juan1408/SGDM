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
                        <td><?php echo (int) $jugador['id']; ?></td>
                        <td><?php echo htmlspecialchars($jugador['nombre_completo'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($jugador['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <?php if ($jugador['esta_activo'] == 1) : ?>
                                <span class="etiqueta-activo">Activo</span>
                            <?php else : ?>
                                <span class="etiqueta-inactivo">Bloqueado</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($jugador['fecha_registro'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <a href="<?php echo URL_BASE; ?>index.php?c=usuario&a=ver&id=<?php echo (int) $jugador['id']; ?>&origen=jugadores" class="boton-accion">Ver ficha</a>
                            <form method="POST" action="<?php echo URL_BASE; ?>index.php?c=usuario&a=verComo&id=<?php echo (int) $jugador['id']; ?>" style="display:inline-block;">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8'); ?>">
                                <button type="submit" class="boton-accion">Ver como</button>
                            </form>
                            <form method="POST" action="<?php echo URL_BASE; ?>index.php?c=usuario&a=cambiarEstadoUsuario" style="display:inline-block;">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="hidden" name="id" value="<?php echo (int) $jugador['id']; ?>">
                                <input type="hidden" name="rol_id" value="3">
                                <button type="submit" class="boton-secundario">
                                    <?php echo $jugador['esta_activo'] ? 'Bloquear' : 'Activar'; ?>
                                </button>
                            </form>
                            <!--Baja lógica: conserva historial, equipos, capitanías y auditoría-->
                            <form method="POST"
                                action="<?php echo URL_BASE; ?>index.php?c=usuario&a=eliminarUsuario"
                                onsubmit="return confirm('¿Dar de baja esta cuenta? Se conservará su historial y no podrá iniciar sesión.');">
                                <input type="hidden" name="id" value="<?php echo (int) $jugador['id']; ?>">
                                <input type="hidden" name="rol_id" value="3">
                                <input type="hidden" name="csrf_token"
                                    value="<?php echo htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8'); ?>">
                                <button type="submit" class="boton-peligro">Dar de baja</button>
                            </form>
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