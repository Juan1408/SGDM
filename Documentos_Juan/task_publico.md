# 📋 Tareas de Ejecución - Frontend Público

*Este módulo está destinado a los usuarios visitantes (sin cuenta o sin iniciar sesión). Su objetivo es atraer nuevos competidores y mostrar el prestigio del sistema SGDM.*

- [ ] **0. 🚨 RECORDATORIO OBLIGATORIO: HACER `git pull` ANTES DE EMPEZAR A PROGRAMAR**

## 1. Landing Page (Página de Inicio)
- [ ] Diseñar y maquetar `vistas/publico/inicio.php`.
- [ ] Sección Hero (Banner principal con llamado a la acción "Únete a la Competencia").
- [ ] Sección de "Juegos Destacados" (Consumiendo los datos de la tabla `juegos` activos).
- [ ] Testimonios o estadísticas de la plataforma.

## 2. Flujo de Autenticación (UI/UX)
- [ ] Pulir el diseño visual de `login.php`.
- [ ] Crear la vista `registro.php` para que nuevos usuarios puedan crearse una cuenta (Rol Jugador por defecto, o Rol Organizador si se solicita).
- [ ] Implementar el Controlador (`AuthControlador->procesarRegistro()`) para validar los datos e insertarlos en la base de datos de forma segura (hashear contraseña).

## 3. Catálogo de Torneos (Público)
- [ ] Crear la pantalla `torneos_publicos.php`.
- [ ] Mostrar una grilla con los Torneos Activos y Próximos.
- [ ] Filtros de búsqueda (Por juego, por fecha, por estado).
- [ ] Pantalla de "Detalle del Torneo" (Ver las llaves/fixture y los equipos inscritos sin necesidad de estar logueado).

## 4. Rankings y Tablas de Posiciones
- [ ] Pantalla global de Ranking de Jugadores (Basado en estadísticas globales o puntos acumulados).
- [ ] Pantalla global de Ranking de Equipos.

## 5. Páginas Institucionales Estáticas
- [ ] Maquetar pantalla "Sobre Nosotros" (Reglas del sistema, quiénes somos).
- [ ] Maquetar pantalla de "Contacto" / Soporte.
