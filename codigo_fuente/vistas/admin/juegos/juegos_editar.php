<?php /** @var array $juego */ ?>

<section class="modulo-admin">
    <div class="encabezado-seccion">
        <h2 class="titulo-pantalla"><i class="fas fa-edit"></i> Editar Juego</h2>
        <a href="<?php echo URL_BASE; ?>index.php?c=juego&a=index" class="boton-secundario">
            Volver al Catálogo
        </a>
    </div>

    <form action="<?php echo URL_BASE; ?>index.php?c=juego&a=actualizar" method="POST" class="formulario-estandar validacion-activa">
        <fieldset>
        <!-- ID Oculto para que el controlador sepa cuál actualizar -->
        <input type="hidden" name="id" value="<?php echo $juego['id']; ?>">

        <div class="grupo-formulario">
            <label for="nombre">Nombre del Juego <span class="requerido">*</span></label>
            <input type="text" name="nombre" id="nombre" class="campo-requerido" value="<?php echo $juego['nombre']; ?>" required>
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
            <label for="formato_equipo">Formato del Equipo (Jugadores por equipo) <span class="requerido">*</span></label>
            <input type="number" name="formato_equipo" id="formato_equipo" class="campo-requerido" min="1" max="15" value="<?php echo $juego['formato_equipo_defecto']; ?>" required>
        </div>

        <div class="acciones-formulario">
            <button type="submit" class="boton-accion">Actualizar Juego</button>
        </div>
        </fieldset>
    </form>
</section>
