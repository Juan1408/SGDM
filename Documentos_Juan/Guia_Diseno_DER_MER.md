# Guía de Diseño Conceptual y Lógico: Modelo Entidad-Relación (MER) y Relacional (MR)

Esta guía orientativa está basada estrictamente en los estándares teóricos del **Modelo Entidad-Relación de Peter Chen (1976)**, que es el formato exigido para la representación conceptual de bases de datos. 

Te servirá como manual de referencia rápido al momento de utilizar plataformas externas de diagramación (como *Draw.io / Diagrams.net*, *Lucidchart*, *Creately*, etc.). Cuando uses estas plataformas, asegúrate de buscar la librería de formas llamada **"Entity Relationship (Chen)"** o **"ERD (Chen)"**.

---

## 1. Etapa Conceptual: El Diagrama Entidad-Relación (D.E-R.)

El objetivo de esta etapa es abstraer la realidad y los requerimientos del negocio sin preocuparnos por cómo se programará la base de datos (rendimiento, motores SQL, etc.).

### 🟦 Entidades
Son objetos, personas o conceptos del "mundo real" de los cuales necesitamos almacenar información (Ej: `Funcionario`, `Departamento`, `Proyecto`).
- **Representación Gráfica:** Un **Rectángulo** con el nombre en el interior (generalmente en singular).
- **Regla:** Un nombre de entidad solo puede aparecer una vez en todo el diagrama.

### 🟢 Atributos
Son las características o propiedades que describen a una entidad o a una relación (Ej: Nombre, Dirección, Fecha).
- **Representación Gráfica:** Un **Óvalo** conectado por una línea a la entidad o a la relación a la que pertenece.
- **Tipos de Atributos:**
  - **Clave o Determinante:** Identifica de forma única a cada ejemplar (Ej: `Id`, `Cédula`). Su nombre debe ir **subrayado** dentro del óvalo.
  - **Multivaluados (Multivalor):** Tienen un conjunto de valores para un mismo registro (Ej: Varios teléfonos). Se representan con un **óvalo de doble línea** y un **asterisco (*)** acompañando al nombre (Ej: `Teléfono*`).
  - **Compuestos o Derivados:** Se pueden dividir en componentes más pequeños. Se representan con un óvalo de **línea punteada**.

### 🔶 Relaciones
Es el vínculo, asociación o correspondencia entre dos o más entidades.
- **Representación Gráfica:** Un **Rombo** con el nombre (generalmente un verbo en minúscula, ej: `trabaja`, `asignado`, `tiene`) en el interior.
- **Grado de la Relación:** Cantidad de entidades que participan (Binaria = 2, Ternaria = 3).
- **Atributos en una relación:** Las relaciones también pueden tener óvalos (atributos) propios. Por ejemplo, en una relación `N:M` de `Funcionario` y `Proyecto`, la cantidad de `horas` trabajadas es un atributo del rombo (la asociación), no de las entidades individuales.

### 🔢 Cardinalidad y Restricciones
Especifica con cuántos elementos de otra entidad se puede vincular un ejemplar. Se coloca en las líneas conectoras entre la entidad y el rombo.
- **1:1 (Uno a Uno):** Un registro de la entidad A se relaciona con un único registro de B.
- **1:N (Uno a Varios):** Un registro de A puede relacionarse con muchos registros de B.
- **N:M (Varios a Varios):** Múltiples registros de A se asocian con múltiples de B.

---

## 2. Construcciones Avanzadas (Casos Especiales)

Cuando la realidad es más compleja, el MER incluye componentes avanzados:

### 👻 Entidad Débil
Es una entidad que **no puede identificarse por sí sola** sin la existencia de una "entidad fuerte". (Ej: Un `Estudiante` que se identifica por su número de lista dentro de un Grupo, pero solo tiene sentido dentro de la existencia de un `Grupo`).
- **Representación:** Suele graficarse con la entidad y su relación identificadora. A nivel visual clásico puede ser un rectángulo normal dependiente de la cardinalidad, o si la herramienta lo permite, con líneas dobles.

### ♻️ Autorrelación (Relación Recursiva)
Ocurre cuando una entidad se relaciona consigo misma.
- **Representación:** El rombo se conecta dos veces a la misma entidad.
- **Regla Obligatoria (Roles):** Se debe indicar explícitamente en las líneas de conexión el **Rol o Papel** que juega cada extremo. (Ej: Entidad `Funcionario` vinculada al rombo `supervisa`. Una línea dice "Jefe" y la otra dice "Empleado").

### 🔺 Generalización / Categorización (Herencia)
Se utiliza para representar sub-agrupaciones de entidades que comparten atributos generales pero tienen características particulares (Atributos o Relaciones únicas). (Ej: Entidad general `Funcionario` se divide en `Chofer`, `Administrativo`, `Técnico`).
- **Representación:** Un **Triángulo** con la palabra **"es"** en su interior, que conecta la entidad "padre" en el vértice superior con las entidades "hijas" en la base horizontal.

### 📦 Agregaciones
Se utilizan para relacionar entidades con otras relaciones completas (reinterpreta una relación como si fuera una nueva entidad).
- **Representación:** Se enmarcan las entidades involucradas y su rombo dentro de un gran rectángulo, tratándolo de ahí en adelante como un solo bloque capaz de relacionarse con terceros.

---

## 3. Etapa Lógica y Física: Del MER al Esquema Relacional

Una vez que tengas diseñado tu diagrama conceptual (MER), debes traducirlo al Modelo Relacional (Tablas, Filas, Columnas).

1. **Entidades Regulares:** Pasan a ser tablas. Los atributos simples pasan a ser las columnas.
2. **Atributo Clave:** Pasa a ser la Clave Primaria **(Primary Key - PK)**. Garantiza la unicidad y no puede ser nula (`NOT NULL`).
3. **Relaciones 1:N:** La clave primaria (PK) de la entidad con cardinalidad `1` viaja hacia la entidad con cardinalidad `N` convirtiéndose en una Clave Foránea **(Foreign Key - FK)** para asegurar la integridad referencial.
4. **Relaciones N:M:** ¡No se pueden programar directamente! Toda relación N:M origina obligatoriamente el nacimiento de una **Tabla Intermedia** (Tabla asociativa/Detalle). Las PK de ambas entidades fuertes viajan a esta nueva tabla como FKs, y en conjunto forman la nueva Primary Key compuesta.
5. **Atributos Multivaluados:** No se permiten en la Primera Forma Normal (1FN). Al igual que una relación N:M, generan la creación de una nueva tabla paralela conectada a la tabla original con una FK.
6. **Entidades Débiles:** Generan una tabla cuya Clave Primaria es frecuentemente compuesta: su propio atributo identificador parcial + la FK de la entidad fuerte de la cual dependen.
7. **Herencia (Categorización):** Genera una relación estricta 1:1 en la base de datos (como tienes actualmente en tu SQL entre `usuarios` y `perfiles_jugadores`), donde la PK de la tabla hija es simultáneamente la FK que apunta a la tabla padre con `ON DELETE CASCADE`.
