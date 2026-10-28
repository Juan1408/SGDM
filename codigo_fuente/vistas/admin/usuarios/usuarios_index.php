<?php 
    /**
     * @var array $datos
     */
?>
<section class="modulo-admin">
    <h2 class="titulo-pantalla">Administradores del sistema</h2>

    <section>
        <table class="tabla-estandar">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre Completo</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Fecha de Registro</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($datos['usuarios'] as $usuario):?>
                    <tr>
                        <td><?php echo (int) $usuario['id']; ?></td>
                        <td><?php echo htmlspecialchars($usuario['nombre_completo'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td class="etiqueta-rol"><span><?php echo htmlspecialchars($usuario['nombre_rol'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                        <td><?php if ($usuario['esta_activo']): ?>
                            <span class="etiqueta-activo">Activo</span>
                         <?php else: ?>
                            <span class="etiqueta-inactivo">Bloqueado</span>
                         <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($usuario['fecha_registro'], ENT_QUOTES, 'UTF-8'); ?></td>

                        <td>
                            <a href="<?php echo URL_BASE; ?>index.php?c=usuario&a=ver&id=<?php echo (int) $usuario['id']; ?>&origen=usuarios" class="boton-accion">Ver ficha</a>
                            <?php if ((int) $usuario['id'] !== (int) ($_SESSION['usuario_id'] ?? 0)): ?>
                                <form method="POST" action="<?php echo URL_BASE; ?>index.php?c=usuario&a=cambiarEstadoUsuario" style="display:inline-block;">
                                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8'); ?>">
                                    <input type="hidden" name="id" value="<?php echo (int) $usuario['id']; ?>">
                                    <button type="submit" class="boton-secundario">
                                        <?php echo $usuario['esta_activo'] ? 'Bloquear' : 'Activar'; ?>
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="texto-ayuda">Tu propia cuenta</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>    
            
                <?php if (empty($datos['usuarios'])):?>
                    <tr>
                        <td colspan="7" class="celda-vacia">No se encontraron usuarios registrados</td>
                    </tr>
                <?php endif; ?>


            </tbody>
        </table>
    </section>
    
    
</section>