<?php
/** @var array $datos */
?>
<div class="encabezado-seccion">
    <h2>Directorio Global de Torneos</h2>
    <p class="texto-ayuda">Supervisa, filtra y modera todos los torneos registrados en el sistema por los organizadores.</p>
</div>

<!-- Filtros -->
<div class="caja-filtros-profesional">
    <form method="GET" action="<?php echo URL_BASE; ?>index.php" class="formulario-filtros">
        <input type="hidden" name="c" value="torneoAdmin">
        <input type="hidden" name="a" value="index">

        <div class="grupo-formulario-en-linea">
            <label for="juego_id" class="label-profesional">Disciplina</label>
            <select name="juego_id" id="juego_id" class="select-profesional">
                <option value="">Todas las disciplinas</option>
                <?php foreach($datos['juegos'] as $juego): ?>
                    <option value="<?php echo htmlspecialchars($juego['id'] ?? $juego['id_juego']); ?>" <?php echo ($datos['filtro_juego'] == ($juego['id'] ?? $juego['id_juego'])) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($juego['nombre'] ?? $juego['titulo']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="grupo-formulario-en-linea">
            <label for="estado" class="label-profesional">Estado</label>
            <select name="estado" id="estado" class="select-profesional">
                <option value="todos" <?php echo $datos['filtro_estado'] === 'todos' ? 'selected' : ''; ?>>Cualquier estado</option>
                <option value="borrador" <?php echo $datos['filtro_estado'] === 'borrador' ? 'selected' : ''; ?>>Borradores</option>
                <option value="inscripciones_abiertas" <?php echo $datos['filtro_estado'] === 'inscripciones_abiertas' ? 'selected' : ''; ?>>Inscripciones Abiertas</option>
                <option value="en_curso" <?php echo $datos['filtro_estado'] === 'en_curso' ? 'selected' : ''; ?>>En Curso</option>
                <option value="finalizado" <?php echo $datos['filtro_estado'] === 'finalizado' ? 'selected' : ''; ?>>Finalizados</option>
                <option value="cancelado" <?php echo $datos['filtro_estado'] === 'cancelado' ? 'selected' : ''; ?>>Cancelados</option>
            </select>
        </div>

        <div class="grupo-formulario-en-linea">
            <label for="orden" class="label-profesional">Ordenar Por</label>
            <select name="orden" id="orden" class="select-profesional">
                <option value="nuevos" <?php echo $datos['filtro_orden'] === 'nuevos' ? 'selected' : ''; ?>>Más nuevos primero</option>
                <option value="antiguos" <?php echo $datos['filtro_orden'] === 'antiguos' ? 'selected' : ''; ?>>Más antiguos primero</option>
                <option value="az" <?php echo $datos['filtro_orden'] === 'az' ? 'selected' : ''; ?>>Alfabético (A-Z)</option>
                <option value="za" <?php echo $datos['filtro_orden'] === 'za' ? 'selected' : ''; ?>>Alfabético (Z-A)</option>
            </select>
        </div>

        <div class="grupo-formulario-en-linea">
            <button type="submit" class="btn-primario btn-filtro-pro">
                <i class="fa-solid fa-filter"></i> Aplicar Filtros
            </button>
        </div>
    </form>
</div>

<!--Tabla de torneos-->
<div class="tarjeta-datos">
    <table class="tabla-estandar">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre del Torneo</th>
                <th>Juego</th>
                <th>Formato</th>
                <th>Organizador</th>
                <th>Estado</th>
                <th>Fecha Inicio</th>
                <th>Acciones</th>
            </tr>
        </thead>

        <tbody>
            <?php if (empty($datos['torneos'])): ?>
            <tr>
                <td colspan="8" class="texto-centrado">No hay torneos registrados que coincidan con estos filtros.</td>
            </tr>

            <?php else: ?>
                <!--Bucle que recorre y muestra los torneos-->
                <?php foreach ($datos['torneos'] as $torneo): ?>
                    <tr>
                        <td><?php echo $torneo['id']; ?></td>
                        <td><strong><?php echo $torneo['nombre']; ?></strong></td>
                        <td><?php echo $torneo['nombre_juego']; ?></td>
                        <td><?php echo ucfirst($torneo['formato']); ?></td>
                        <td><?php echo htmlspecialchars($torneo['organizador_nombre'] ?? 'Admin'); ?></td>
                        <td>
                            <?php
                                $badgeClass = 'badge';
                                if ($torneo['estado'] === 'en_curso' || $torneo['estado'] === 'inscripciones_abiertas') {
                                    $badgeClass = 'badge badge-success';
                                } elseif ($torneo['estado'] === 'cancelado') {
                                    $badgeClass = 'badge badge-danger';
                                }
                            ?>
                            <span class="<?php echo $badgeClass; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $torneo['estado'])); ?>
                            </span>
                        </td>
                        
                        <td><?php echo $torneo['fecha_inicio'] ? date('d/m/Y', strtotime($torneo['fecha_inicio'])) : 'Sin definir'; ?></td>
                        <td class="acciones-tabla">
                            <a href="<?php echo URL_BASE; ?>index.php?c=torneoAdmin&a=editar&id=<?php echo $torneo['id']; ?>" class="btn-icono" title="Editar">
                                <i class="fa-solid fa-pen"></i>
                            </a>

                            <a href="<?php echo URL_BASE; ?>index.php?c=torneoAdmin&a=eliminar&id=<?php echo $torneo['id']; ?>" class="btn-icono" title="Eliminar" onclick="return confirm('¿Estás seguro de que deseas eliminar este torneo? Esta acción es totalmente irreversible y eliminará todos los partidos asociados.');">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>