<div class="contenedor-principal">
    <div class="header-modulo">
        <div class="titulo-modulo">
            <div class="pulse pulse-primario"></div>
            <h1>Salón de la Fama por Disciplina</h1>
        </div>
        <p>Top 10 histórico segregado para mantener la integridad competitiva de cada juego.</p>
    </div>

    <?php if (empty($salonesDeLaFama)): ?>
        <div class="resultados-vacio">
            <i class="fa-solid fa-folder-open"></i>
            <h3>No hay registros suficientes</h3>
            <p>Aún no existen torneos finalizados en ninguna disciplina para generar el salón de la fama.</p>
        </div>
    <?php else: ?>
        <div class="resultados-grid">
            <?php foreach ($salonesDeLaFama as $salon): ?>
                <div class="disciplina-bloque">
                    
                    <!-- Cabecera de la Disciplina -->
                    <div class="disciplina-cabecera">
                        <div class="disciplina-titulo">
                            <i class="fa-solid fa-gamepad"></i>
                            <h2><?php echo htmlspecialchars($salon['juego_nombre']); ?></h2>
                        </div>
                        <span class="disciplina-etiqueta">
                            <?php echo htmlspecialchars($salon['juego_categoria']); ?>
                        </span>
                    </div>

                    <!-- Tabla Minimalista -->
                    <div style="overflow-x: auto;">
                        <table class="tabla-minimalista">
                            <thead>
                                <tr>
                                    <th class="col-rank">Rank</th>
                                    <th>Participante</th>
                                    <th class="col-center">Torneos</th>
                                    <th class="col-center">Victorias</th>
                                    <th class="col-right">Pts Totales</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $posicion = 1; ?>
                                <?php foreach ($salon['ranking'] as $participante): ?>
                                    <tr>
                                        <td class="col-rank <?php echo ($posicion <= 3) ? 'td-rank-top' : ''; ?>">
                                            <?php echo $posicion; ?>
                                        </td>
                                        <td class="td-participante">
                                            <?php if ($posicion === 1): ?>
                                                <i class="fa-solid fa-trophy icono-oro" title="Primer Puesto"></i>
                                            <?php elseif ($posicion === 2): ?>
                                                <i class="fa-solid fa-medal icono-plata" title="Segundo Puesto"></i>
                                            <?php elseif ($posicion === 3): ?>
                                                <i class="fa-solid fa-medal icono-bronce" title="Tercer Puesto"></i>
                                            <?php endif; ?>
                                            <?php echo htmlspecialchars($participante['participante_nombre']); ?>
                                        </td>
                                        <td class="col-center">
                                            <?php echo $participante['torneos_jugados']; ?>
                                        </td>
                                        <td class="col-center">
                                            <?php echo $participante['partidos_ganados']; ?> / <?php echo $participante['partidos_jugados']; ?>
                                        </td>
                                        <td class="col-right td-puntos">
                                            <?php echo number_format($participante['puntos_totales'], 0); ?>
                                        </td>
                                    </tr>
                                    <?php $posicion++; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
