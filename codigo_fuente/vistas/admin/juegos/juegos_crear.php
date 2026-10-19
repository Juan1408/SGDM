<section class="modulo-admin">
    <div class="encabezado-seccion">
        <h2 class="titulo-pantalla"><i class="fas fa-plus-circle"></i> Registrar Nuevo Juego</h2>
        <a href="<?php echo URL_BASE; ?>index.php?c=juego&a=index" class="boton-secundario">Volver al Catálogo</a>
    </div>

    <form action="<?php echo URL_BASE; ?>index.php?c=juego&a=guardar" method="POST" enctype="multipart/form-data" class="formulario-estandar validacion-activa">
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8'); ?>">
        <fieldset>
            <div class="grupo-formulario">
        <label for="nombre">Nombre del Juego / Disciplina <span class="requerido">*</span></label>
        <input type="text" id="nombre" name="nombre" class="campo-requerido" placeholder="Ej: League of Legends, Fútbol 5..." required>
    </div>
    
    <div class="grupo-formulario">
        <label for="categoria">Categoria <span class="requerido">*</span></label>
        <select id="categoria" name="categoria" class="campo-requerido" required>
            <option value="esport">eSport</option>
            <option value="deporte-fisico">Deporte Fisico</option>
            <option value="juego-mesa">Juego de Mesa</option>
            </select>
        </div>

        <div class="grupo-formulario">
            <label for="imagen">Carátula del juego</label>
            <input type="file" id="imagen" name="imagen" accept="image/jpeg,image/png,image/webp">
            <p><small>JPG, PNG o WEBP, máximo 2 MB. Es la imagen que se ve en la tarjeta del catálogo público.</small></p>
        </div>

        <div class="grupo-formulario">
            <h3>Sistema de Puntuación Base</h3>
            <label for="puntos_victoria">Puntos por Victoria</label>
            <input type="number" step="0.01" id="puntos_victoria" name="puntos_victoria" value="3.00" required>
            
            <label for="puntos_empate">Puntos por Empate</label>
            <input type="number" step="0.01" id="puntos_empate" name="puntos_empate" value="1.00" required>
            
            <label for="puntos_derrota">Puntos por Derrota</label>
            <input type="number" step="0.01" id="puntos_derrota" name="puntos_derrota" value="0.00" required>
        </div>

        <div class="grupo-formulario">
            <h3>Modalidad de Juego Permitida</h3>
            <p><small>¿Se juega individual o en equipos?</small></p>
            <div style="display: flex; gap: 1.5rem; flex-wrap: wrap; margin-top: 0.5rem;">
                <?php if(isset($modalidades) && !empty($modalidades)): ?>
                    <?php foreach($modalidades as $mod): ?>
                        <label class="opcion-checkbox" style="margin-top: 5px;">
                            <input class="entrada-checkbox" type="radio" name="modalidades[]" value="<?php echo $mod['id']; ?>" required> 
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
                <?php if(isset($formatos) && !empty($formatos)): ?>
                    <?php foreach($formatos as $formato): ?>
                        <label class="opcion-checkbox" style="margin-top: 5px;">
                            <input class="entrada-checkbox" type="checkbox" name="formatos[]" value="<?php echo htmlspecialchars($formato['codigo']); ?>"> 
                            <?php echo htmlspecialchars($formato['nombre']); ?>
                        </label>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No hay formatos registrados en el sistema.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="acciones-formulario">
            <button type="submit" class="boton-accion">
                Guardar en el Catálogo
            </button>
        </div>
        </fieldset>
    </form>
</section>