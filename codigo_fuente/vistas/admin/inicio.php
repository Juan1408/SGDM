<?php
    /**
     * @var array $datos Contiene los datos pasados desde el AdminControlador
     * Sin esta declaracion en la linea 18 $datos nos daria error al intentar acceder a $datos['totalUsuarios'];
     */
?>

<section class="modulo-admin">
    <h2 class="titulo-pantalla">Resumen del Sistema</h2>

    <!--SECCION DE ESTADISTICAS GLOBALES-->
    <section class="contenedor-tarjetas">
        <article class="tarjeta-estadistica">
            <h3>Organizadores Totales</h3>
            <p><?php echo $datos['totalOrganizadores']; ?></p>
        </article>
        <article class="tarjeta-estadistica">
            <h3>Torneos Activos</h3>
            <p><?php echo $datos['totalTorneos']; ?></p>
        </article>
        <article class="tarjeta-estadistica">
            <h3>Jugadores Totales</h3>
            <p><?php echo $datos['totalJugadores']; ?></p>
        </article>
        <article class="tarjeta-estadistica">
            <h3>Equipos Totales</h3>
            <p><?php echo $datos['totalEquipos']; ?></p>
        </article>
    </section>

    <div>

        <!--SECCION DE ULTIMOS TORNEOS CREADOS-->
        <section>
            <h3 class="subtitulo-panel">Últimos Torneos Creados</h3>
            <table class="tabla-estandar">
                <thead>
                    <tr>
                        <th>Nombre del Torneo</th>
                        <th>Estado</th>
                        <th>Fecha de Inicio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($datos['ultimosTorneos'] as $torneo): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($torneo['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($torneo['estado']); ?></td>
                        <td><?php echo htmlspecialchars($torneo['fecha_inicio'] ?? '—'); ?></td>
                        <td><a href="<?php echo URL_BASE; ?>index.php?c=torneoAdmin&a=index" class="boton-accion">Ver Directorio</a></td>
                    </tr>
                    <?php endforeach; ?>

                    <?php if (empty($datos['ultimosTorneos'])): ?>
                    <tr>
                        <td colspan="4">No hay torneos registrados.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

        <!--SECCION DE EQUIPOS REGISTRADOS-->
        <section class="seccion-espaciada">
            <h3 class="subtitulo-panel">Últimos Equipos Registrados</h3>
            <table class="tabla-estandar">
                <thead>
                    <tr>
                        <th>Nombre del Equipo</th>
                        <th>Líder</th>
                        <th>Miembros</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($datos['ultimosEquipos'] as $equipo): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($equipo['nombre_equipo']); ?></td>
                        <td><?php echo htmlspecialchars($equipo['creado_por']); ?></td>
                        <td><?php echo (int)$equipo['total_miembros']; ?></td>
                        <td><a href="<?php echo URL_BASE; ?>index.php?c=equipoAdmin&a=index" class="boton-accion">Ver Directorio</a></td>
                    </tr>
                    <?php endforeach; ?>

                    <?php if (empty($datos['ultimosEquipos'])): ?>
                    <tr>
                        <td colspan="4">No hay equipos registrados.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

        <!--SECCION DE JUGADORES REGISTRADOS-->
        <section class="seccion-espaciada">
            <h3 class="subtitulo-panel">Últimos Jugadores Registrados</h3>
            <table class="tabla-estandar">
                <thead>
                    <tr>
                        <th>Nombre Completo</th>
                        <th>Correo Electrónico</th>
                        <th>Fecha de Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($datos['ultimosJugadores'] as $jugador): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($jugador['nombre_completo']); ?></td>
                        <td><?php echo htmlspecialchars($jugador['email']); ?></td>
                        <td><?php echo htmlspecialchars($jugador['fecha_registro']); ?></td>
                        <td>
                            <form method="POST" action="<?php echo URL_BASE; ?>index.php?c=usuario&a=verComo&id=<?php echo (int)$jugador['id']; ?>" style="display:inline-block;">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8'); ?>">
                                <button type="submit" class="boton-accion">Ver como</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <?php if (empty($datos['ultimosJugadores'])): ?>
                    <tr>
                        <td colspan="4">No hay jugadores registrados.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

        <!--SECCION DE ULTIMOS ORGANIZADORES REGISTRADOS-->
        <section class="seccion-espaciada">
            <h3 class="subtitulo-panel">Últimos Organizadores Registrados</h3>
            <table class="tabla-estandar">
                <thead>
                    <tr>
                        <th>Nombre Completo</th>
                        <th>Correo Electrónico</th>
                        <th>Fecha de Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($datos['ultimosOrganizadores'] as $organizador): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($organizador['nombre_completo']); ?></td>
                        <td><?php echo htmlspecialchars($organizador['email']); ?></td>
                        <td><?php echo htmlspecialchars($organizador['fecha_registro']); ?></td>
                        <td>
                            <form method="POST" action="<?php echo URL_BASE; ?>index.php?c=usuario&a=verComo&id=<?php echo (int)$organizador['id']; ?>" style="display:inline-block;">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(\App\Ayudantes\Sesion::generarTokenCSRF(), ENT_QUOTES, 'UTF-8'); ?>">
                                <button type="submit" class="boton-accion">Ver como</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <?php if (empty($datos['ultimosOrganizadores'])): ?>
                    <tr>
                        <td colspan="4">No hay organizadores registrados.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>
    </div>
</section>