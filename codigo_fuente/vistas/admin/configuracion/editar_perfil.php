<div class="header" style="margin-bottom: 2rem;">
    <h1 style="margin: 0; color: var(--color-texto-principal); font-size: 1.8rem;">Editar Perfil del Administrador</h1>
    <p style="color: var(--color-texto-secundario); margin-top: 0.5rem;">Mantén actualizada tu información de contacto personal.</p>
</div>

<div class="tarjeta" style="max-width: 600px;">
    <form action="<?php echo URL_BASE; ?>index.php?c=admin&a=editarPerfil" method="POST" class="formulario-estandar validacion-activa">
        <input type="hidden" name="csrf_token" value="<?php echo \App\Ayudantes\Sesion::generarTokenCSRF(); ?>">
        
        <div class="grupo-formulario">
            <label for="nombre_completo">Nombre Completo <span style="color: red;">*</span></label>
            <input type="text" id="nombre_completo" name="nombre_completo" class="campo-requerido" value="<?php echo htmlspecialchars($datosUsuario['nombre_completo'] ?? ''); ?>" required>
        </div>
        
        <div class="grupo-formulario">
            <label for="telefono">Teléfono de Contacto</label>
            <input type="tel" id="telefono" name="telefono" value="<?php echo htmlspecialchars($datosUsuario['telefono'] ?? ''); ?>" placeholder="+598 99 123 456">
        </div>

        <hr style="margin: 2rem 0; border: none; border-top: 1px solid var(--color-borde);">

        <h3 style="margin-top: 0; margin-bottom: 1rem; color: var(--color-texto-principal); font-size: 1.1rem;">Datos Inmodificables (Seguridad)</h3>
        
        <div class="grupo-formulario">
            <label>Correo Electrónico (Login)</label>
            <input type="text" value="<?php echo htmlspecialchars($datosUsuario['email'] ?? ''); ?>" disabled style="background-color: var(--color-fondo-principal); cursor: not-allowed; color: var(--color-texto-secundario);">
            <small style="display: block; margin-top: 0.5rem; color: var(--color-texto-secundario);">Si necesitas cambiar tu correo de acceso, por favor hazlo directamente desde la base de datos por motivos de seguridad.</small>
        </div>

        <div class="grupo-formulario">
            <label>Nivel de Permisos</label>
            <input type="text" value="Administrador General (Nivel 1)" disabled style="background-color: var(--color-fondo-principal); cursor: not-allowed; color: var(--color-texto-secundario);">
        </div>

        <div style="margin-top: 2rem; display: flex; justify-content: flex-end;">
            <button type="submit" class="btn btn-primario">
                <i class="fa-solid fa-save"></i> Guardar Cambios
            </button>
        </div>
    </form>
</div>
