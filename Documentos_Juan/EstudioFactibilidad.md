<div align="center">
  <!-- Reemplazar src con la ruta real del logo si existe -->
  <img src="https://via.placeholder.com/150?text=LOGO+EMPRESA" alt="Logo de la Empresa" width="150"/>
  
  # Sistema de Gestión Deportiva Multidisciplinaria (SGDM)
  
  **Documento:** Estudio de Factibilidad  
  **Autor(es):** Juan Fernández y Equipo de Desarrollo  
  **Fecha de entrega:** 23 de Agosto de 2026  
  **Versión:** 1.0  
</div>

<br><br>

## ÍNDICE
1. [Introducción](#1-introducción)
2. [Descripción general del proyecto](#2-descripción-general-del-proyecto)
3. [Objetivo del estudio de factibilidad](#3-objetivo-del-estudio-de-factibilidad)
4. [Alcance del análisis](#4-alcance-del-análisis)
5. [Factibilidad técnica](#5-factibilidad-técnica)
6. [Factibilidad económica](#6-factibilidad-económica)
7. [Factibilidad operativa](#7-factibilidad-operativa)
8. [Factibilidad legal](#8-factibilidad-legal)
9. [Conclusión general de factibilidad](#9-conclusión-general-de-factibilidad)

---

## 1. Introducción
El presente documento establece el estudio de factibilidad para la creación del **Sistema de Gestión Deportiva Multidisciplinaria (SGDM)**. La importancia de este análisis radica en mitigar riesgos económicos, técnicos, operacionales y legales antes de escalar la etapa de desarrollo y despliegue. Este estudio garantiza que el proyecto sea viable, sostenible en el tiempo e impulse la eficiencia en la organización de competencias tanto de deportes tradicionales como de eSports.

## 2. Descripción general del proyecto
El proyecto consiste en el desarrollo de una plataforma web integral basada en arquitectura MVC (Modelo-Vista-Controlador) para administrar torneos deportivos.

- **Problema que resuelve:** La gestión manual, fragmentada y en hojas de cálculo de los torneos deportivos, lo que genera desorganización en los fixtures, pérdida de resultados históricos y falta de transparencia para los competidores y el público.
- **A quién está dirigido:** 
  - *Administradores Generales:* Para el control de la plataforma y moderación.
  - *Organizadores (Rol 2):* Entidades o personas que crean y gestionan torneos.
  - *Jugadores (Rol 3):* Competidores individuales o capitanes de equipos.
  - *Público en general:* Espectadores que desean ver resultados y fixtures.
- **Funciones principales del sistema:**
  - Módulo de autenticación seguro (roles y permisos).
  - Catálogo dinámico de juegos/disciplinas.
  - Creación de equipos y gestión de roster (invitaciones).
  - Generación de torneos y administración de inscripciones.
  - Registro de resultados y visualización de tablas de posiciones/llaves públicas.

## 3. Objetivo del estudio de factibilidad
Determinar la viabilidad técnica, económica, operativa y legal del desarrollo e implementación del sistema SGDM, con el fin de proporcionar al equipo directivo y académico la información necesaria para aprobar la continuación, inversión y eventual despliegue en producción del proyecto.

## 4. Alcance del análisis
El análisis abarcará la infraestructura tecnológica necesaria para la etapa de desarrollo, pruebas y los primeros 12 meses de operación en un servidor de producción de gama de entrada. Se evalúa la capacidad técnica del equipo de desarrollo interno (estudiantes/programadores junior-mid). No se incluyen en este estudio proyecciones de escalabilidad masiva (servidores elásticos en la nube) ni costos de campañas de marketing masivo, limitándose al funcionamiento operativo del software.

## 5. Factibilidad técnica
El proyecto utilizará un stack tecnológico robusto pero accesible, adecuado para las capacidades actuales del equipo de desarrollo.

**Capacidades del equipo de desarrollo:** El equipo cuenta con conocimientos sólidos en programación orientada a objetos (POO), patrón MVC, manipulación del DOM con Vanilla JS y diseño de bases de datos relacionales en MySQL.

| Componente | Recursos Requeridos | Recursos Disponibles | Viabilidad |
| :--- | :--- | :--- | :--- |
| **Lenguaje Backend** | PHP 8.0+ | PHP 8.2 (XAMPP local) | ✅ Alta |
| **Base de Datos** | Motor SQL Relacional | MySQL / MariaDB | ✅ Alta |
| **Control de Versiones** | Git / GitHub | Repositorio GitHub Activo | ✅ Alta |
| **Arquitectura** | MVC sin Frameworks pesados | Entorno MVC Propio (Front Controller) | ✅ Alta |
| **Hosting (Producción)** | Servidor Apache/Linux básico | Pendiente de contratación | ⚠️ Media |

*Conclusión Técnica:* Altamente factible. Las herramientas (PHP, MySQL, HTML/CSS/JS) son de código abierto, están dominadas por el equipo y no requieren hardware especializado para su desarrollo.

## 6. Factibilidad económica
Análisis estimado de los costos y beneficios para el primer año de operación (expresado en USD referencial).

**Costos Estimados (Año 1):**
- Hosting Compartido / VPS Básico (Ej. Hostinger/DigitalOcean): $120 USD.
- Dominio (.com / .net): $15 USD.
- Herramientas de Desarrollo (VS Code, XAMPP): $0 USD (Open Source).
- Costo de mano de obra (Desarrollo Interno): $0 USD (Proyecto formativo/interno).
- **Total Inversión Inicial:** $135 USD.

**Beneficios Cuantificables y Cualitativos:**
- Ahorro de más de 20 horas semanales en gestión manual de planillas.
- Posibilidad de monetización futura cobrando una tarifa mínima de suscripción a los Organizadores por usar la plataforma.
- Centralización de datos evitando pérdida de información estadística.

*Conclusión Económica:* Totalmente factible. Los costos de infraestructura son mínimos y asumibles, y el software base es 100% gratuito (Open Source).

## 7. Factibilidad operativa
Revisión del impacto que tendrá el sistema en los usuarios finales.

- **Impacto en el Proceso:** Se pasa de coordinar equipos y resultados mediante grupos de WhatsApp y tablas de Excel (Proceso Anterior) a una plataforma centralizada y automatizada donde el sistema rutea las invitaciones y genera los fixtures (Proceso Nuevo).
- **Aceptación del Sistema:** Al tratarse de un público afín a los videojuegos (eSports) y el deporte, la adopción de herramientas tecnológicas es natural. La interfaz se está diseñando priorizando la Experiencia de Usuario (UX).
- **Capacitación necesaria:** 
  - *Jugadores y Público:* Ninguna (interfaz intuitiva).
  - *Organizadores:* Se requerirá un manual de usuario simple (1 página) o un video tutorial de 3 minutos sobre cómo generar las llaves del torneo.
  - *Administradores:* El equipo desarrollador ya conoce el sistema.

*Conclusión Operativa:* Altamente factible. La curva de aprendizaje es mínima y la mejora en la eficiencia de gestión es evidente.

## 8. Factibilidad legal
Evaluación de las normativas y licencias que rigen el proyecto.

- **Uso de Software:** El proyecto está desarrollado utilizando lenguajes y herramientas de código abierto (Open Source) bajo licencias permisivas (PHP License, MySQL GPL, etc.), lo cual permite su uso comercial sin pago de regalías a terceros.
- **Privacidad y Datos Personales:** Las contraseñas de los usuarios no se almacenan en texto plano; el sistema utiliza el algoritmo de encriptación `Bcrypt` nativo de PHP (`password_hash`), protegiendo la identidad de los usuarios en caso de vulneraciones.
- **Políticas Institucionales:** Se debe redactar e incluir un documento de "Términos y Condiciones" y "Política de Privacidad" en el Frontend (Módulo Público) para cumplir con las normativas locales de protección de datos personales informando sobre el uso de cookies de sesión.

*Conclusión Legal:* Factible. Cumple con los estándares básicos de seguridad informática y licenciamiento de software.

## 9. Conclusión general de factibilidad
Tras evaluar las cuatro dimensiones críticas, se determina que el desarrollo del Sistema de Gestión Deportiva Multidisciplinaria (SGDM) es **Totalmente Factible**. 

Las dimensiones técnica, operativa y legal son 100% viables, sustentadas en el uso de tecnologías Open Source ya dominadas por el equipo de desarrollo y protocolos de seguridad nativos (PDO y Bcrypt). En la dimensión económica, la barrera de entrada es ínfima (aprox. $135 USD anuales), lo cual representa un riesgo financiero extremadamente bajo frente a los enormes beneficios organizacionales y de escalabilidad que el software aportará a la gestión deportiva. Por lo tanto, se recomienda proceder con la etapa de ejecución y despliegue del proyecto.
