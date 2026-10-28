<?php
/**
 * @var array $datos
 */
//Extraemos el usuario y el rol
$usuario = $datos['usuario'];
$esAdmin = $datos['es_admin'];
$origen = $datos['origen'] ?? '';
$volverUrl = $datos['volver_url'] ?? (URL_BASE . 'index.php?c=usuario&a=index');
$impacto = $datos['impacto_rol'] ?? ['torneos' => 0, 'equipos' => 0, 'capitanias' => 0];
$totalImpacto = array_sum($impacto);

?>

<section class="modulo-admin">
    <div class="encabezado-seccion">
        <h2 class="titulo-pantalla"><i class="fas fa-user-edit"></i> Editar usuario: <?php echo htmlspecialchars($usuario['nombre_completo'], ENT_QUOTES, 'UTF-8'); ?></h2>
    </div>

    <form action="<?php echo URL_BASE; ?>index.php?c=usuario&a=actualizarUsuario" method="POST" class="formulario-estandar">
        <fieldset>
    <!-- Campos ocultos-->
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="id" value="<?php echo (int) $usuario['id']; ?>">
    <input type="hidden" name="origen" value="<?php echo htmlspecialchars($origen, ENT_QUOTES, 'UTF-8'); ?>">

    <!-- Campo para el nombre completo -->
    <div class="grupo-formulario">
        <label for="nombre_completo">Nombre Completo</label>

        <!-- Usamos text para que acepte cualquier caracter-->
        <input type="text" id="nombre_completo" name="nombre_completo" 
        value="<?php echo htmlspecialchars($usuario['nombre_completo'], ENT_QUOTES, 'UTF-8'); ?>"
        required>        
    </div>

    <div class="grupo-formulario">
        <label for="email">Correo Electrónico</label>
        <input type="email" id="email" value="<?php echo htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8'); ?>" readonly disabled>
        <span class="texto-ayuda">
            El correo no se modifica desde el panel administrativo: cambiarlo invalidaría la verificación del usuario.
        </span>
    </div>

    <div class="grupo-formulario">
        <label for="rol_id">Rol del Sistema</label>
        <select name="rol_id" id="rol_id" required>
            <option value="">Seleccione un rol</option>
            <?php
            //Recorremos los roles que vienen en $datos
            $roles = $datos['roles'];
            foreach ($roles as $rol) {
                // Marcamos el rol actual del usuario
                $seleccionado = ($rol['id'] == $usuario['rol_id']) ? 'selected' : '';
                echo "<option value='" . (int) $rol['id'] . "' {$seleccionado}>" . htmlspecialchars($rol['nombre_rol'], ENT_QUOTES, 'UTF-8') . "</option>";
            }
            ?>
        </select>
        <span class="error-mensaje" id="rol-error"></span>

        <?php if ($totalImpacto > 0): ?>
            <p class="texto-ayuda">
                Este usuario tiene <?php echo (int) $impacto['torneos']; ?> torneos organizados,
                <?php echo (int) $impacto['equipos']; ?> equipos creados y
                <?php echo (int) $impacto['capitanias']; ?> capitanías.
                Cambiar el rol no transfiere esas responsabilidades: revisalas antes de guardar.
            </p>
        <?php endif; ?>
    </div>

    <div class="grupo-formulario">
        <label style="display:flex; gap:8px; align-items:flex-start;">
            <input type="checkbox" name="confirmar_admin" id="confirmar_admin" value="1">
            <span>
                Confirmo que quiero otorgar el rol <strong>Administrador</strong>.
                Es obligatorio solo cuando el rol seleccionado es Administrador.
            </span>
        </label>
    </div>

    <div class="acciones-formulario">
        <a href="<?php echo htmlspecialchars($volverUrl, ENT_QUOTES, 'UTF-8'); ?>" class="boton-secundario">Cancelar</a>
        <button type="submit" class="boton-accion">Guardar Cambios</button>
    </div>
        </fieldset>
    </form>
</section>