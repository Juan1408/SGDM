<?php
/**
 * ============================================================================
 * VISTA ADMIN: politicas_contrasenas.php
 * ============================================================================
 * Propósito: Permite al Administrador General configurar los parámetros
 *            globales de complejidad, vencimiento e historial de contraseñas.
 * Ubicación: codigo_fuente/vistas/admin/configuracion/politicas_contrasenas.php
 * ============================================================================
 * @var array $datos Datos inyectados desde PoliticaContrasenaControlador
 */
$politica = $datos['politica'] ?? [];
?>

<section class="modulo-admin">
    <div class="encabezado-seccion mb-4">
        <h2 class="titulo-pantalla"><i class="fa-solid fa-shield-halved text-primary me-2"></i>Políticas de Seguridad y Contraseñas</h2>
        <p class="texto-ayuda encabezado-descripcion">
            Define las reglas globales de complejidad y caducidad para todas las cuentas de usuario registradas en SGDM ASCEND.
        </p>
    </div>

    <form action="<?php echo URL_BASE; ?>index.php?c=politicaContrasena&a=guardar" method="POST" class="formulario-estandar validacion-activa">
        
        <!-- BLOQUE 1: COMPLEJIDAD DE LA CLAVE -->
        <fieldset class="mb-4">
            <legend><i class="fa-solid fa-key me-2"></i>Requisitos de Complejidad</legend>
            <p class="texto-ayuda">Configura los caracteres obligatorios que debe incluir la contraseña de un Jugador o Organizador al registrarse o cambiar su clave.</p>

            <div class="campo-grupo mb-3">
                <label for="longitud_minima" class="fw-bold">Longitud Mínima de Contraseña (Caracteres):</label>
                <input type="number" 
                       id="longitud_minima" 
                       name="longitud_minima" 
                       value="<?php echo htmlspecialchars($politica['longitud_minima'] ?? 8); ?>" 
                       min="4" 
                       max="64" 
                       required 
                       class="campo-requerido form-control style-input"
                       style="max-width: 200px;">
                <small class="texto-ayuda">Recomendado: 8 o más caracteres.</small>
            </div>

            <div class="opciones-checkbox mt-3">
                <label class="d-flex align-items-center mb-2 cursor-pointer">
                    <input type="checkbox" name="requiere_mayuscula" value="1" <?php echo !empty($politica['requiere_mayuscula']) ? 'checked' : ''; ?> class="me-2">
                    <span>Requerir al menos una letra mayúscula (A-Z)</span>
                </label>

                <label class="d-flex align-items-center mb-2 cursor-pointer">
                    <input type="checkbox" name="requiere_minuscula" value="1" <?php echo !empty($politica['requiere_minuscula']) ? 'checked' : ''; ?> class="me-2">
                    <span>Requerir al menos una letra minúscula (a-z)</span>
                </label>

                <label class="d-flex align-items-center mb-2 cursor-pointer">
                    <input type="checkbox" name="requiere_numero" value="1" <?php echo !empty($politica['requiere_numero']) ? 'checked' : ''; ?> class="me-2">
                    <span>Requerir al menos un número (0-9)</span>
                </label>

                <label class="d-flex align-items-center mb-2 cursor-pointer">
                    <input type="checkbox" name="requiere_caracter_especial" value="1" <?php echo !empty($politica['requiere_caracter_especial']) ? 'checked' : ''; ?> class="me-2">
                    <span>Requerir al menos un carácter especial (<code>!@#$%^&*()_+-=[]{}</code>)</span>
                </label>
            </div>
        </fieldset>

        <!-- BLOQUE 2: VENCIMIENTO E HISTORIAL -->
        <fieldset class="mb-4">
            <legend><i class="fa-solid fa-clock-rotate-left me-2"></i>Caducidad e Histórico de Claves</legend>
            <p class="texto-ayuda">Establece la vigencia de las claves y la cantidad de contraseñas anteriores para evitar su reutilización inmediata.</p>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="expiracion_dias" class="fw-bold">Expiración de Contraseña (Días):</label>
                    <input type="number" 
                           id="expiracion_dias" 
                           name="expiracion_dias" 
                           value="<?php echo htmlspecialchars($politica['expiracion_dias'] ?? 90); ?>" 
                           min="0" 
                           max="365" 
                           class="form-control style-input"
                           style="max-width: 200px;">
                    <small class="texto-ayuda">Ingresa <code>0</code> para desactivar la expiración automática.</small>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="historial_cantidad" class="fw-bold">Historial de Claves Anteriores:</label>
                    <input type="number" 
                           id="historial_cantidad" 
                           name="historial_cantidad" 
                           value="<?php echo htmlspecialchars($politica['historial_cantidad'] ?? 5); ?>" 
                           min="0" 
                           max="20" 
                           class="form-control style-input"
                           style="max-width: 200px;">
                    <small class="texto-ayuda">Cantidad de contraseñas pasadas que el usuario no podrá reutilizar.</small>
                </div>
            </div>
        </fieldset>

        <!-- INFORMACIÓN DE AUDITORÍA DE ÚLTIMA ACTUALIZACIÓN -->
        <?php if (!empty($politica['actualizado_en'])): ?>
            <div class="alerta-informacion mb-4 p-3 border rounded bg-light">
                <small class="text-muted">
                    <i class="fa-solid fa-circle-info me-1"></i>
                    Última modificación registrada: 
                    <strong><?php echo date('d/m/Y H:i', strtotime($politica['actualizado_en'])); ?></strong>
                    <?php if (!empty($politica['actualizado_por_nombre'])): ?>
                        por <strong><?php echo htmlspecialchars($politica['actualizado_por_nombre']); ?></strong> (<?php echo htmlspecialchars($politica['actualizado_por_email']); ?>)
                    <?php endif; ?>
                </small>
            </div>
        <?php endif; ?>

        <!-- BOTONES DE ACCIÓN -->
        <div class="grupo-botones d-flex gap-3">
            <button type="submit" class="boton-accion">
                <i class="fa-solid fa-floppy-disk me-2"></i>Guardar Políticas de Seguridad
            </button>
            <a href="<?php echo URL_BASE; ?>index.php?c=admin&a=dashboard" class="boton-accion boton-secundario">
                Cancelar
            </a>
        </div>
    </form>
</section>
