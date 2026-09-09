<?php
/**
 * Vista: gestionar-equipo.php
 * Requiere que vengan cargados desde EquipoControlador (gestionar() / actualizarEquipo()):
 * $equipo, $miembros, $capitanes, $solicitudesPendientes
 * Acceso restringido: solo el capitan de este equipo puntual llega hasta aca.
 *
 * Usa las clases reales de gestionar-equipo.css (fila-jugador, boton-hacer-capitan,
 * boton-quitar-miembro, badge-lider/badge-capitan/badge-pendiente, etc.)
 */

$nombreEquipo = $equipo['nombre_equipo'];
$ubicacion = $equipo['ubicacion'] ?? '';
$anioFundacion = $equipo['anio_fundacion'] ?? '';
$descripcion = $equipo['descripcion'] ?? '';
$codigoInvitacion = $equipo['codigo_invitacion'] ?? '----';

//Chequeamos rapido, para cada miembro, si tambien es capitan (para mostrar la insignia en vez de los botones)
$idsCapitanes = array_column($capitanes, 'id');
$esCapitanFn = function($usuarioId) use ($idsCapitanes) {
   return in_array($usuarioId, $idsCapitanes);
};
?>
<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>ASCEND - Gestionar <?php echo htmlspecialchars($nombreEquipo); ?></title>

   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/variables.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/base.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/navbar.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/layouts/footer.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/jugador/crear-equipo.css">
   <link rel="stylesheet" href="<?php echo URL_BASE; ?>css/jugador/gestionar-equipo.css">

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
   <link rel="preconnect" href="https://fonts.googleapis.com">
   <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>

<body>

   <!-- NAVBAR ---------------------------->
    <?php include __DIR__ . '/../componentes/navbar-jugador.php'; ?>

   <main class="crear-equipo-pagina">

      <div id="vista-gestionar-equipo">

         <div class="crear-equipo-header">
            <span class="gestion-etiqueta">Líder del equipo</span>
            <h1 id="gestion-nombre-equipo"><?php echo htmlspecialchars($nombreEquipo); ?></h1>
            <p>Editá los datos de tu equipo, sumá jugadores nuevos y asigná un capitán.</p>
         </div>

         <?php if (!empty($error)): ?>
            <div class="mensaje-error"><?php echo htmlspecialchars($error); ?></div>
         <?php endif; ?>

         <?php if (isset($_GET['exito'])): ?>
            <div class="mensaje-exito">Datos del equipo actualizados.</div>
         <?php endif; ?>

         <div class="crear-equipo-formulario">

            <!-- ============================== -->
            <!-- EDITAR DATOS DEL EQUIPO        -->
            <!-- ============================== -->
            <form class="tarjeta-equipo" method="POST" action="<?php echo URL_BASE; ?>index.php?c=equipo&a=actualizarEquipo">
               <h2>Datos del equipo</h2>

               <input type="hidden" name="equipo_id" value="<?php echo (int) $equipo['id']; ?>">

               <div class="crear-equipo-imagenes">
                  <div class="campo-imagen campo-banner">
                     <div class="preview-banner" id="preview-banner">
                        <span class="placeholder-texto">Sin banner todavía</span>
                     </div>
                     <label class="boton-subir-imagen">
                        <i class="fa-solid fa-image"></i> Cambiar banner
                     </label>
                     <input type="file" accept="image/*" hidden disabled>
                  </div>

                  <div class="campo-imagen campo-escudo">
                     <div class="preview-escudo" id="preview-escudo">
                        <i class="fa-solid fa-shield-halved placeholder-icono"></i>
                     </div>
                     <label class="boton-subir-imagen">
                        <i class="fa-solid fa-camera"></i> Cambiar escudo
                     </label>
                     <input type="file" accept="image/*" hidden disabled>
                  </div>
                  <!-- Subida real de imagenes pendiente, mismo tema que en Crear Equipo -->
               </div>

               <div class="campo-grupo">
                  <label for="gestion-nombre">Nombre del equipo</label>
                  <input type="text" name="nombre_equipo" id="gestion-nombre" value="<?php echo htmlspecialchars($nombreEquipo); ?>" required maxlength="100">
               </div>

               <div class="campo-fila">
                  <div class="campo-grupo">
                     <label for="gestion-localidad">Localidad</label>
                     <input type="text" name="ubicacion" id="gestion-localidad" value="<?php echo htmlspecialchars($ubicacion); ?>" maxlength="100">
                  </div>

                  <div class="campo-grupo">
                     <label for="gestion-anio">Año de fundación</label>
                     <input type="number" name="anio_fundacion" id="gestion-anio" value="<?php echo htmlspecialchars($anioFundacion); ?>" min="1950" max="2026">
                  </div>
               </div>

               <div class="campo-grupo">
                  <label for="gestion-descripcion">Descripción</label>
                  <textarea name="descripcion" id="gestion-descripcion" rows="4"><?php echo htmlspecialchars($descripcion); ?></textarea>
               </div>

               <div class="campo-grupo">
                  <label>Código de invitación</label>
                  <div class="codigo-invitacion-box">
                     <span id="codigo-invitacion-texto"><?php echo htmlspecialchars($codigoInvitacion); ?></span>
                  </div>
               </div>

               <div class="crear-equipo-acciones">
                  <button type="submit" class="boton-registro">Guardar Cambios</button>
               </div>
            </form>

            <!-- ============================== -->
            <!-- MIEMBROS DEL EQUIPO            -->
            <!-- ============================== -->
            <section class="tarjeta-equipo">
               <h2>Miembros del equipo</h2>

               <?php if (empty($miembros)): ?>
                  <p class="lista-vacia">Este equipo todavía no tiene miembros.</p>
               <?php else: ?>
                  <div class="lista-jugadores" id="lista-miembros">
                     <?php foreach ($miembros as $miembro): ?>
                        <div class="fila-jugador">
                           <img class="fila-jugador-avatar"
                              src="<?php echo htmlspecialchars($miembro['foto_perfil_url'] ?: URL_BASE . 'img/avatars/a1.png'); ?>"
                              alt="<?php echo htmlspecialchars($miembro['nombre_completo']); ?>">

                           <div class="fila-jugador-info">
                              <span class="fila-jugador-nombre"><?php echo htmlspecialchars($miembro['nombre_completo']); ?></span>
                           </div>

                           <?php if ($esCapitanFn($miembro['id'])): ?>
                              <span class="fila-jugador-badge badge-capitan">Capitán</span>
                           <?php elseif ($miembro['id'] === $_SESSION['usuario_id']): ?>
                              <span class="fila-jugador-badge badge-lider">Vos</span>
                           <?php else: ?>
                              <div class="fila-jugador-acciones">
                                 <form method="POST" action="<?php echo URL_BASE; ?>index.php?c=equipo&a=hacerCapitan">
                                    <input type="hidden" name="equipo_id" value="<?php echo (int) $equipo['id']; ?>">
                                    <input type="hidden" name="usuario_id" value="<?php echo (int) $miembro['id']; ?>">
                                    <button type="submit" class="boton-hacer-capitan">Hacer Capitán</button>
                                 </form>
                                 <form method="POST" action="<?php echo URL_BASE; ?>index.php?c=equipo&a=expulsarMiembro">
                                    <input type="hidden" name="equipo_id" value="<?php echo (int) $equipo['id']; ?>">
                                    <input type="hidden" name="usuario_id" value="<?php echo (int) $miembro['id']; ?>">
                                    <button type="submit" class="boton-quitar-miembro">Quitar</button>
                                 </form>
                              </div>
                           <?php endif; ?>
                        </div>
                     <?php endforeach; ?>
                  </div>
               <?php endif; ?>
            </section>

            <!-- ============================== -->
            <!-- AGREGAR JUGADORES (busqueda en vivo con JS) -->
            <!-- ============================== -->
            <section class="tarjeta-equipo">
               <h2>Agregar jugadores</h2>

               <?php if (isset($_GET['invitacionEnviada'])): ?>
                  <div class="mensaje-exito">Invitación enviada.</div>
               <?php endif; ?>

               <div class="buscador-jugadores">
                  <i class="fa-solid fa-magnifying-glass"></i>
                  <input type="text" id="input-buscar-jugador" placeholder="Buscar por nombre o apodo..." data-equipo-id="<?php echo (int) $equipo['id']; ?>">
               </div>

               <div class="lista-jugadores" id="lista-resultados-busqueda">
                  <p class="lista-vacia">Escribí un nombre o apodo para buscar jugadores.</p>
               </div>
            </section>

            <!-- ============================== -->
            <!-- SOLICITUDES PENDIENTES (jugadores que pidieron entrar) -->
            <!-- ============================== -->
            <section class="tarjeta-equipo">
               <h2>Solicitudes pendientes</h2>

               <?php if (empty($solicitudesPendientes)): ?>
                  <p class="lista-vacia">No hay solicitudes pendientes.</p>
               <?php else: ?>
                  <div class="lista-jugadores" id="lista-solicitudes">
                     <?php foreach ($solicitudesPendientes as $solicitud): ?>
                        <div class="fila-jugador">
                           <img class="fila-jugador-avatar"
                              src="<?php echo htmlspecialchars($solicitud['foto_perfil_url'] ?: URL_BASE . 'img/avatars/a1.png'); ?>"
                              alt="<?php echo htmlspecialchars($solicitud['nombre_completo']); ?>">

                           <div class="fila-jugador-info">
                              <span class="fila-jugador-nombre"><?php echo htmlspecialchars($solicitud['nombre_completo']); ?></span>
                              <?php if (!empty($solicitud['mensaje'])): ?>
                                 <span class="fila-jugador-usuario">"<?php echo htmlspecialchars($solicitud['mensaje']); ?>"</span>
                              <?php endif; ?>
                           </div>

                           <span class="fila-jugador-badge badge-pendiente">Pendiente</span>

                           <div class="fila-jugador-acciones">
                              <form method="POST" action="<?php echo URL_BASE; ?>index.php?c=equipo&a=responderSolicitudRecibida">
                                 <input type="hidden" name="equipo_id" value="<?php echo (int) $equipo['id']; ?>">
                                 <input type="hidden" name="solicitud_id" value="<?php echo (int) $solicitud['id']; ?>">
                                 <input type="hidden" name="respuesta" value="aceptar">
                                 <button type="submit" class="boton-invitar">Aceptar</button>
                              </form>
                              <form method="POST" action="<?php echo URL_BASE; ?>index.php?c=equipo&a=responderSolicitudRecibida">
                                 <input type="hidden" name="equipo_id" value="<?php echo (int) $equipo['id']; ?>">
                                 <input type="hidden" name="solicitud_id" value="<?php echo (int) $solicitud['id']; ?>">
                                 <input type="hidden" name="respuesta" value="rechazar">
                                 <button type="submit" class="boton-cancelar-solicitud">Rechazar</button>
                              </form>
                           </div>
                        </div>
                     <?php endforeach; ?>
                  </div>
               <?php endif; ?>
            </section>

            <!-- ============================== -->
            <!-- DEJAR EL EQUIPO                -->
            <!-- ============================== -->
            <section class="tarjeta-equipo">
               <h2>Dejar el equipo</h2>

               <?php if (isset($_GET['errorDejar'])): ?>
                  <div class="mensaje-error"><?php echo htmlspecialchars($_GET['errorDejar']); ?></div>
               <?php endif; ?>

               <p>Si te vas, dejás de ser capitán y de formar parte de este equipo. Necesitás tener otro capitán ya asignado (usá "Hacer Capitán" en algún miembro antes de irte).</p>

               <form method="POST" action="<?php echo URL_BASE; ?>index.php?c=equipo&a=dejarEquipo"
                  onsubmit="return confirm('¿Seguro que querés dejar este equipo?');">
                  <input type="hidden" name="equipo_id" value="<?php echo (int) $equipo['id']; ?>">
                  <button type="submit" class="boton-quitar-miembro">Dejar el equipo</button>
               </form>
            </section>

         </div>

      </div>

   </main>

   <!-- FOOTER ---------------------------->
   <?php include __DIR__ . '/../componentes/footer.html'; ?>

   <script>window.URL_BASE_JS = "<?php echo URL_BASE; ?>";</script>
   <script src="<?php echo URL_BASE; ?>js/buscar-jugadores-vivo.js"></script>

</body>
</html>
