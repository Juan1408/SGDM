<div class="encabezado-dashboard">
    <h2>Registrar Nuevo Juego</h2>
    <a href="<?php echo URL_BASE; ?>index.php?c=juego&a=index" class="boton-secundario">Volver al Catalogo</a>
</div>

<div class="tarjeta-datos">
    <form action="<?php echo URL_BASE; ?>index.php?c=juego&a=guardar" method="POST" class="formulario-admin validacion-activa">

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
            <label for="formato_equipo">Formato del Equipo (Jugadores por equipo) <span class="requerido">*</span></label>
            <input type="number" id="formato_equipo" name="formato_equipo" class="campo-requerido" min="1" max="15" value="1" required>
            <small>Usa 1 si es un juego individual (1 vs 1).</small>
        </div>

        <div class="acciones-formulario">
            <button type="submit" class="boton-primario">
                Guardar en el Catalogo
            </button>
        </div>
    </form>
</div>