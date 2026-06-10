<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo ?? 'Crear torneo - SGDM'; ?></title>
    <link rel="stylesheet" href="/publico/css/style.css">
</head>
<body>
    <?php require_once __DIR__ . '/../parciales/cabecera.php'; ?>

    <main class="contenedor-pantalla" style="max-width: 900px;">
        <div class="tarjeta-autenticacion" style="padding: 30px;">
            <a href="/torneos" style="color: #93c5fd; text-decoration:none; margin-bottom: 12px; display:inline-block;">← Volver a torneos</a>
            <h1 style="font-family: var(--fuente-titulo); font-size: 2rem; margin-bottom: 8px;">Crear un nuevo torneo</h1>
            <p style="color: var(--color-texto-secundario); margin-bottom: 18px;">Este formulario completa el flujo del módulo 3: configuración básica del torneo.</p>

            <form action="/torneos/crear" method="POST" style="display:grid; gap:14px;">
                <input type="hidden" name="token_csrf" value="<?php echo htmlspecialchars($token_csrf ?? ''); ?>">

                <div class="grupo-formulario">
                    <label for="nombre">Nombre del torneo</label>
                    <input id="nombre" name="nombre" class="control-formulario" type="text" required>
                </div>

                <div class="grupo-formulario">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" class="control-formulario" rows="3" placeholder="Reglamento breve, modalidad o formato"></textarea>
                </div>

                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:14px;">
                    <div class="grupo-formulario">
                        <label for="formato">Formato</label>
                        <select id="formato" name="formato" class="control-formulario">
                            <option value="liga">Liga</option>
                            <option value="eliminacion_directa">Eliminación directa</option>
                            <option value="suizo">Suizo</option>
                        </select>
                    </div>
                    <div class="grupo-formulario">
                        <label for="estado">Estado</label>
                        <select id="estado" name="estado" class="control-formulario">
                            <option value="inscripciones_abiertas">Inscripciones abiertas</option>
                            <option value="borrador">Borrador</option>
                            <option value="en_curso">En curso</option>
                        </select>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:14px;">
                    <div class="grupo-formulario">
                        <label for="modalidad_id">Modalidad</label>
                        <select id="modalidad_id" name="modalidad_id" class="control-formulario">
                            <?php foreach (($modalidades ?? []) as $modalidad): ?>
                                <option value="<?php echo (int)$modalidad['id']; ?>"><?php echo htmlspecialchars($modalidad['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="grupo-formulario">
                        <label for="sistema_puntuacion_id">Sistema de puntuación</label>
                        <select id="sistema_puntuacion_id" name="sistema_puntuacion_id" class="control-formulario">
                            <option value="">Personalizado</option>
                            <?php foreach (($sistemas_puntuacion ?? []) as $sistema): ?>
                                <option value="<?php echo (int)$sistema['id']; ?>"><?php echo htmlspecialchars($sistema['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:14px;">
                    <div class="grupo-formulario">
                        <label for="cupo_max_equipos">Cupo máximo</label>
                        <input id="cupo_max_equipos" name="cupo_max_equipos" class="control-formulario" type="number" value="8" min="2">
                    </div>
                    <div class="grupo-formulario">
                        <label for="cupo_min_equipos">Cupo mínimo</label>
                        <input id="cupo_min_equipos" name="cupo_min_equipos" class="control-formulario" type="number" value="2" min="2">
                    </div>
                    <div class="grupo-formulario">
                        <label for="mejor_de">Mejor de</label>
                        <input id="mejor_de" name="mejor_de" class="control-formulario" type="number" value="1" min="1">
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap:14px;">
                    <div class="grupo-formulario">
                        <label for="puntos_victoria">Puntos victoria</label>
                        <input id="puntos_victoria" name="puntos_victoria" class="control-formulario" type="number" step="0.5" value="3">
                    </div>
                    <div class="grupo-formulario">
                        <label for="puntos_empate">Puntos empate</label>
                        <input id="puntos_empate" name="puntos_empate" class="control-formulario" type="number" step="0.5" value="1">
                    </div>
                    <div class="grupo-formulario">
                        <label for="puntos_derrota">Puntos derrota</label>
                        <input id="puntos_derrota" name="puntos_derrota" class="control-formulario" type="number" step="0.5" value="0">
                    </div>
                </div>

                <div class="grupo-formulario">
                    <label for="tipo_resultado">Tipo de resultado</label>
                    <select id="tipo_resultado" name="tipo_resultado" class="control-formulario">
                        <option value="goles">Goles</option>
                        <option value="puntos">Puntos</option>
                        <option value="rondas">Rondas</option>
                        <option value="booleano">Booleano</option>
                    </select>
                </div>

                <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:14px;">
                    <div class="grupo-formulario">
                        <label for="fecha_inicio">Fecha de inicio</label>
                        <input id="fecha_inicio" name="fecha_inicio" class="control-formulario" type="date">
                    </div>
                    <div class="grupo-formulario">
                        <label for="fecha_fin">Fecha de fin</label>
                        <input id="fecha_fin" name="fecha_fin" class="control-formulario" type="date">
                    </div>
                    <div class="grupo-formulario">
                        <label for="fecha_limite_inscripcion">Límite de inscripción</label>
                        <input id="fecha_limite_inscripcion" name="fecha_limite_inscripcion" class="control-formulario" type="date">
                    </div>
                </div>

                <button type="submit" class="boton-primario">Guardar torneo</button>
            </form>
        </div>
    </main>
</body>
</html>
