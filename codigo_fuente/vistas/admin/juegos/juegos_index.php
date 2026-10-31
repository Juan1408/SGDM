
<section class="modulo-admin">
    <div class="encabezado-seccion">
        <h2 class="titulo-pantalla"><i class="fas fa-gamepad"></i> Catálogo de Juegos</h2>
        <a href="<?php echo URL_BASE; ?>index.php?c=juego&a=crear" class="boton-accion">
            Añadir Nuevo Juego
        </a>
    </div>

    <section>
        <table class="tabla-estandar">
            <thead>
            <tr>
                <th>ID</th>
                <th>Nombre del Juego</th>
                <th>Categoría</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($juegos)): ?> 
                <tr>
                    <td colspan="5" class="text-center">No hay juegos registrados.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($juegos as $juego): ?>
                <tr>
                    <td><?php echo $juego['id']; ?></td>
                    <td><?php echo $juego['nombre']; ?></td>
                    <td><?php echo $juego['categoria']; ?></td>
                    <td><?php if ($juego['activo']): ?>
                        <span class="estado-activo">Activo</span>
                    <?php else: ?>
                        <span class="estado-inactivo">Inactivo</span>
                    <?php endif; ?>
                    </td>
                    <td class="acciones-tabla">
                        <form method="POST" action="<?php echo URL_BASE; ?>index.php?c=juego&a=cambiarEstado" style="display:inline;">
                            <input type="hidden" name="csrf_token" value="<?php echo \App\Ayudantes\Sesion::generarTokenCSRF(); ?>">
                            <input type="hidden" name="id" value="<?php echo $juego['id']; ?>">
                            <button type="submit" class="boton-secundario" style="cursor:pointer; border:none;">
                                <?php echo $juego['activo'] ? 'Desactivar' : 'Activar'; ?>
                            </button>
                        </form>
                        <a href="<?php echo URL_BASE; ?>index.php?c=juego&a=editar&id=<?php echo $juego['id'] ?>" class="boton-accion">
                            Editar
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    </section>
</section>