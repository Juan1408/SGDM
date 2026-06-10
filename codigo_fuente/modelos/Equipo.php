<?php
/**
 * Clase Equipo
 * 
 * Entidad que representa a un equipo registrado en el sistema.
 */
class Equipo {
    /**
     * @var int|null $id Identificador único del equipo.
     */
    public ?int $id;

    /**
     * @var string $nombreEquipo Nombre del equipo.
     */
    public string $nombreEquipo;

    /**
     * @var string|null $escudoUrl URL de la imagen del escudo.
     */
    public ?string $escudoUrl;

    /**
     * @var string|null $descripcion Breve reseña o lema del equipo.
     */
    public ?string $descripcion;

    /**
     * @var string $fechaCreacion Fecha y hora de registro.
     */
    public string $fechaCreacion;

    /**
     * @var int $creadoPor ID del usuario fundador/creador del equipo.
     */
    public int $creadoPor;

    /**
     * @var bool $activo Estado lógico del equipo.
     */
    public bool $activo;

    /**
     * @var string|null $codigoInvitacion Código alfanumérico para invitar miembros.
     */
    public ?string $codigoInvitacion;

    /**
     * Constructor de la clase Equipo.
     */
    public function __construct(
        ?int $id,
        string $nombreEquipo,
        ?string $escudoUrl = null,
        ?string $descripcion = null,
        string $fechaCreacion = '',
        int $creadoPor = 0,
        bool $activo = true,
        ?string $codigoInvitacion = null
    ) {
        $this->id = $id;
        $this->nombreEquipo = $nombreEquipo;
        $this->escudoUrl = $escudoUrl;
        $this->descripcion = $descripcion;
        $this->fechaCreacion = $fechaCreacion;
        $this->creadoPor = $creadoPor;
        $this->activo = $activo;
        $this->codigoInvitacion = $codigoInvitacion;
    }
}
