<?php
/** @var array $datos */
?>
<div class="encabezado-seccion">
    <h2>Editar Torneo (Modo Administrador)</h2>
    <a href="<?php echo URL_BASE; ?>index.php?c=torneoAdmin&a=index" class="btn-secundario">
        <i class="fa-solid fa-arrow-left"></i> Volver a la lista
    </a>
</div>

<div class="tarjeta-datos">
    <form action="<?php echo URL_BASE; ?>index.php?c=torneoAdmin&a=actualizar" method="POST" class="formulario-estandar">
        <input type="hidden" name="csrf_token" value="<?php echo \App\Ayudantes\Sesion::generarTokenCSRF(); ?>">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($datos['torneo']['id']); ?>">
        
        <div class="grupo-formulario">
            <label for="nombre">Nombre del Torneo</label>
            <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($datos['torneo']['nombre']); ?>" required>
        </div>

        <div class="grupo-formulario">
            <label for="estado">Estado de Moderación</label>
            <select id="estado" name="estado" required>
                <option value="borrador" <?php echo $datos['torneo']['estado'] === 'borrador' ? 'selected' : ''; ?>>Borrador</option>
                <option value="inscripciones_abiertas" <?php echo $datos['torneo']['estado'] === 'inscripciones_abiertas' ? 'selected' : ''; ?>>Inscripciones Abiertas</option>
                <option value="en_curso" <?php echo $datos['torneo']['estado'] === 'en_curso' ? 'selected' : ''; ?>>En Curso</option>
                <option value="finalizado" <?php echo $datos['torneo']['estado'] === 'finalizado' ? 'selected' : ''; ?>>Finalizado</option>
                <option value="cancelado" <?php echo $datos['torneo']['estado'] === 'cancelado' ? 'selected' : ''; ?>>Cancelado (Infracción de normas)</option>
            </select>
            <small class="texto-ayuda">El administrador tiene el poder de forzar el estado del torneo, por ejemplo, para cancelarlo por reportes.</small>
        </div>
        
        <div class="acciones-formulario">
            <button type="submit" class="btn-primario">
                <i class="fa-solid fa-save"></i> Guardar Cambios
            </button>
        </div>
    </form>
</div>
