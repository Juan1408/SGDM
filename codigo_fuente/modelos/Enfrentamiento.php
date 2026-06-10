<?php
/**
 * Modelo simple para un encuentro de torneo.
 */
class Enfrentamiento {
    public ?int $id;
    public int $torneoId;
    public int $ronda;
    public ?int $participanteLocalId;
    public ?int $participanteVisitanteId;
    public ?string $fechaHoraProgramada;
    public ?string $cancha;
    public ?float $resultadoLocal;
    public ?float $resultadoVisitante;
    public string $estado;
    public ?int $participanteGanadorId;

    public function __construct(
        ?int $id,
        int $torneoId,
        int $ronda,
        ?int $participanteLocalId,
        ?int $participanteVisitanteId,
        ?string $fechaHoraProgramada,
        ?string $cancha,
        ?float $resultadoLocal,
        ?float $resultadoVisitante,
        string $estado = 'programado',
        ?int $participanteGanadorId = null
    ) {
        $this->id = $id;
        $this->torneoId = $torneoId;
        $this->ronda = $ronda;
        $this->participanteLocalId = $participanteLocalId;
        $this->participanteVisitanteId = $participanteVisitanteId;
        $this->fechaHoraProgramada = $fechaHoraProgramada;
        $this->cancha = $cancha;
        $this->resultadoLocal = $resultadoLocal;
        $this->resultadoVisitante = $resultadoVisitante;
        $this->estado = $estado;
        $this->participanteGanadorId = $participanteGanadorId;
    }
}

