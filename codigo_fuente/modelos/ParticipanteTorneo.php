<?php
/**
 * Clase ParticipanteTorneo
 * 
 * Entidad que unifica la inscripción de competidores en un torneo,
 * abstrayendo si se trata de un equipo o de un usuario individual.
 */
class ParticipanteTorneo {
    /**
     * @var int|null $id Identificador del participante en el torneo.
     */
    public ?int $id;

    /**
     * @var int $torneoId Identificador del torneo.
     */
    public int $torneoId;

    /**
     * @var string $tipo Tipo de competidor ('equipo' o 'usuario').
     */
    public string $tipo;

    /**
     * @var int $referenciaId ID en la tabla de equipos o usuarios respectivamente.
     */
    public int $referenciaId;

    /**
     * @var string $nombre Nombre desnormalizado para visualización rápida.
     */
    public string $nombre;

    /**
     * @var string $estado Estado de la inscripción (pendiente, confirmado, rechazado, cancelado).
     */
    public string $estado;

    /**
     * @var string $fechaInscripcion Fecha y hora de inscripción.
     */
    public string $fechaInscripcion;

    /**
     * @var int|null $confirmadoPor ID del organizador que confirmó la inscripción.
     */
    public ?int $confirmadoPor;

    /**
     * @var string|null $fechaConfirmacion Fecha y hora de confirmación.
     */
    public ?string $fechaConfirmacion;

    /**
     * Constructor de la clase ParticipanteTorneo.
     */
    public function __construct(
        ?int $id,
        int $torneoId,
        string $tipo,
        int $referenciaId,
        string $nombre,
        string $estado = 'pendiente',
        string $fechaInscripcion = '',
        ?int $confirmadoPor = null,
        ?string $fechaConfirmacion = null
    ) {
        $this->id = $id;
        $this->torneoId = $torneoId;
        $this->tipo = $tipo;
        $this->referenciaId = $referenciaId;
        $this->nombre = $nombre;
        $this->estado = $estado;
        $this->fechaInscripcion = $fechaInscripcion;
        $this->confirmadoPor = $confirmadoPor;
        $this->fechaConfirmacion = $fechaConfirmacion;
    }
}
