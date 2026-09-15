# 📄 Documentación Técnica: Limitaciones Semánticas del Modelo Entidad-Relación (DER)

> **Sistema:** SGDM ASCEND  
> **Ubicación del Documento:** `Documentos_Juan/limitaciones.md`  
> **Propósito:** Registrar e identificar las reglas de negocio y restricciones operacionales del dominio que exceden la capacidad expresiva gráfica del Diagrama Entidad-Relación (Notación Peter Chen), especificando su mecanismo de resolución en SQL y Backend PHP.

---

## 📐 1. Marco Teórico: ¿Qué es una Limitación Semántica?

Cuando se diseña un **Modelo Entidad-Relación (DER / MER)**, se utiliza un lenguaje gráfico estático compuesto por entidades (rectángulos), atributos (óvalos) y relaciones (rombos) con sus respectivas cardinalidades ($1:1$, $1:N$, $N:M$).

Sin embargo, los sistemas reales poseen **reglas de negocio dinámicas, restricciones condicionales y dependencias algorítmicas** que no poseen una figura geométrica ni una notación nativa para ser dibujadas. Se dice entonces que se ha alcanzado una **Limitación Semántica del Modelo DER**.

---

## 🎯 2. Matriz de Limitaciones Semánticas en SGDM ASCEND

A continuación se detallan los **5 casos principales de limitaciones semánticas** presentes en la arquitectura del sistema, su justificación y cómo fueron resueltos en el desarrollo:

| # | Regla de Negocio / Restricción del Sistema | ¿Por qué el DER gráfico no la puede representar? | Solución en Base de Datos (SQL) | Solución en Backend (PHP) |
| :-: | :--- | :--- | :--- | :--- |
| **1** | **Participación Polimórfica Exclusiva** (`participantes_torneo`) | El DER no posee notación nativa para la disyunción exclusiva (XOR). Al conectar `Torneo` con `Equipo` y `Usuario`, el gráfico no puede exigir que sea uno **U** otro, pero nunca ambos simultáneamente en la misma celda. | `tipo ENUM('equipo','usuario')` + `referencia_id` con `UNIQUE KEY (torneo_id, tipo, referencia_id)`. | Validación previa en `TorneoControlador::inscribir()` según la modalidad del torneo. |
| **2** | **Coherencia Cronológica y Lógica de Fechas** (`torneos`) | El DER permite dibujar los óvalos de fechas, pero no puede graficar relaciones de orden temporal ($A \le B \le C \le D$). | `CONSTRAINT fechas_validas CHECK (fecha_inicio IS NULL OR fecha_fin IS NULL OR fecha_inicio <= fecha_fin)`. | `Validador::enteroEnRango()` y comparación de fechas en `TorneoModelo`. |
| **3** | **Restricción Histórica del Algoritmo Suizo** (`torneo_suizo_parejas`) | El DER no puede representar restricciones dinámicas basadas en el historial de tuplas pasadas (history-based constraints). | Tabla relacional `torneo_suizo_parejas` con `CHECK (participante_a_id != participante_b_id)` y `ya_se_enfrentaron = TRUE`. | Motor del torneo en `ModuloSuizo::generarRondaSuiza()` que consulta enfrentamientos previos. |
| **4** | **Incompatibilidad entre Capitanía y Solicitud** (`solicitudes_equipo` vs `equipo_capitanes`) | El DER no puede expresar restricciones cruzadas entre dos relaciones distintas (cross-relation constraints). | Regla de unicidad `UNIQUE KEY unique_solicitud (equipo_id, usuario_id)`. | Verificación previa en `EquipoControlador::solicitarUnirse()` impidiendo que un capitán se auto-invite. |
| **5** | **Reglas Algorítmicas de Seguridad de Contraseñas** (`politicas_contrasenas`) | El DER no puede graficar algoritmos de encriptado, expresiones regulares de complejidad ni análisis de historial de claves. | Tabla `historial_contrasenas` para almacenamiento de hashes pasados. | Método `PoliticaContrasena::validarPassword()` con expresiones regulares (`preg_match`). |

---

## 🔍 3. Desglose Detallado de Cada Caso

---

### 📌 Caso 1: Participación Polimórfica Exclusiva (`participantes_torneo`)

- **Descripción del problema:** Un torneo puede ser disputado por escuadras (`equipos`) o por competidores individuales (`perfiles_jugadores`). En la base de datos se requiere una sola estructura de inscripción.
- **Limitación del DER:** En notación Chen, dibujar dos líneas desde el rombo `participa` hacia `Equipo` y `PerfilJugador` implica que ambos participan en conjunto en cada registro. El DER no tiene una figura para el operador `XOR` (Exclusión Mutua).
- **Mecanismo de resolución:**
  - **SQL (`ascend.sql`):**
    ```sql
    CREATE TABLE participantes_torneo (
        id INT AUTO_INCREMENT PRIMARY KEY,
        torneo_id INT NOT NULL,
        tipo ENUM('equipo', 'usuario') NOT NULL,
        referencia_id INT NOT NULL,
        UNIQUE KEY unico_participante (torneo_id, tipo, referencia_id)
    );
    ```

---

### 📌 Caso 2: Coherencia Cronológica y Fechas Críticas (`torneos`)

- **Descripción del problema:** Un torneo posee 4 hitos temporales: `fecha_inicio_inscripcion`, `fecha_limite_inscripcion`, `fecha_inicio` y `fecha_fin`. Debe cumplirse la secuencia cronológica estricta.
- **Limitación del DER:** Los atributos tipo fecha son óvalos independientes. El DER no posee conectores lógicos de comparación (`<`, `>`, `<=`).
- **Mecanismo de resolución:**
  - **SQL (`ascend.sql`):**
    ```sql
    CONSTRAINT fechas_validas CHECK (
        fecha_inicio IS NULL OR fecha_fin IS NULL OR fecha_inicio <= fecha_fin
    )
    ```

---

### 📌 Caso 3: Restricción Histórica de No Repetición en Sistema Suizo (`torneo_suizo_parejas`)

- **Descripción del problema:** En el formato de torneo Sistema Suizo, el algoritmo de emparejamiento prohíbe que dos competidores se enfrenten más de una vez en el mismo torneo.
- **Limitación del DER:** Las relaciones en el DER son estáticas e instantáneas; no pueden evaluar tuplas históricas insertadas en rondas anteriores.
- **Mecanismo de resolución:**
  - **SQL (`ascend.sql`):**
    ```sql
    CREATE TABLE torneo_suizo_parejas (
        torneo_id INT NOT NULL,
        participante_a_id INT NOT NULL,
        participante_b_id INT NOT NULL,
        ronda INT NOT NULL,
        ya_se_enfrentaron BOOLEAN DEFAULT TRUE,
        PRIMARY KEY (torneo_id, participante_a_id, participante_b_id),
        CONSTRAINT distintos CHECK (participante_a_id != participante_b_id)
    );
    ```

---

### 📌 Caso 4: Restricciones Cruzadas e Integridad de Rol (`solicitudes_equipo` vs `equipo_capitanes`)

- **Descripción del problema:** Un jugador que ya es capitán principal de un equipo no debe poder enviarse una solicitud de ingreso a su propio equipo.
- **Limitación del DER:** El DER no posee sintaxis para validar la intersección o invalidez entre dos rombos de relación distintos (`capitanea` vs `solicita_unirse`).
- **Mecanismo de resolución:**
  - **Backend (PHP - `PoliticaContrasenaControlador.php` / `EquipoControlador.php`):**
    Validación de negocio previa a la ejecución de la consulta SQL.

---

### 📌 Caso 5: Complejidad y Vencimiento de Contraseñas (`politicas_contrasenas`)

- **Descripción del problema:** El Administrador configura parámetros dinámicos de seguridad (ADM-07): longitud mínima, uso de caracteres especiales, vencimiento en días y cantidad de claves pasadas a no reutilizar.
- **Limitación del DER:** Las expresiones regulares, algoritmos de hashing (Bcrypt) y reglas temporales dinámicas están totalmente fuera del alcance conceptual del DER.
- **Mecanismo de resolución:**
  - **Backend (PHP - [PoliticaContrasena.php](file:///c:/xampp/htdocs/SGDM/codigo_fuente/modelos/PoliticaContrasena.php)):**
    Método `validarPassword($password)` mediante evaluación con expresiones regulares `preg_match()`.

---

## 🎓 4. Guía de Defensa Oral frente a un Tribunal

Si durante la presentación o examen el tribunal pregunta:  
*"¿El diagrama DER representa el 100% de la lógica y restricciones de su sistema?"*

### Respuesta Modelo Sugerida:
> *"No, debido a las **limitaciones semánticas inherentes al Modelo Entidad-Relación de Peter Chen**. El DER es un modelo conceptual gráfico estático capaz de representar entidades, atributos y relaciones con cardinalidad, pero no posee notación nativa para expresar reglas de disyunción exclusiva (como la participación polimórfica de equipos u usuarios), ordenamiento cronológico de fechas, ni restricciones dinámicas históricas (como la no repetición de parejas en el Sistema Suizo).  
> Por esta razón, nuestro diseño trasciende el DER y garantiza la integridad completa mediante **restricciones CHECK y UNIQUE en MySQL** y **servicios de validación en la capa Backend PHP**."*
