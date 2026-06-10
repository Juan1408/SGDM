<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo ?? 'Inscripción a Torneo - SGDM'; ?></title>
    <link rel="stylesheet" href="/publico/css/style.css">
    <link rel="stylesheet" href="/publico/css/equipos.css">
</head>
<body>
    <?php require_once __DIR__ . '/../parciales/cabecera.php'; ?>

    <main class="contenedor-pagina" style="max-width: 600px;">
        <div class="barra-superior">
            <h1 class="titulo-pagina">Inscripción a Torneo</h1>
            <a href="/torneos" style="color: var(--color-texto-secundario); text-decoration:none;">← Volver</a>
        </div>

        <div class="tarjeta-formulario">
            <form action="/participantes/inscribir?torneo_id=<?php echo (int)$torneo_id; ?>" method="POST">
                <input type="hidden" name="token_csrf" value="<?php echo htmlspecialchars($token_csrf); ?>">

                <div class="grupo-formulario">
                    <label>Modalidad de inscripción</label>
                    <div style="display:flex; gap:12px; flex-wrap:wrap;">
                        <label class="opcion-radio">
                            <input type="radio" name="tipo" value="usuario" id="tipo_usuario" checked onchange="toggleEquipo(false)">
                            <span>👤 Como jugador individual</span>
                        </label>
                        <?php if (!empty($mis_equipos)): ?>
                            <label class="opcion-radio">
                                <input type="radio" name="tipo" value="equipo" id="tipo_equipo" onchange="toggleEquipo(true)">
                                <span>🛡 Como equipo</span>
                            </label>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Selección de equipo (visible solo si se elige inscripción por equipo) -->
                <?php if (!empty($mis_equipos)): ?>
                    <div class="grupo-formulario" id="selector-equipo" style="display:none;">
                        <label for="referencia_id">Seleccioná tu equipo</label>
                        <select name="referencia_id" id="referencia_id" class="control-formulario">
                            <?php foreach ($mis_equipos as $equipo): ?>
                                <option value="<?php echo $equipo->id; ?>"><?php echo htmlspecialchars($equipo->nombreEquipo); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small style="color:var(--color-texto-secundario);">Solo podés inscribir equipos en los que seas Capitán.</small>
                    </div>
                <?php endif; ?>

                <button type="submit" class="boton-primario">Confirmar Inscripción</button>
            </form>
        </div>
    </main>

    <script>
        function toggleEquipo(mostrar) {
            var selector = document.getElementById('selector-equipo');
            if (selector) {
                selector.style.display = mostrar ? 'block' : 'none';
            }
        }
    </script>
</body>
</html>
