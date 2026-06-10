<?php
/**
 * Clase SolicitudEquipo
 * 
 * Entidad que representa la solicitud de un usuario para ingresar a un equipo.
 */
class SolicitudEquipo {
    /**
     * @var int|null $id Identificador de la solicitud.
     */
    public ?int $id;

    /**
     * @var int $equipoId Identificador del equipo de destino.
     */
    public int $equipoId;

    /**
     * @var int $usuarioId Identificador del usuario solicitante.
     */
    public int $usuarioId;

    /**
     * @var string|null $mensaje Mensaje de presentación del usuario.
     */
    public ?string $mensaje;

    /**
     * @var string $estado Estado de la solicitud (pendiente, aprobado, rechazado, cancelado).
     */
    public string $estado;

    /**
     * @var string $fechaSolicitud Fecha y hora de envío.
     */
    public string $fechaSolicitud;

    /**
     * @var string|null $fechaRespuesta Fecha y hora de resolución.
     */
    public ?string $fechaRespuesta;

    /**
     * @var int|null $respondidoPor ID del capitán que aprobó o rechazó la solicitud.
     */
    public ?int $respondidoPor;

    /**
     * Constructor de la clase SolicitudEquipo.
     */
    public function __construct(
        ?int $id,
        int $equipoId,
        int $usuarioId,
        ?string $mensaje = null,
        string $estado = 'pendiente',
        string $fechaSolicitud = '',
        ?string $fechaRespuesta = null,
        ?int $respondidoPor = null
    ) {
        $this->id = $id;
        $this->equipoId = $equipoId;
        $this->usuarioId = $usuarioId;
        $this->mensaje = $mensaje;
        $this->estado = $estado;
        $this->fechaSolicitud = $fechaSolicitud;
        $this->fechaRespuesta = $fechaRespuesta;
        $this->respondidoPor = $respondidoPor;
    }
}
