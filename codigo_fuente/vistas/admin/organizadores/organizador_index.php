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
                        <td><?php echo $organizador['id']; ?></td>
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
                            <a href="<?php echo URL_BASE; ?>index.php?c=organizador&a=cambiarVerificacion&id=<?php echo $organizador['id']; ?>" 
                               class="<?php echo $organizador['verificado_oficial'] ? 'boton-secundario' : 'boton-exito'; ?>">
                                <?php echo $organizador['verificado_oficial'] ? 'Revocar Habilitación' : 'Aprobar Organizador'; ?>
                            </a>
                            <a href="<?php echo URL_BASE; ?>index.php?c=usuario&a=verComo&id=<?php echo $organizador['id']; ?>" class="boton-accion">Ver Como</a>
                            <a href="<?php echo URL_BASE; ?>index.php?c=usuario&a=editarUsuario&id=<?php echo $organizador['id']; ?>" class="boton-accion">Editar</a>
                            <a href="<?php echo URL_BASE; ?>index.php?c=usuario&a=cambiarEstadoUsuario&id=<?php echo $organizador['id']; ?>" class="boton-secundario">
                                <?php echo $organizador['esta_activo'] ? 'Bloquear' : 'Activar'; ?>
                            </a>
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