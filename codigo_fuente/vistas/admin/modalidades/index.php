<?php
/**
 * Vista: Gestión de Módulos (Modalidades)
 * Permite al administrador apagar o prender formatos enteros a nivel global.
 * 
 * @var array $formatos
 */
?>
<div class="modalidades-container">
    <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/admin/admin_modalidades.css">
    <div class="modalidades-header">
        <h2>🛠️ Gestión de Módulos de Torneo (Feature Flags)</h2>
        <p>Activa o desactiva formatos enteros de la plataforma. Si desactivas un formato, los organizadores no podrán seleccionarlo, permitiendo realizar mantenimientos programados de forma segura.</p>
    </div>

    <?php if(isset($_SESSION['mensaje_exito'])): ?>
        <div style="padding: 10px; background-color: #d4edda; color: #155724; border-radius: 5px; margin-bottom: 15px;">
            <?php echo $_SESSION['mensaje_exito']; unset($_SESSION['mensaje_exito']); ?>
        </div>
    <?php endif; ?>

    <form action="<?php echo URL_BASE; ?>index.php?c=formato&a=guardarEstados" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo \App\Ayudantes\Sesion::generarTokenCSRF(); ?>">
        <div class="modalidades-list">
            <?php foreach ($formatos as $f): ?>
                <div class="modalidad-card">
                    <div class="modalidad-info">
                        <h3><?php echo htmlspecialchars($f['nombre']); ?></h3>
                        <p><?php echo htmlspecialchars($f['descripcion']); ?></p>
                    </div>
                    
                    <div class="modalidad-action">
                        <label class="switch">
                            <input type="checkbox" name="formatos[]" value="<?php echo htmlspecialchars($f['codigo']); ?>" <?php echo ($f['activo'] == 1) ? 'checked' : ''; ?>>
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div style="margin-top: 20px; text-align: right;">
            <button type="submit" style="padding: 10px 20px; background-color: #007BFF; color: white; border: none; border-radius: 5px; font-weight: bold; cursor: pointer; transition: background 0.3s;">
                💾 Guardar Cambios
            </button>
        </div>
    </form>
</div>
