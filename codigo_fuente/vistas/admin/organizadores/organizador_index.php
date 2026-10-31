<?php
    /**
     * @var array $datos Contiene la lista de organizadores
     */
?>


<section class="modulo-admin">
    <h2 class="titulo-pantalla">Gestión y Aprobación de Organizadores</h2>

    <?php if (!empty($datos['mensaje'])): ?>
        <div class="alerta alerta-exito">
            <?php echo htmlspecialchars($datos['mensaje']); ?>
        </div>
    <?php endif; ?>

    <section>
        <table class="tabla-estandar">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre / Organización</th>
                    <th>Email</th>
                    <th>Teléfono</th>
                    <th>Estado Cuenta</th>
                    <th>Aprobación Admin</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($datos['organizadores'] as $organizador): ?>
                    <tr>
                        <td><?php echo (int) $organizador['id']; ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($organizador['nombre_completo']); ?></strong><br>
                            <small class="texto-secundario"><?php echo htmlspecialchars($organizador['nombre_organizacion']); ?></small>
                        </td>
                        <td><?php echo htmlspecialchars($organizador['email']); ?></td>
                        <td><?php echo htmlspecialchars($organizador['telefono'] ?? 'N/A'); ?></td>
                        <td>
                            <span class="<?php echo $organizador['esta_activo'] ? 'estado-exito' : 'estado-fallido'; ?>">
                                <?php echo $organizador['esta_activo'] ? 'Activo' : 'Bloqueado'; ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($organizador['verificado_oficial']): ?>
                                <span class="estado-exito"><i class="fa-solid fa-circle-check"></i> Habilitado</span>
                            <?php else: ?>
                                <span class="estado-pendiente"><i class="fa-solid fa-clock"></i> Pendiente</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($organizador['verificado_oficial']): ?>
                                <form method="POST" action="<?php echo URL_BASE; ?>index.php?c=organizador&a=responderSolicitud">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="id" value="<?php echo (int) $organizador['id']; ?>">
                                    <input type="hidden" name="decision" value="revocar">
                                    <button type="submit" class="boton-secundario">
                                    <i class="fa-solid fa-ban"></i> Revocar Habilitación
                                    </button>
                                </form>
                            <?php else: ?>
                                <form method="POST" action="<?php echo URL_BASE; ?>index.php?c=organizador&a=responderSolicitud">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="id" value="<?php echo (int) $organizador['id']; ?>">
                                    <input type="hidden" name="decision" value="aprobar">
                                    <button type="submit" class="boton-exito">
                                    <i class="fa-solid fa-check"></i> Aprobar
                                    </button>
                                </form>
                                <form method="POST" action="<?php echo URL_BASE; ?>index.php?c=organizador&a=responderSolicitud">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="id" value="<?php echo (int) $organizador['id']; ?>">
                                    <input type="hidden" name="decision" value="rechazar">
                                    <button type="submit" class="boton-peligro">
                                    <i class="fa-solid fa-xmark"></i> Rechazar
                                    </button>
                                </form>
                            <?php endif; ?>
                            <a href="<?php echo URL_BASE; ?>index.php?c=usuario&a=ver&id=<?php echo (int) $organizador['id']; ?>&origen=organizadores" class="boton-accion">Ver ficha</a>
                            <form method="POST" action="<?php echo URL_BASE; ?>index.php?c=usuario&a=verComo&id=<?php echo (int) $organizador['id']; ?>" style="display:inline-block;">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8'); ?>">
                                <button type="submit" class="boton-accion">Ver Como</button>
                            </form>
                            <form method="POST" action="<?php echo URL_BASE; ?>index.php?c=usuario&a=cambiarEstadoUsuario" style="display:inline-block;">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8'); ?>">
                                <input type="hidden" name="id" value="<?php echo (int) $organizador['id']; ?>">
                                <input type="hidden" name="rol_id" value="2">
                                <button type="submit" class="boton-secundario">
                                    <?php echo $organizador['esta_activo'] ? 'Bloquear' : 'Activar'; ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($datos['organizadores'])): ?>
                    <tr>
                        <td colspan="7" class="celda-vacia">No se encontraron organizadores registrados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
</section>
