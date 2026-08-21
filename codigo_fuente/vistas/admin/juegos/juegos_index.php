
<div class="encabezado-dashboard">
    <h2><i class="fas fa-gamepad"></i>Catalogo de Juegos</h2>
    <a href="<?php echo URL_BASE; ?>index.php?c=juego&a=crear" class="boton-primario">
        Añadir Nuevo Juego
    </a>
</div>

<div class="tarjeta-datos">
    <table class="tabla-admin">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre del Juego</th>
                <th>Categoría</th>
                <th>Jugadores</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($juegos)): ?> 
                <tr>
                    <td colspan="6" class="text-center">No hay juegos registrados.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($juegos as $juego): ?>
                <tr>
                    <td><?php echo $juego['id']; ?></td>
                    <td><?php echo $juego['nombre']; ?></td>
                    <td><?php echo $juego['categoria']; ?></td>
                    <td><?php echo $juego['formato_equipo_defecto']; ?></td>
                    <td><?php if ($juego['activo']): ?>
                        <span class="estado-activo">Activo</span>
                    <?php else: ?>
                        <span class="estado-inactivo">Inactivo</span>
                    <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?php echo URL_BASE; ?>index.php?c=juego&a=cambiarEstado&id=<?php echo $juego['id']; ?>" class="boton-secundario">
                            <?php echo $juego['activo'] ? 'Desactivar' : 'Activar'; ?>
                        </a>
                        <a href="<?php echo URL_BASE; ?>index.php?c=juego&a=editar&id=<?php echo $juego['id'] ?>" class="boton-accion">
                            Editar
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>