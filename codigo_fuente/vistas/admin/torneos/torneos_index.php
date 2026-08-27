
<div class="encabezado-seccion">
    <h2>Gestión de Torneos</h2>
    
</div>

<!--Tabla de torneos-->
<div class="tarjeta-datos">
    <table class="tabla-admin">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre del Torneo</th>
                <th>Juego</th>
                <th>Formato</th>
                <th>Estado</th>
                <th>Fecha Inicio</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
            <?php if (empty($datos['torneos'])): ?>
            <tr>
                <td colspan="7" class="texto-centro">No hay torneos registrados aún.</td>
            </tr>

            <?php else: ?>
                <!--Bucle que recorre y muestra los torneos-->
                <?php foreach ($datos['torneos'] as $torneo): ?>
                    <tr>
                        <td><?php echo $torneo['id']; ?></td>
                        <td><strong><?php echo $torneo['nombre']; ?></strong></td>
                        <td><?php echo $torneo['nombre_juego']; ?></td>
                        <td><?php echo ucfirst($torneo['formato']); ?></td>
                        <td>
                            <span class="badge badge-<?php echo strtolower($torneo['estado'] == 'borrador' ? 'gris' : 'verde'); ?>">
                                <?php echo ucfirst($torneo['estado']); ?>
                            </span>
                        </td>
                        
                        <td><?php echo $torneo['fecha_inicio'] ? date('d/m/Y', strtotime($torneo['fecha_inicio'])) : 'Sin definir'; ?></td>
                        <td class="acciones-tabla">
                            <a href="<?php echo URL_BASE; ?>index.php?c=torneoAdmin&a=editar&id=<?php echo $torneo['id']; ?>" class="btn-icono" title="Editar">
                                <i class="fa-solid fa-pen"></i>
                            </a>

                            <a href="<?php echo URL_BASE; ?>index.php?c=torneoAdmin&a=eliminar&id=<?php echo $torneo['id']; ?>" class="btn-icono" title="Eliminar">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>