<?php
/**
 * @var array $datos
 */
//Extraemos el usuario y el rol
$usuario = $datos['usuario'];
$esAdmin = $datos['es_admin'];

?>

<section class="modulo-admin">
    <h2 class="titulo-pantalla">Editar usuario: <?php echo $usuario['nombre_completo']; ?></h2>

    <form action="<?php echo URL_BASE; ?>index.php?c=usuario&a=actualizarUsuario" method="POST" class="formulario-estandar">

    <!-- Campos ocultos-->
    <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">

    <!-- Campo para el nombre completo -->
    <div class="grupo-formulario">
        <label for="nombre_completo">Nombre Completo</label>

        <!-- Usamos text para que acepte cualquier caracter-->
        <input type="text" id="nombre_completo" name="nombre_completo" 
        value="<?php echo $usuario['nombre_completo']; ?>"
        required>        
    </div>

    <div class="grupo-formulario">
        <label for="email">Correo Electronico</label>
        <input type="email" id="email" name="email" value="<?php echo $usuario['email']; ?>" required>
        <span class="error-mensaje" id="email-error"></span>
    </div>

    <div class="grupo-formulario">
        <label for="rol_id">Rol del Sisitema</label>
        <select name="rol_id" id="rol_id" required>
            <option value="">Seleccione un rol</option>
            <?php
            //Recorremos los roles que vienen en $datos
            $roles = $datos['roles'];
            foreach ($roles as $rol) {
                // Marcamos el rol actual del usuario
                $seleccionado = ($rol['id'] == $usuario['rol_id']) ? 'selected' : '';
                echo "<option value='{$rol['id']}' {$seleccionado}>{$rol['nombre_rol']}</option>";
            }
            ?>
        </select>
        <span class="error-mensaje" id="rol-error"></span>
    </div>

    <div class="acciones-formulario">
        <a href="<?php echo URL_BASE; ?>index.php?c=usuario&a=index" class="boton-secundario">Cancelar</a>
        <button type="submit" class="boton-accion">Guardar Cambios</button>
    </div>
    </form>
</section>