<?php
/**
 * Clase Usuario
 * 
 * Entidad que representa a un usuario registrado en el sistema.
 */
class Usuario {
    /**
     * @var int|null $id Identificador del usuario.
     */
    public ?int $id;

    /**
     * @var string $email Correo electrónico único del usuario.
     */
    public string $email;

    /**
     * @var string $contrasenaHash Hash de la contraseña de acceso.
     */
    public string $contrasenaHash;

    /**
     * @var string $nombreCompleto Nombre y apellido completo.
     */
    public string $nombreCompleto;

    /**
     * @var string|null $telefono Número de teléfono móvil o de contacto.
     */
    public ?string $telefono;

    /**
     * @var string|null $fechaNacimiento Fecha de nacimiento (formato YYYY-MM-DD).
     */
    public ?string $fechaNacimiento;

    /**
     * @var string|null $fotoPerfilUrl Enlace a la imagen de perfil.
     */
    public ?string $fotoPerfilUrl;

    /**
     * @var string $fechaRegistro Fecha y hora de creación de la cuenta.
     */
    public string $fechaRegistro;

    /**
     * @var int $rolId Identificador del rol asignado.
     */
    public int $rolId;

    /**
     * @var bool $estaActivo Estado de habilitación de la cuenta.
     */
    public bool $estaActivo;

    /**
     * @var bool $emailVerificado Estatus de validación de correo.
     */
    public bool $emailVerificado;

    /**
     * @var string|null $ultimoAcceso Fecha y hora de la última conexión.
     */
    public ?string $ultimoAcceso;

    /**
     * Constructor del modelo Usuario.
     */
    public function __construct(
        ?int $id,
        string $email,
        string $contrasenaHash,
        string $nombreCompleto,
        ?string $telefono = null,
        ?string $fechaNacimiento = null,
        ?string $fotoPerfilUrl = null,
        string $fechaRegistro = '',
        int $rolId = 1,
        bool $estaActivo = true,
        bool $emailVerificado = false,
        ?string $ultimoAcceso = null
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->contrasenaHash = $contrasenaHash;
        $this->nombreCompleto = $nombreCompleto;
        $this->telefono = $telefono;
        $this->fechaNacimiento = $fechaNacimiento;
        $this->fotoPerfilUrl = $fotoPerfilUrl;
        $this->fechaRegistro = $fechaRegistro;
        $this->rolId = $rolId;
        $this->estaActivo = $estaActivo;
        $this->emailVerificado = $emailVerificado;
        $this->ultimoAcceso = $ultimoAcceso;
    }
}
