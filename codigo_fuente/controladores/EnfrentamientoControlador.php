<?php
require_once __DIR__ . '/../nucleo/ControladorBase.php';
require_once __DIR__ . '/../nucleo/AdministradorSesion.php';
require_once __DIR__ . '/../repositorios/RepositorioEnfrentamiento.php';
require_once __DIR__ . '/../repositorios/RepositorioParticipante.php';
require_once __DIR__ . '/../repositorios/RepositorioTorneo.php';

class EnfrentamientoControlador extends ControladorBase {
    private RepositorioEnfrentamiento $repositorio;
    private RepositorioParticipante $repositorioParticipante;
    private RepositorioTorneo $repositorioTorneo;

    public function __construct() {
        $this->repositorio = new RepositorioEnfrentamiento();
        $this->repositorioParticipante = new RepositorioParticipante();
        $this->repositorioTorneo = new RepositorioTorneo();
    }

    public function resultados(): void {
        AdministradorSesion::iniciar();
        if (!AdministradorSesion::estaAutenticado()) {
            $this->redireccionar('/login');
        }

        $torneoId = (int)($_GET['id'] ?? 0);
        if ($torneoId <= 0) {
            $this->redireccionar('/torneos?error=torneo_invalido');
        }

        $torneo = $this->repositorioTorneo->obtenerPorId($torneoId);
        if (!$torneo) {
            $this->redireccionar('/torneos?error=torneo_no_encontrado');
        }

        $participantes = $this->repositorioParticipante->obtenerParticipantesPorTorneo($torneoId);
        if (empty($this->repositorio->obtenerPorTorneo($torneoId)) && count($participantes) >= 2) {
            $this->repositorio->generarFixtureBasico($torneoId, $participantes);
        }

        $encuentros = $this->repositorio->obtenerPorTorneo($torneoId);
        $tabla = $this->calcularTabla($participantes, $encuentros);

        $this->renderizarVista('torneos/resultados', [
            'torneo' => $torneo,
            'encuentros' => $encuentros,
            'tabla' => $tabla,
            'titulo' => 'Resultados - ' . $torneo->nombre,
        ]);
    }

    public function guardarResultado(): void {
        AdministradorSesion::iniciar();
        if (!AdministradorSesion::estaAutenticado()) {
            $this->redireccionar('/login');
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redireccionar('/torneos');
        }

        $encuentroId = (int)($_POST['encuentro_id'] ?? 0);
        $torneoId = (int)($_POST['torneo_id'] ?? 0);
        $resultadoLocal = (float)($_POST['resultado_local'] ?? 0);
        $resultadoVisitante = (float)($_POST['resultado_visitante'] ?? 0);

        $encuentro = $this->repositorio->obtenerPorId($encuentroId);
        if (!$encuentro) {
            $this->redireccionar('/torneos?error=encuentro_no_encontrado');
        }

        $ganadorId = null;
        if ($resultadoLocal > $resultadoVisitante) {
            $ganadorId = $encuentro->participanteLocalId;
        } elseif ($resultadoVisitante > $resultadoLocal) {
            $ganadorId = $encuentro->participanteVisitanteId;
        }

        $this->repositorio->actualizarResultado($encuentroId, $resultadoLocal, $resultadoVisitante, $ganadorId, 'finalizado');
        $this->redireccionar('/torneos/resultados?id=' . $torneoId . '&mensaje_exito=resultado_guardado');
    }

    private function calcularTabla(array $participantes, array $encuentros): array {
        $tabla = [];

        foreach ($participantes as $participante) {
            $tabla[$participante->id] = [
                'participante' => $participante,
                'pj' => 0,
                'pg' => 0,
                'pe' => 0,
                'pp' => 0,
                'gf' => 0,
                'gc' => 0,
                'pts' => 0,
            ];
        }

        foreach ($encuentros as $encuentro) {
            if ($encuentro->resultadoLocal === null || $encuentro->resultadoVisitante === null) {
                continue;
            }

            $local = $encuentro->participanteLocalId;
            $visitante = $encuentro->participanteVisitanteId;

            if ($local && isset($tabla[$local])) {
                $tabla[$local]['pj']++;
                $tabla[$local]['gf'] += $encuentro->resultadoLocal;
                $tabla[$local]['gc'] += $encuentro->resultadoVisitante;
            }

            if ($visitante && isset($tabla[$visitante])) {
                $tabla[$visitante]['pj']++;
                $tabla[$visitante]['gf'] += $encuentro->resultadoVisitante;
                $tabla[$visitante]['gc'] += $encuentro->resultadoLocal;
            }

            if ($encuentro->resultadoLocal > $encuentro->resultadoVisitante) {
                if ($local && isset($tabla[$local])) { $tabla[$local]['pg']++; $tabla[$local]['pts'] += 3; }
                if ($visitante && isset($tabla[$visitante])) { $tabla[$visitante]['pp']++; }
            } elseif ($encuentro->resultadoVisitante > $encuentro->resultadoLocal) {
                if ($visitante && isset($tabla[$visitante])) { $tabla[$visitante]['pg']++; $tabla[$visitante]['pts'] += 3; }
                if ($local && isset($tabla[$local])) { $tabla[$local]['pp']++; }
            } else {
                if ($local && isset($tabla[$local])) { $tabla[$local]['pe']++; $tabla[$local]['pts'] += 1; }
                if ($visitante && isset($tabla[$visitante])) { $tabla[$visitante]['pe']++; $tabla[$visitante]['pts'] += 1; }
            }
        }

        uasort($tabla, static function (array $a, array $b): int {
            if ($a['pts'] !== $b['pts']) {
                return $b['pts'] <=> $a['pts'];
            }
            if ($a['pg'] !== $b['pg']) {
                return $b['pg'] <=> $a['pg'];
            }
            return $b['gf'] <=> $a['gf'];
        });

        return $tabla;
    }
}
