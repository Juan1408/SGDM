<?php
/**
 * Clase ControladorBase
 * 
 * Clase de la cual heredan todos los controladores de la aplicación.
 * Provee métodos reutilizables para renderizado de vistas y redirecciones.
 */
class ControladorBase {
    /**
     * Renderiza una vista y le pasa datos de forma segura.
     * 
     * @param string $nombreVista Nombre del archivo de vista (sin extensión .php).
     * @param array $datos Colección de variables que se extraerán en la vista.
     * @return void
     */
    protected function renderizarVista(string $nombreVista, array $datos = []): void {
        /**
         * @var string $rutaVista Ruta completa del archivo de vista.
         */
        $rutaVista = __DIR__ . '/../vistas/' . $nombreVista . '.php';

        if (file_exists($rutaVista)) {
            // Extrae los datos para que estén disponibles como variables locales
            extract($datos);
            
            // Carga la vista correspondiente
            require $rutaVista;
        } else {
            die("Error crítico: No se encontró la vista '$nombreVista' en la ruta '$rutaVista'.");
        }
    }

    /**
     * Redirecciona al usuario a una ruta interna o externa.
     * 
     * @param string $destino URL o ruta de redirección.
     * @return void
     */
    protected function redireccionar(string $destino): void {
        header("Location: " . $destino);
        exit;
    }
}
