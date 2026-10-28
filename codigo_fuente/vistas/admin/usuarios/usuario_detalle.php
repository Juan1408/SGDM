<?php
/**
 * Ficha administrativa de un usuario (solo lectura).
 * Vista inyectada por UsuarioControlador::ver().
 *
 * Datos personales y de auditoría para control interno del administrador.
 * Nunca se reciben ni muestran contraseñas, hashes ni tokens de sesión.
 *
 * @var array $datos
 */
$usuario = $datos['usuario'];
$perfilJugador = $datos['perfil_jugador'];
$perfilOrganizador = $datos['perfil_organizador'];
$equipos = $datos['equipos'];
$participaciones = $datos['participaciones'];
$accesos = $datos['accesos'];
$resumenAccesos = $datos['resumen_accesos'];
$sesiones = $datos['sesiones'];
$actividad = $datos['actividad'];
$metricas = $datos['metricas'];
$esMiCuenta = !empty($datos['es_mi_cuenta']);

$rolId = (int) $usuario['rol_id'];
$volverUrl = $datos['volver_url'] ?? '';
if ($volverUrl === '') {
    if ($rolId === 3) {
        $volverUrl = URL_BASE . 'index.php?c=jugador&a=index';
    } elseif ($rolId === 2) {
        $volverUrl = URL_BASE . 'index.php?c=organizador&a=index';
    } else {
        $volverUrl = URL_BASE . 'index.php?c=usuario&a=index';
    }
}

$foto = trim((string) ($usuario['foto_perfil_url'] ?? ''));
$fotoSrc = '';
if ($foto !== '') {
    $fotoSrc = preg_match('#^https?://#i', $foto) ? $foto : URL_BASE . ltrim($foto, '/');
}

$formatearFecha = function ($valor, string $formato = 'd/m/Y H:i') {
    if (empty($valor)) {
        return '—';
    }
    $marca = strtotime((string) $valor);
    return $marca === false ? '—' : date($formato, $marca);
};

$recortar = function ($texto, int $largo = 60) {
    $texto = trim((string) $texto);
    if ($texto === '') {
        return '—';
    }
    return mb_strlen($texto, 'UTF-8') > $largo
        ? mb_substr($texto, 0, $largo, 'UTF-8') . '…'
        : $texto;
};

$etiquetaInscripcion = [
    'pendiente' => ['Pendiente', 'estado-pendiente'],
    'confirmado' => ['Confirmado', 'estado-exito'],
    'rechazado' => ['Rechazado', 'estado-fallido'],
    'cancelado' => ['Cancelado', 'estado-cancelado'],
];

$etiquetaTorneo = [
    'borrador' => 'Borrador',
    'inscripciones_abiertas' => 'Inscripciones abiertas',
    'en_curso' => 'En curso',
    'finalizado' => 'Finalizado',
    'cancelado' => 'Cancelado',
];
?>

<div class="ficha-encabezado">
    <div>
        <a href="<?php echo $volverUrl; ?>" class="boton-secundario">
            <i class="fa-solid fa-arrow-left"></i> Volver al listado
        </a>
        <?php if (!$esMiCuenta): ?>
            <a href="<?php echo URL_BASE; ?>index.php?c=usuario&a=editarUsuario&id=<?php echo (int) $usuario['id']; ?>&origen=ficha"
               class="boton-accion">
                <i class="fa-solid fa-user-edit"></i> Editar nombre o rol
            </a>
        <?php endif; ?>
    </div>
    <?php if ($esMiCuenta): ?>
        <span class="ficha-aviso ficha-aviso-neutro">
            <i class="fa-solid fa-user-shield"></i> Esta es tu propia cuenta
        </span>
    <?php endif; ?>
</div>

<!-- Identidad -->
<section class="ficha-hero">
    <div class="ficha-hero-escudo ficha-hero-avatar">
        <?php if ($fotoSrc !== ''): ?>
            <img src="<?php echo htmlspecialchars($fotoSrc, ENT_QUOTES, 'UTF-8'); ?>"
                 alt="Foto de <?php echo htmlspecialchars($usuario['nombre_completo'], ENT_QUOTES, 'UTF-8'); ?>">
        <?php else: ?>
            <span class="ficha-hero-escudo-vacio">
                <?php echo htmlspecialchars(mb_strtoupper(mb_substr($usuario['nombre_completo'], 0, 1, 'UTF-8'), 'UTF-8'), ENT_QUOTES, 'UTF-8'); ?>
            </span>
        <?php endif; ?>
    </div>

    <div class="ficha-hero-datos">
        <div class="ficha-hero-titulo">
            <h2><?php echo htmlspecialchars($usuario['nombre_completo'], ENT_QUOTES, 'UTF-8'); ?></h2>
            <span class="badge"><?php echo htmlspecialchars($usuario['nombre_rol'], ENT_QUOTES, 'UTF-8'); ?></span>
            <?php if ((int) $usuario['esta_activo'] === 1): ?>
                <span class="badge badge-success">Activo</span>
            <?php else: ?>
                <span class="badge badge-danger">Bloqueado</span>
            <?php endif; ?>
        </div>

        <p class="ficha-hero-disciplina">
            <i class="fa-solid fa-envelope"></i>
            <?php echo htmlspecialchars($usuario['email'], ENT_QUOTES, 'UTF-8'); ?>
            <span class="ficha-separador">·</span>
            <i class="fa-solid fa-hashtag"></i> ID <?php echo (int) $usuario['id']; ?>
        </p>

        <dl class="ficha-meta">
            <div>
                <dt>Teléfono de contacto</dt>
                <dd><?php echo htmlspecialchars($usuario['telefono'] ?: '—', ENT_QUOTES, 'UTF-8'); ?></dd>
            </div>
            <div>
                <dt>Fecha de registro</dt>
                <dd><?php echo $formatearFecha($usuario['fecha_registro']); ?></dd>
            </div>
            <div>
                <dt>Último acceso</dt>
                <dd><?php echo $formatearFecha($usuario['ultimo_acceso']); ?></dd>
            </div>
            <div>
                <dt>Email verificado</dt>
                <dd><?php echo (int) $usuario['email_verificado'] === 1 ? 'Sí' : 'No'; ?></dd>
            </div>
            <div>
                <dt>Último intento fallido</dt>
                <dd><?php echo $formatearFecha($resumenAccesos['ultimo_fallido']); ?></dd>
            </div>
        </dl>
    </div>
</section>

<!-- Resumen -->
<section class="ficha-resumen" aria-label="Resumen del usuario">
    <div class="ficha-tarjeta">
        <span class="ficha-tarjeta-numero"><?php echo (int) $metricas['equipos_activos']; ?></span>
        <span class="ficha-tarjeta-etiqueta">Equipos activos</span>
    </div>
    <div class="ficha-tarjeta">
        <span class="ficha-tarjeta-numero"><?php echo (int) $metricas['equipos_creados']; ?></span>
        <span class="ficha-tarjeta-etiqueta">Equipos creados</span>
    </div>
    <div class="ficha-tarjeta">
        <span class="ficha-tarjeta-numero"><?php echo (int) $metricas['torneos_organizados']; ?></span>
        <span class="ficha-tarjeta-etiqueta">Torneos organizados</span>
    </div>
    <div class="ficha-tarjeta">
        <span class="ficha-tarjeta-numero"><?php echo (int) $resumenAccesos['exitosos']; ?></span>
        <span class="ficha-tarjeta-etiqueta">Accesos exitosos</span>
    </div>
    <div class="ficha-tarjeta">
        <span class="ficha-tarjeta-numero"><?php echo (int) $resumenAccesos['fallidos']; ?></span>
        <span class="ficha-tarjeta-etiqueta">Intentos fallidos</span>
    </div>
    <div class="ficha-tarjeta">
        <span class="ficha-tarjeta-numero"><?php echo count($participaciones); ?></span>
        <span class="ficha-tarjeta-etiqueta">Participaciones</span>
    </div>
</section>

<p class="ficha-nota-legal">
    <i class="fa-solid fa-lock"></i>
    Información personal y de auditoría mostrada con fines de control interno de la plataforma.
    No se incluyen contraseñas, hashes ni tokens de sesión.
</p>

<!-- Perfil especializado -->
<section class="ficha-seccion">
    <header class="ficha-seccion-cabecera">
        <h3><i class="fa-solid fa-id-card"></i> Perfil especializado</h3>
    </header>

    <?php if ($rolId === 2 && $perfilOrganizador): ?>
        <dl class="ficha-meta ficha-meta-perfil">
            <div>
                <dt>Nombre de la organización</dt>
                <dd><?php echo htmlspecialchars($perfilOrganizador['nombre_organizacion'], ENT_QUOTES, 'UTF-8'); ?></dd>
            </div>
            <div>
                <dt>Verificación oficial</dt>
                <dd>
                    <?php if ((int) $perfilOrganizador['verificado_oficial'] === 1): ?>
                        <span class="badge badge-success">Verificado</span>
                    <?php else: ?>
                        <span class="badge badge-danger">No verificado</span>
                    <?php endif; ?>
                </dd>
            </div>
            <div>
                <dt>Localidad</dt>
                <dd><?php echo htmlspecialchars($perfilOrganizador['localidad'] ?: '—', ENT_QUOTES, 'UTF-8'); ?></dd>
            </div>
            <div>
                <dt>Teléfono de contacto</dt>
                <dd><?php echo htmlspecialchars($perfilOrganizador['telefono_contacto'] ?: '—', ENT_QUOTES, 'UTF-8'); ?></dd>
            </div>
            <div>
                <dt>Sitio web</dt>
                <dd><?php echo htmlspecialchars($perfilOrganizador['sitio_web'] ?: '—', ENT_QUOTES, 'UTF-8'); ?></dd>
            </div>
            <div>
                <dt>Última actualización</dt>
                <dd><?php echo $formatearFecha($perfilOrganizador['actualizado_en']); ?></dd>
            </div>
            <?php if (!empty($perfilOrganizador['bio_organizacion'])): ?>
                <div class="ficha-meta-ancho">
                    <dt>Descripción</dt>
                    <dd><?php echo nl2br(htmlspecialchars($perfilOrganizador['bio_organizacion'], ENT_QUOTES, 'UTF-8')); ?></dd>
                </div>
            <?php endif; ?>
        </dl>
    <?php elseif ($rolId === 3 && $perfilJugador): ?>
        <dl class="ficha-meta ficha-meta-perfil">
            <div>
                <dt>Apodo / gamertag</dt>
                <dd><?php echo htmlspecialchars($perfilJugador['apodo_gamertag'] ?: '—', ENT_QUOTES, 'UTF-8'); ?></dd>
            </div>
            <div>
                <dt>Nivel</dt>
                <dd><?php echo (int) $perfilJugador['nivel']; ?></dd>
            </div>
            <div>
                <dt>Experiencia</dt>
                <dd><?php echo (int) $perfilJugador['experiencia_puntos']; ?> puntos</dd>
            </div>
            <div>
                <dt>País</dt>
                <dd><?php echo htmlspecialchars($perfilJugador['pais'] ?: '—', ENT_QUOTES, 'UTF-8'); ?></dd>
            </div>
            <div>
                <dt>Ciudad</dt>
                <dd><?php echo htmlspecialchars($perfilJugador['ciudad'] ?: '—', ENT_QUOTES, 'UTF-8'); ?></dd>
            </div>
            <div>
                <dt>Fecha de nacimiento</dt>
                <dd><?php echo $formatearFecha($perfilJugador['fecha_nacimiento'], 'd/m/Y'); ?></dd>
            </div>
            <div>
                <dt>Discord</dt>
                <dd><?php echo htmlspecialchars($perfilJugador['discord_tag'] ?: '—', ENT_QUOTES, 'UTF-8'); ?></dd>
            </div>
            <div>
                <dt>Última actualización</dt>
                <dd><?php echo $formatearFecha($perfilJugador['actualizado_en']); ?></dd>
            </div>
            <?php if (!empty($perfilJugador['bio'])): ?>
                <div class="ficha-meta-ancho">
                    <dt>Biografía</dt>
                    <dd><?php echo nl2br(htmlspecialchars($perfilJugador['bio'], ENT_QUOTES, 'UTF-8')); ?></dd>
                </div>
            <?php endif; ?>
        </dl>
    <?php else: ?>
        <p class="ficha-vacio">Este usuario no tiene un perfil especializado registrado para su rol.</p>
    <?php endif; ?>
</section>

<!-- Equipos -->
<section class="ficha-seccion">
    <header class="ficha-seccion-cabecera">
        <h3><i class="fa-solid fa-users"></i> Equipos (<?php echo count($equipos); ?>)</h3>
        <p class="texto-ayuda">Incluye membresías inactivas para conservar el historial de auditoría.</p>
    </header>

    <?php if (empty($equipos)): ?>
        <p class="ficha-vacio">El usuario no pertenece a ningún equipo.</p>
    <?php else: ?>
        <table class="tabla-estandar">
            <thead>
                <tr>
                    <th>Equipo</th>
                    <th>Rol en el equipo</th>
                    <th>Posición</th>
                    <th>Dorsal</th>
                    <th>Membresía</th>
                    <th>Desde</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($equipos as $equipo): ?>
                    <tr>
                        <td>
                            <a href="<?php echo URL_BASE; ?>index.php?c=equipoAdmin&a=ver&id=<?php echo (int) $equipo['id']; ?>">
                                <?php echo htmlspecialchars($equipo['nombre_equipo'], ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                            <?php if ((int) $equipo['activo'] !== 1): ?>
                                <span class="badge badge-danger">Equipo inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ((int) $equipo['es_capitan'] === 1): ?>
                                <span class="badge badge-success">Capitán</span>
                            <?php else: ?>
                                Miembro
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($equipo['posicion'] ?: '—', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo $equipo['numero_camiseta'] !== null ? (int) $equipo['numero_camiseta'] : '—'; ?></td>
                        <td>
                            <?php if ((int) $equipo['membresia_activa'] === 1): ?>
                                <span class="estado-exito">Activa</span>
                            <?php else: ?>
                                <span class="estado-cancelado">Inactiva</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $formatearFecha($equipo['fecha_union'], 'd/m/Y'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<!-- Participaciones individuales -->
<section class="ficha-seccion">
    <header class="ficha-seccion-cabecera">
        <h3><i class="fa-solid fa-trophy"></i> Participaciones individuales (<?php echo count($participaciones); ?>)</h3>
        <p class="texto-ayuda">Inscripciones del usuario como competidor individual, no como parte de un equipo.</p>
    </header>

    <?php if (empty($participaciones)): ?>
        <p class="ficha-vacio">El usuario no tiene participaciones individuales en torneos.</p>
    <?php else: ?>
        <table class="tabla-estandar">
            <thead>
                <tr>
                    <th>Torneo</th>
                    <th>Estado del torneo</th>
                    <th>Inscripción</th>
                    <th>PJ</th>
                    <th>G</th>
                    <th>E</th>
                    <th>P</th>
                    <th>Puntos</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($participaciones as $participacion): ?>
                    <?php
                        $estadoIns = $etiquetaInscripcion[$participacion['estado']] ?? [$participacion['estado'], ''];
                        $estadoTor = $etiquetaTorneo[$participacion['torneo_estado']] ?? $participacion['torneo_estado'];
                    ?>
                    <tr>
                        <td>
                            <span class="texto-negrita"><?php echo htmlspecialchars($participacion['torneo_nombre'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="ficha-subtexto">Inscripto el <?php echo $formatearFecha($participacion['fecha_inscripcion'], 'd/m/Y'); ?></span>
                        </td>
                        <td><?php echo htmlspecialchars($estadoTor, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><span class="<?php echo htmlspecialchars($estadoIns[1], ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($estadoIns[0], ENT_QUOTES, 'UTF-8'); ?></span></td>
                        <td><?php echo (int) ($participacion['partidos_jugados'] ?? 0); ?></td>
                        <td><?php echo (int) ($participacion['partidos_ganados'] ?? 0); ?></td>
                        <td><?php echo (int) ($participacion['partidos_empatados'] ?? 0); ?></td>
                        <td><?php echo (int) ($participacion['partidos_perdidos'] ?? 0); ?></td>
                        <td><?php echo (int) ($participacion['puntos'] ?? 0); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<!-- Accesos -->
<section class="ficha-seccion">
    <header class="ficha-seccion-cabecera">
        <h3><i class="fa-solid fa-right-to-bracket"></i> Intentos de acceso</h3>
        <p class="texto-ayuda">
            <?php echo (int) $resumenAccesos['total']; ?> registros en total:
            <?php echo (int) $resumenAccesos['exitosos']; ?> exitosos y
            <?php echo (int) $resumenAccesos['fallidos']; ?> fallidos. Se muestran los 10 más recientes.
        </p>
    </header>

    <?php if (empty($accesos)): ?>
        <p class="ficha-vacio">No hay intentos de acceso registrados para este usuario.</p>
    <?php else: ?>
        <table class="tabla-estandar">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Resultado</th>
                    <th>IP de origen</th>
                    <th>Detalle</th>
                    <th>Navegador / agente</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($accesos as $acceso): ?>
                    <tr>
                        <td><?php echo $formatearFecha($acceso['fecha_hora']); ?></td>
                        <td>
                            <?php if ((int) $acceso['exito'] === 1): ?>
                                <span class="estado-exito">Exitoso</span>
                            <?php else: ?>
                                <span class="estado-fallido">Fallido</span>
                            <?php endif; ?>
                        </td>
                        <td><code><?php echo htmlspecialchars($acceso['ip_origen'], ENT_QUOTES, 'UTF-8'); ?></code></td>
                        <td><?php echo htmlspecialchars($recortar($acceso['mensaje_error'], 50), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td title="<?php echo htmlspecialchars((string) $acceso['user_agent'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars($recortar($acceso['user_agent'], 60), ENT_QUOTES, 'UTF-8'); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<!-- Sesiones -->
<section class="ficha-seccion">
    <header class="ficha-seccion-cabecera">
        <h3><i class="fa-solid fa-desktop"></i> Sesiones registradas</h3>
        <p class="texto-ayuda">Se muestran las 10 más recientes. Por seguridad no se expone el identificador de sesión.</p>
    </header>

    <?php if (empty($sesiones)): ?>
        <p class="ficha-vacio">No hay sesiones registradas para este usuario.</p>
    <?php else: ?>
        <table class="tabla-estandar">
            <thead>
                <tr>
                    <th>Estado</th>
                    <th>IP de origen</th>
                    <th>Última actividad</th>
                    <th>Creada</th>
                    <th>Expira</th>
                    <th>Navegador / agente</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sesiones as $sesion): ?>
                    <?php
                        $vigente = (int) $sesion['activa'] === 1 && strtotime((string) $sesion['expira_en']) > time();
                        if ($vigente) {
                            $estadoSesion = ['Activa', 'estado-exito'];
                        } elseif ((int) $sesion['activa'] === 1) {
                            $estadoSesion = ['Expirada', 'estado-pendiente'];
                        } else {
                            $estadoSesion = ['Cerrada', 'estado-cancelado'];
                        }
                    ?>
                    <tr>
                        <td><span class="<?php echo $estadoSesion[1]; ?>"><?php echo $estadoSesion[0]; ?></span></td>
                        <td><code><?php echo htmlspecialchars($sesion['ip_origen'], ENT_QUOTES, 'UTF-8'); ?></code></td>
                        <td><?php echo $formatearFecha($sesion['ultima_actividad']); ?></td>
                        <td><?php echo $formatearFecha($sesion['creado_en']); ?></td>
                        <td><?php echo $formatearFecha($sesion['expira_en']); ?></td>
                        <td title="<?php echo htmlspecialchars((string) $sesion['user_agent'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars($recortar($sesion['user_agent'], 60), ENT_QUOTES, 'UTF-8'); ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<!-- Actividad -->
<section class="ficha-seccion">
    <header class="ficha-seccion-cabecera">
        <h3><i class="fa-solid fa-clock-rotate-left"></i> Actividad reciente</h3>
        <p class="texto-ayuda">Últimas 10 acciones del usuario registradas en el log de actividad.</p>
    </header>

    <?php if (empty($actividad)): ?>
        <p class="ficha-vacio">No hay actividad registrada para este usuario.</p>
    <?php else: ?>
        <table class="tabla-estandar">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Acción</th>
                    <th>Descripción</th>
                    <th>IP de origen</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($actividad as $registro): ?>
                    <tr>
                        <td><?php echo $formatearFecha($registro['fecha_hora']); ?></td>
                        <td><code><?php echo htmlspecialchars($registro['accion'], ENT_QUOTES, 'UTF-8'); ?></code></td>
                        <td><?php echo htmlspecialchars($recortar($registro['descripcion'], 90), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><code><?php echo htmlspecialchars($registro['ip_origen'] ?: '—', ENT_QUOTES, 'UTF-8'); ?></code></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
