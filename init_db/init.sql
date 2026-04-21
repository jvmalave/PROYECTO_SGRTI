-- Ejecutado automáticamente al iniciar el contenedor por primera vez
CREATE SCHEMA IF NOT EXISTS identity;         -- Usuarios, Roles, Permisos
CREATE SCHEMA IF NOT EXISTS requirements_core;-- El "qué" del negocio
CREATE SCHEMA IF NOT EXISTS execution_flow;   -- El "cómo" y "quién" lo hace
CREATE SCHEMA IF NOT EXISTS reporting_kpi;    -- Resultados y métricas
CREATE SCHEMA IF NOT EXISTS audit_logs;      -- Rastro de seguridad e historia