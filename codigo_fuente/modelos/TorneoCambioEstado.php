<?php
/**
 * Clase TorneoCambioEstado
 *
 * Entidad que registra cada transición de estado de un torneo,
 * formando el historial de ciclo de vida del mismo.
 */
class TorneoCambioEstado {
    /** @var int|null $id Identificador del registro de cambio. */
    public ?int $id;

    /** @var int $torneoId FK al torneo afectado. */
    public int $torneoId;

    /** @var string|null $estadoAnterior Estado previo al cambio (null si es el estado inicial). */
    public ?string $estadoAnterior;

    /** @var string $estadoNuevo Nuevo estado aplicado al torneo. */
    public string $estadoNuevo;

    /** @var string|null $motivo Justificación del cambio ingresada por el usuario. */
    public ?string $motivo;

    /** @var int $usuarioId ID del usuario que realizó el cambio de estado. */
    public int $usuarioId;

    /** @var string $fechaCambio Timestamp del momento del cambio. */
    public string $fechaCambio;

    /**
     * Constructor de TorneoCambioEstado.
     */
    public function __construct(
        ?int $id,
        int $torneoId,
        ?string $estadoAnterior,
        string $estadoNuevo,
        ?string $motivo,
        int $usuarioId,
        string $fechaCambio = ''
    ) {
        $this->id = $id;
        $this->torneoId = $torneoId;
        $this->estadoAnterior = $estadoAnterior;
        $this->estadoNuevo = $estadoNuevo;
        $this->motivo = $motivo;
        $this->usuarioId = $usuarioId;
        $this->fechaCambio = $fechaCambio;
    }
}
