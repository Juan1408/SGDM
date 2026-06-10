<?php
/**
 * Clase Rol
 * 
 * Entidad que representa un rol de usuario dentro del sistema.
 */
class Rol {
    /**
     * @var int|null $id Identificador del rol.
     */
    public ?int $id;

    /**
     * @var string $nombreRol Nombre descriptivo del rol.
     */
    public string $nombreRol;

    /**
     * @var string|null $descripcion Explicación de las responsabilidades del rol.
     */
    public ?string $descripcion;

    /**
     * @var int $nivelPermiso Jerarquía o nivel numérico del rol para validación de accesos.
     */
    public int $nivelPermiso;

    /**
     * Constructor del modelo Rol.
     * 
     * @param int|null $id Identificador único del rol.
     * @param string $nombreRol Nombre del rol.
     * @param string|null $descripcion Detalles del rol.
     * @param int $nivelPermiso Nivel de permisos asignado.
     */
    public function __construct(
        ?int $id,
        string $nombreRol,
        ?string $descripcion = null,
        int $nivelPermiso = 0
    ) {
        $this->id = $id;
        $this->nombreRol = $nombreRol;
        $this->descripcion = $descripcion;
        $this->nivelPermiso = $nivelPermiso;
    }
}
