<?php
/** 
 * @var array $datos
 */
?>

<section class="encabezado-seccion" style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h2 class="titulo-pantalla">Auditoría</h2>
        <p class="texto-ayuda encabezado-descripcion">Gestión de auditoría del sistema</p>
    </div>
    <form method="GET" action="index.php" style="display: flex; gap: 10px; align-items: center;">
        <input type="hidden" name="c" value="auditoria">
        <input type="hidden" name="a" value="index">
        <label for="fecha">Filtrar por fecha:</label>
        <input type="date" id="fecha" name="fecha" value="<?php echo htmlspecialchars($datos['fechaFiltro'] ?? ''); ?>" style="padding: 5px; border-radius: 4px; border: 1px solid #ccc;">
        <button type="submit" class="boton-primario">Buscar</button>
        <?php if (!empty($datos['fechaFiltro'])): ?>
            <a href="index.php?c=auditoria&a=index" class="boton-secundario">Limpiar</a>
        <?php endif; ?>
    </form>
</section>

<div>
    <table class="tabla-estandar tabla-fija">
        <thead>
            <tr>
                <th class="col-xs">ID</th>
                <th class="col-sm">Usuario</th>
                <th class="col-sm">Acción</th>
                <th class="col-md">Descripción</th>
                <th class="col-sm">Fecha</th>
                <th class="col-sm">IP</th>
                <th class="col-lg">User Agent</th>
            </tr>
        </thead>
                <tbody>
            <?php foreach ($datos['auditoria'] as $log): ?>
            <tr>
                <td><?php echo $log['id']; ?></td>
                <td><?php echo $log['usuario_email'] ? $log['usuario_email'] : 'Sistema'; ?></td>
                <td><span class="estado-pendiente"><?php echo $log['accion']; ?></span></td>
                <td>
                    <span class="texto-truncado" title="<?php echo $log['descripcion']; ?>">
                        <?php echo $log['descripcion']; ?>
                    </span>
                </td>
                <td><?php echo date('d/m/Y H:i', strtotime($log['fecha_hora'])); ?></td>
                <td><?php echo $log['ip_origen']; ?></td>
                <td>
                    <span class="texto-truncado-largo" title="<?php echo $log['user_agent']; ?>">
                        <?php echo $log['user_agent']; ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>

    </table>
</div>