<?php 
    /**
     * @var Array $datos
     */
?>
<section class="modulo-admin">
    <h2 class="titulo-pantalla">Directorio de usuarios</h2>

    <section>
        <table class="tabla-estandar">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre Completo</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Fecha de Registro</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($datos['usuarios'] as $usuario):?>
                    <tr>
                        <td><?php echo $usuario['id']; ?></td>
                        <td><?php echo $usuario['nombre_completo']; ?></td>
                        <td><?php echo $usuario['email']; ?></td>
                        <td class="etiqueta-rol"><span><?php echo $usuario['nombre_rol']; ?></span></td>
                        <td><?php if ($usuario['esta_activo']): ?>
                            <span class="etiqueta-activo">Activo</span>
                         <?php else: ?>
                            <span class="etiqueta-inactivo">Bloqueado</span>
                         <?php endif; ?>
                        </td>
                        <td><?php echo $usuario['fecha_registro']; ?></td>

                        <td>
                            <a href="#" class="boton-accion">Editar</a>
                            <a href="#" class="boton-accion">Bloquear</a>
                        </td>
                    </tr>
                <?php endforeach; ?>    
            
                <?php if (empty($datos['usuarios'])):?>
                    <tr>
                        <td colspan="7" class="celda-vacia">No se encontraron usuarios registrados</td>
                    </tr>
                <?php endif; ?>


            </tbody>
        </table>
    </section>
    
    
</section>