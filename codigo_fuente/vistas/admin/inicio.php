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
                        <td><?php echo $torneo['nombre']; ?></td>
                        <td><?php echo $torneo['estado']; ?></td>
                        <td><?php echo $torneo['fecha_inicio']; ?></td>
                        <td><a href="#torneos/configurar" class="boton-accion">Ver Detalles</a></td>
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
            <h3 class="subtitulo-panel">Ultimos Equipos Registrados</h3>
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
                        <td><?php echo $equipo['nombre_equipo']; ?></td>
                        <td><?php echo $equipo['creado_por']; ?></td>
                        <td><?php echo $equipo['total_miembros']; ?></td>
                        <td><a href="#" class="boton-accion">Ver como</a></td>
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
            <h3 class="subtitulo-panel">Ultimos Jugadores Registrados</h3>
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
                        <td><?php echo $jugador['nombre_completo']; ?></td>
                        <td><?php echo $jugador['email']; ?></td>
                        <td><?php echo $jugador['fecha_registro']; ?></td>
                        <td><a href="../jugador/perfil-jugador.html" class="boton-accion">Ver como</a></td>
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
            <h3 class="subtitulo-panel">Ultimos Organizadores Registrados</h3>
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
                        <td><?php echo $organizador['nombre_completo']; ?></td>
                        <td><?php echo $organizador['email']; ?></td>
                        <td><?php echo $organizador['fecha_registro']; ?></td>
                        <td><a href="../organizador/dashboard.html" class="boton-accion">Ver como</a></td>
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