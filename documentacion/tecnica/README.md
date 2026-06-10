# Documentación técnica

Arquitectura, diagrama de clases, decisiones de diseño y cómo desplegar.

## Restricción de roles en torneos
- La creación de torneos se valida en el controlador de torneos para permitir solo roles de Administrador (1) u Organizador (2).
- El botón de creación se oculta en la vista pública de torneos para usuarios que no tienen esos permisos.
