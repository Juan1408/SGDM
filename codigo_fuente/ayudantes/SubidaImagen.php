<?php
namespace App\Ayudantes;

/**
 * Guarda imagenes subidas por formulario (avatar, banner, etc).
 * Devuelve la ruta RELATIVA a publico/ (ej: 'img/perfiles/subidos/ab12.jpg') o null si algo falla.
 * La vista le antepone URL_BASE para mostrarla.
 */
class SubidaImagen {

    private const EXTENSIONES = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    private const MAX_BYTES = 2 * 1024 * 1024;

    //True si el usuario eligio un archivo en ese campo (para distinguir "no subio nada" de "subio algo invalido")
    public static function fueEnviado(?array $archivo): bool {
        return $archivo !== null && ($archivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
    }

    public static function guardar(array $archivo, string $subcarpeta): ?string {
        if (($archivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || $archivo['size'] > self::MAX_BYTES) {
            return null;
        }

        $tipo = (new \finfo(FILEINFO_MIME_TYPE))->file($archivo['tmp_name']);
        if (!isset(self::EXTENSIONES[$tipo]) || @getimagesize($archivo['tmp_name']) === false) {
            return null;
        }

        $rutaRelativa = 'img/' . trim($subcarpeta, '/') . '/';
        $carpeta = __DIR__ . '/../publico/' . $rutaRelativa;
        if (!is_dir($carpeta) && !mkdir($carpeta, 0755, true)) {
            return null;
        }

        $nombre = bin2hex(random_bytes(8)) . '.' . self::EXTENSIONES[$tipo];
        if (!move_uploaded_file($archivo['tmp_name'], $carpeta . $nombre)) {
            return null;
        }

        return $rutaRelativa . $nombre;
    }
}
