<?php
/**
 * Clase Torneo
 *
 * Entidad principal que representa un torneo multideportivo configurable.
 * Soporta múltiples formatos, modalidades y sistemas de puntuación.
 */
class Torneo {
    /** @var int|null $id Identificador único del torneo. */
    public ?int $id;

    /** @var string $nombre Nombre del torneo. */
    public string $nombre;

    /** @var string|null $descripcion Descripción o reglamento resumido. */
    public ?string $descripcion;

    /**
     * @var string $formato Formato de competencia.
     * Valores: 'liga' | 'eliminacion_directa' | 'suizo'
     */
    public string $formato;

    /**
     * @var string $estado Estado actual del torneo.
     * Valores: 'borrador' | 'inscripciones_abiertas' | 'en_curso' | 'finalizado' | 'cancelado'
     */
    public string $estado;

    /** @var int $cupoMaxEquipos Cantidad máxima de participantes. */
    public int $cupoMaxEquipos;

    /** @var int $cupoMinEquipos Cantidad mínima de participantes para iniciar. */
    public int $cupoMinEquipos;

    /** @var string|null $fechaInicio Fecha de inicio (formato YYYY-MM-DD). */
    public ?string $fechaInicio;

    /** @var string|null $fechaFin Fecha de finalización (formato YYYY-MM-DD). */
    public ?string $fechaFin;

    /** @var string|null $fechaLimiteInscripcion Fecha límite de inscripción. */
    public ?string $fechaLimiteInscripcion;

    /** @var string|null $reglas Reglamento detallado. */
    public ?string $reglas;

    /** @var string|null $premios Descripción de premios o incentivos. */
    public ?string $premios;

    /** @var string|null $ubicacion Sede física o plataforma virtual. */
    public ?string $ubicacion;

    /** @var int $organizadorId ID del usuario organizador a cargo. */
    public int $organizadorId;

    /** @var string $creadoEn Timestamp de creación del registro. */
    public string $creadoEn;

    /** @var string $actualizadoEn Timestamp de última modificación. */
    public string $actualizadoEn;

    /** @var int $modalidadId FK a la tabla modalidades (1=individual, 2=equipos). */
    public int $modalidadId;

    /** @var int|null $sistemaPuntuacionId FK al catálogo de sistemas de puntuación (NULL si se usan puntos personalizados). */
    public ?int $sistemaPuntuacionId;

    /** @var float $puntosVictoria Puntos otorgados por victoria. */
    public float $puntosVictoria;

    /** @var float $puntosEmpate Puntos otorgados por empate. */
    public float $puntosEmpate;

    /** @var float $puntosDerrota Puntos otorgados por derrota. */
    public float $puntosDerrota;

    /**
     * @var string $tipoResultado Tipo de marcador del encuentro.
     * Valores: 'goles' | 'puntos' | 'rondas' | 'booleano'
     */
    public string $tipoResultado;

    /**
     * @var int $mejorDe Número de partidas/mapas para definir el ganador de una llave
     * (aplica a formato eliminación directa).
     */
    public int $mejorDe;

    /**
     * Constructor de la clase Torneo.
     */
    public function __construct(
        ?int $id,
        string $nombre,
        ?string $descripcion = null,
        string $formato = 'liga',
        string $estado = 'borrador',
        int $cupoMaxEquipos = 8,
        int $cupoMinEquipos = 2,
        ?string $fechaInicio = null,
        ?string $fechaFin = null,
        ?string $fechaLimiteInscripcion = null,
        ?string $reglas = null,
        ?string $premios = null,
        ?string $ubicacion = null,
        int $organizadorId = 0,
        string $creadoEn = '',
        string $actualizadoEn = '',
        int $modalidadId = 1,
        ?int $sistemaPuntuacionId = null,
        float $puntosVictoria = 3,
        float $puntosEmpate = 1,
        float $puntosDerrota = 0,
        string $tipoResultado = 'goles',
        int $mejorDe = 1
    ) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->formato = $formato;
        $this->estado = $estado;
        $this->cupoMaxEquipos = $cupoMaxEquipos;
        $this->cupoMinEquipos = $cupoMinEquipos;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
        $this->fechaLimiteInscripcion = $fechaLimiteInscripcion;
        $this->reglas = $reglas;
        $this->premios = $premios;
        $this->ubicacion = $ubicacion;
        $this->organizadorId = $organizadorId;
        $this->creadoEn = $creadoEn;
        $this->actualizadoEn = $actualizadoEn;
        $this->modalidadId = $modalidadId;
        $this->sistemaPuntuacionId = $sistemaPuntuacionId;
        $this->puntosVictoria = $puntosVictoria;
        $this->puntosEmpate = $puntosEmpate;
        $this->puntosDerrota = $puntosDerrota;
        $this->tipoResultado = $tipoResultado;
        $this->mejorDe = $mejorDe;
    }
}
