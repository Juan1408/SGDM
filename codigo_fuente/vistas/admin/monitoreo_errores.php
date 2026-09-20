<?php /** @var array $errores */ ?>
<!-- Estilos específicos de este módulo -->
<link rel="stylesheet" href="<?php echo URL_BASE; ?>css/admin/monitoreo_errores.css"><main class="contenedor-principal">
    
    <header class="header-modulo">
        <div class="titulo-modulo">
            <div class="pulse"></div>
            <h1>Monitoreo de Errores</h1>
        </div>
        <a href="index.php?c=admin&a=dashboard" class="btn-volver">Volver al Panel</a>
    </header>

    <!-- Barra de Filtros -->
    <section class="barra-filtros">
        <?php 
            $est = $_GET['estado'] ?? 'todos'; 
            $rol = $_GET['rol'] ?? 'todos';
            $tipo = $_GET['tipo'] ?? 'todos';
        ?>
        <form action="index.php" method="GET" class="form-filtros" id="form-filtros">
            <input type="hidden" name="c" value="errorTracking">
            <input type="hidden" name="a" value="dashboard">
            
            <div class="grupo-filtro">
                <label for="estado">Estado</label>
                <select name="estado" id="estado" onchange="document.getElementById('form-filtros').submit();">
                    <option value="todos" <?= $est==='todos'?'selected':'' ?>>Todos los estados</option>
                    <option value="nuevo" <?= $est==='nuevo'?'selected':'' ?>>Nuevos</option>
                    <option value="revision" <?= $est==='revision'?'selected':'' ?>>En Revisión</option>
                    <option value="resuelto" <?= $est==='resuelto'?'selected':'' ?>>Resueltos</option>
                </select>
            </div>

            <div class="grupo-filtro">
                <label for="rol">Rol Afectado</label>
                <select name="rol" id="rol" onchange="document.getElementById('form-filtros').submit();">
                    <option value="todos" <?= $rol==='todos'?'selected':'' ?>>Cualquier Rol</option>
                    <option value="jugador" <?= $rol==='jugador'?'selected':'' ?>>Jugadores</option>
                    <option value="organizador" <?= $rol==='organizador'?'selected':'' ?>>Organizadores</option>
                </select>
            </div>

            <div class="grupo-filtro">
                <label for="tipo">Tipo de Fallo</label>
                <select name="tipo" id="tipo" onchange="document.getElementById('form-filtros').submit();">
                    <option value="todos" <?= $tipo==='todos'?'selected':'' ?>>Todos los tipos</option>
                    <option value="base_de_datos" <?= $tipo==='base_de_datos'?'selected':'' ?>>Base de Datos</option>
                    <option value="codigo" <?= $tipo==='codigo'?'selected':'' ?>>Excepción / Código</option>
                </select>
            </div>
        </form>
    </section>

    <!-- Lista de Errores -->
    <section class="lista-errores">
        <?php if (empty($errores)): ?>
            <div class="estado-vacio">
                <i class="fa-solid fa-shield-check icono-vacio"></i>
                <h2>Sistema Estable</h2>
                <p>No se han encontrado errores con los filtros actuales.</p>
            </div>
        <?php else: ?>
            <?php foreach ($errores as $error): ?>
                
                <article class="tarjeta-error estado-<?= $error['estado'] ?>">
                    
                    <!-- Resumen del Error (Click para abrir Acordeón) -->
                    <div class="error-resumen" onclick="toggleAcordeon(this, <?= $error['id'] ?>)">
                        <div class="error-identificador">
                            <h3 class="error-mensaje"><?= htmlspecialchars(substr($error['mensaje'], 0, 80)) ?><?= strlen($error['mensaje']) > 80 ? '...' : '' ?></h3>
                            <div class="error-etiquetas">
                                <span class="etiqueta etiqueta-ocurrencias"><i class="fa-solid fa-fire"></i> <?= $error['cantidad_ocurrencias'] ?></span>
                                <span class="etiqueta etiqueta-<?= $error['estado'] ?>"><?= ucfirst($error['estado']) ?></span>
                                <span class="etiqueta"><i class="fa-solid fa-code"></i> <?= $error['tipo_error'] == 'base_de_datos' ? 'Base de Datos' : 'PHP' ?></span>
                            </div>
                        </div>
                        <div class="error-meta">
                            <span class="meta-fecha"><i class="fa-regular fa-clock"></i> <?= date('d/m H:i', strtotime($error['ultima_vez'])) ?></span>
                            <i class="fa-solid fa-chevron-down icono-desplegar"></i>
                        </div>
                    </div>

                    <!-- Cuerpo Desplegable (Acordeón) -->
                    <div class="error-detalles" id="detalle-<?= $error['id'] ?>">
                        <div class="detalles-contenido">
                            <p><strong>Ruta:</strong> <?= htmlspecialchars($error['archivo']) ?>:<?= $error['linea'] ?></p>
                            <p><strong>Afectado:</strong> <?= $error['usuario_email'] ? htmlspecialchars($error['usuario_email']) : 'Anónimo' ?> (<?= $error['rol_usuario'] ?? 'N/A' ?>)</p>
                            
                            <?php if (!empty($error['detalles_resolucion'])): ?>
                            <div style="background-color: var(--color-fondo-principal); padding: 0.75rem; border-left: 3px solid var(--color-exito); margin-top: 0.75rem; font-size: 0.9rem; color: var(--color-texto-principal); border-radius: 4px;">
                                <strong style="display:block; margin-bottom: 0.25rem; font-size: 0.8rem; color: var(--color-texto-secundario); text-transform: uppercase;">Resolución Implementada:</strong>
                                <?= nl2br(htmlspecialchars($error['detalles_resolucion'])) ?>
                            </div>
                            <?php endif; ?>

                            <!-- Botonera de Acciones -->
                            <div class="error-acciones">
                                <a href="index.php?c=errorTracking&a=verDetalle&id=<?= $error['id'] ?>" class="btn btn-primario"><i class="fa-solid fa-expand"></i> Pantalla Completa</a>
                                
                                <form method="POST" action="index.php?c=errorTracking&a=accionError" class="form-accion">
                                    <input type="hidden" name="id" value="<?= $error['id'] ?>">
                                    
                                    <?php if ($error['estado'] !== 'revision'): ?>
                                    <button type="submit" name="accion" value="cambiar_estado" class="btn btn-secundario" onclick="this.form.estado.value='revision';">
                                        <i class="fa-solid fa-magnifying-glass"></i> En Revisión
                                    </button>
                                    <?php endif; ?>
                                    
                                    <?php if ($error['estado'] !== 'resuelto'): ?>
                                    <button type="button" class="btn btn-exito" onclick="abrirModalResolucion(<?= $error['id'] ?>, '')">
                                        <i class="fa-solid fa-check"></i> Marcar Resuelto
                                    </button>
                                    <?php else: ?>
                                    <?php
                                        // Escapar el texto para que pueda ser pasado seguro como argumento en Javascript
                                        $resolucionLimpia = str_replace(["\r\n", "\r", "\n"], '\n', $error['detalles_resolucion'] ?? '');
                                        $resolucionJS = htmlspecialchars(addslashes($resolucionLimpia));
                                    ?>
                                    <button type="button" class="btn btn-exito" onclick="abrirModalResolucion(<?= $error['id'] ?>, '<?= $resolucionJS ?>')">
                                        <i class="fa-solid fa-pen"></i> Editar Resolución
                                    </button>
                                    <?php endif; ?>

                                    <button type="submit" name="accion" value="eliminar" class="btn btn-peligro btn-icon" title="Eliminar Registro" onclick="return confirm('¿Seguro que quieres eliminar este log de error?');">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>

                                    <!-- Campo oculto para el estado si no es resuelto (lo maneja el JS si abre el modal) -->
                                    <input type="hidden" name="estado" value="">
                                </form>
                            </div>
                        </div>
                    </div>
                </article>

            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</main>

<!-- Modal de Resolución de Error -->
<div id="modal-resolucion" class="modal-overlay">
    <div class="modal-contenido">
        <div class="modal-cabecera">
            <h2>Resolver Error</h2>
            <button type="button" class="btn-cerrar" onclick="cerrarModal()">&times;</button>
        </div>
        <form method="POST" action="index.php?c=errorTracking&a=accionError" id="form-resolucion">
            <input type="hidden" name="id" id="modal-error-id" value="">
            <input type="hidden" name="accion" value="cambiar_estado">
            <input type="hidden" name="estado" value="resuelto">
            
            <div class="grupo-input">
                <label for="detalles_resolucion">Detalles de la Solución (Opcional)</label>
                <textarea name="detalles_resolucion" id="detalles_resolucion" rows="4" placeholder="Ej: Se corrigió la consulta SQL en Torneo.php que estaba devolviendo nulos..."></textarea>
                <small>Esta nota quedará en el registro para futuras referencias.</small>
            </div>

            <div class="modal-acciones">
                <button type="button" class="btn btn-secundario" onclick="cerrarModal()">Cancelar</button>
                <button type="submit" class="btn btn-exito">Guardar Resolución</button>
            </div>
        </form>
    </div>
</div>

<script src="<?php echo URL_BASE; ?>js/admin/monitoreo_errores.js"></script>
