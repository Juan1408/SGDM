<?php 
/** 
 * @var array $juego 
 * @var array $todas_modalidades
 * @var array $ids_permitidas
 * @var array $todos_formatos
 * @var array $ids_formatos_permitidos
 */ 
?>

<section class="modulo-admin">
    <div class="encabezado-seccion">
        <h2 class="titulo-pantalla"><i class="fas fa-edit"></i> Editar Juego</h2>
        <a href="<?php echo URL_BASE; ?>index.php?c=juego&a=index" class="boton-secundario">
            Volver al Catálogo
        </a>
    </div>

    <form action="<?php echo URL_BASE; ?>index.php?c=juego&a=actualizar" method="POST" enctype="multipart/form-data" class="formulario-estandar validacion-activa">
    <input type="hidden" name="csrf_token" value="<?php echo \App\Ayudantes\Sesion::generarTokenCSRF(); ?>">
        <fieldset>
        <!-- ID Oculto para que el controlador sepa cuál actualizar -->
        <input type="hidden" name="id" value="<?php echo $juego['id']; ?>">

        <div class="grupo-formulario">
            <label for="nombre">Nombre del Juego <span class="requerido">*</span></label>
            <input type="text" name="nombre" id="nombre" class="campo-requerido" value="<?php echo htmlspecialchars($juego['nombre']); ?" required>
        </div>

        <div class="grupo-formulario">
            <label for="categoria">Categoría <span class="requerido">*</span></label>
            <select name="categoria" id="categoria" class="campo-requerido" required>
                <option value="esport" <?php echo ($juego['categoria'] === 'esport' ? 'selected' : ''); ?>>eSport</option>
                <option value="deporte-fisico" <?php echo ($juego['categoria'] === 'deporte-fisico' ? 'selected' : ''); ?>>Deporte Físico</option>
                <option value="juego-mesa" <?php echo ($juego['categoria'] === 'juego-mesa' ? 'selected' : ''); ?>>Juego de Mesa</option>
            </select>
        </div>

        <div class="grupo-formulario">
            <label for="imagen">Carátula del juego</label>
            <?php if (!empty($juego['imagen_url'])): ?>
                <p><small>Imagen actual: <?php echo htmlspecialchars($juego['imagen_url']); ?>. Subí otra solo si querés reemplazarla.</small></p>
            <?php endif; ?>
            <input type="file" id="imagen" name="imagen" accept="image/jpeg,image/png,image/webp">
        </div>

        <div class="grupo-formulario">
            <h3>Sistema de Puntuación Base</h3>
            <label for="puntos_victoria">Puntos por Victoria</label>
            <input type="number" step="0.01" id="puntos_victoria" name="puntos_victoria" value="<?php echo $juego['puntos_victoria']; ?>" required>
            
            <label for="puntos_empate">Puntos por Empate</label>
            <input type="number" step="0.01" id="puntos_empate" name="puntos_empate" value="<?php echo $juego['puntos_empate']; ?>" required>
            
            <label for="puntos_derrota">Puntos por Derrota</label>
            <input type="number" step="0.01" id="puntos_derrota" name="puntos_derrota" value="<?php echo $juego['puntos_derrota']; ?>" required>
        </div>

        <div class="grupo-formulario">
            <h3>Modalidad de Juego Permitida</h3>
            <p><small>¿Se juega individual o en equipos?</small></p>
            <div style="display: flex; gap: 1.5rem; flex-wrap: wrap; margin-top: 0.5rem;">
                <?php if(isset($todas_modalidades) && !empty($todas_modalidades)): ?>
                    <?php foreach($todas_modalidades as $mod): ?>
                        <label class="opcion-checkbox" style="margin-top: 5px;">
                            <input class="entrada-checkbox" type="radio" name="modalidades[]" value="<?php echo $mod['id']; ?>" <?php echo (in_array($mod['id'], $ids_permitidas)) ? 'checked' : ''; ?> required> 
                            <?php echo htmlspecialchars($mod['nombre']); ?>
                        </label>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No hay modalidades registradas en el sistema.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="grupo-formulario">
            <h3>Formatos de Torneo Permitidas</h3>
            <p><small>Selecciona los sistemas de competencia que soporta.</small></p>
            <div style="display: flex; gap: 1.5rem; flex-wrap: wrap; margin-top: 0.5rem;">
                <?php if(isset($todos_formatos) && !empty($todos_formatos)): ?>
                    <?php foreach($todos_formatos as $formato): ?>
                        <label class="opcion-checkbox" style="margin-top: 5px;">
                            <input class="entrada-checkbox" type="checkbox" name="formatos[]" value="<?php echo htmlspecialchars($formato['codigo']); ?>" <?php echo (in_array($formato['codigo'], $ids_formatos_permitidos)) ? 'checked' : ''; ?>> 
                            <?php echo htmlspecialchars($formato['nombre']); ?>
                        </label>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No hay formatos registrados en el sistema.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="acciones-formulario">
            <button type="submit" class="boton-accion">Actualizar Juego</button>
        </div>
        </fieldset>
    </form>
</section>
