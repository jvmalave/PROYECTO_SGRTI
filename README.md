# PROYECTO_SGRTI
## Ecosistema Digital Orientado a Microservicios para la Optimización de la Gestión de Requerimientos TI

El proyecto consiste en un ecosistema digital desacoplado (Arquitectura Monolito-Modular "Shared-Nothing") compuesto por:

- Portal Web Operativo: Desarrollado en Angular 17+ y Laravel, para la gestión administrativa y técnica. Centraliza el registro SSOT y gestiona el progreso granular de los requerimientos.

- App Móvil de Supervisión: Implementada en Ionic/Angular para la consulta de estatus, visualización de semáforos y KPIs en tiempo real.

- Módulo de Notificaciones: Sistema automático que envía correos electrónicos proactivos a los interesados cada vez que un componente cambia de fase.

- Módulo de Inteligencia Predictiva: Microservicio en Python para el análisis de riesgo de incumplimiento en la planificación basado en el progreso de los componentes.

- Base de Datos Centralizada: PostgreSQL segmentada en 5 esquemas lógicos para asegurar la independencia, seguridad y trazabilidad inmutable de los datos.
