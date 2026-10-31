<?php
/** @var array $datos */
?>
<div class="encabezado-seccion">
    <h2>Directorio de Equipos</h2>
    <p class="texto-ayuda">Aquí puedes ver y filtrar todos los equipos multigaming registrados en la plataforma.</p>
</div>

<!-- Filtros -->
<div class="caja-filtros-profesional">
    <form method="GET" action="<?php echo URL_BASE; ?>index.php" class="formulario-filtros">
        <input type="hidden" name="c" value="equipoAdmin">
        <input type="hidden" name="a" value="index">

        <div class="grupo-formulario-en-linea">
            <label for="juego_id" class="label-profesional">Filtrar por Disciplina</label>
            <select name="juego_id" id="juego_id" class="select-profesional">
                <option value="">Todas las disciplinas / Sin torneos</option>
                <?php foreach($datos['juegos'] as $juego): ?>
                    <?php 
                        $id = $juego['id'] ?? $juego['id_juego'] ?? '';
                        $nombre = $juego['nombre'] ?? $juego['titulo'] ?? 'Desconocido';
                    ?>
                    <option value="<?php echo htmlspecialchars($id); ?>" <?php echo ($datos['filtro_juego'] == $id) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($nombre); ?>
                    </option>
                <?php endforeach; ?>
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

<!-- Tabla de Equipos -->
<div class="tarjeta-datos">
    <table class="tabla-estandar">
        <thead>
            <tr>
                <th>ID</th>
                <th>Logo</th>
                <th>Nombre del Equipo</th>
                <th>Líder / Creador</th>
                <th>Miembros</th>
                <th>Fecha de Creación</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($datos['equipos'])): ?>
                <tr>
                    <td colspan="8" class="texto-centrado">No se encontraron equipos con los filtros actuales.</td>
                </tr>
            <?php else: ?>
                <?php foreach($datos['equipos'] as $equipo): ?>
                    <tr>
                        <td><?php echo (int) $equipo['id']; ?></td>
                        <td>
                            <?php if(!empty($equipo['escudo_url'])): ?>
                                <img src="<?php echo htmlspecialchars($equipo['escudo_url']); ?>" alt="Logo" class="img-escudo-tabla">
                            <?php else: ?>
                                <div class="img-escudo-tabla"></div>
                            <?php endif; ?>
                        </td>
                        <td class="texto-negrita"><?php echo htmlspecialchars($equipo['nombre_equipo']); ?></td>
                        <td><?php echo htmlspecialchars($equipo['lider_nombre']); ?></td>
                        <td><span class="badge"><?php echo (int) $equipo['total_miembros']; ?></span></td>
                        <td><?php echo date('d/m/Y', strtotime($equipo['fecha_creacion'])); ?></td>
                        <td>
                            <?php if($equipo['activo'] == 1): ?>
                                <span class="badge badge-success">Activo</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo URL_BASE; ?>index.php?c=equipoAdmin&a=ver&id=<?php echo (int) $equipo['id']; ?>"
                               class="boton-accion" title="Ver ficha del equipo">
                                <i class="fa-solid fa-eye"></i> Ver ficha
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
