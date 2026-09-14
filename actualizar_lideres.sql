-- ============================================================
-- ACTUALIZACIÓN PRODUCCIÓN: Líderes + is_leader en users
-- Aplicar en phpMyAdmin / consola MySQL (Base: cat_20)
-- Idempotente: se puede re-ejecutar sin romper nada.
-- ============================================================

-- 1) COLUMNA is_leader en users (marca quién es líder de ministerio)
SET @col_exists = (
    SELECT COUNT(*) FROM `information_schema`.`COLUMNS`
    WHERE `TABLE_SCHEMA` = DATABASE() AND `TABLE_NAME` = 'users' AND `COLUMN_NAME` = 'is_leader'
);
SET @sql_add_leader = IF(@col_exists = 0,
    'ALTER TABLE `users` ADD COLUMN `is_leader` tinyint(1) NOT NULL DEFAULT 0 AFTER `is_active`',
    'SELECT 1');
PREPARE stmt_add_leader FROM @sql_add_leader;
EXECUTE stmt_add_leader;
DEALLOCATE PREPARE stmt_add_leader;

-- 2) SINCRONIZAR: marcar como líder a quienes ya tienen el rol 'Lider'
UPDATE `users` u
LEFT JOIN `model_has_roles` mr
       ON mr.`model_id` = u.`id`
      AND mr.`model_type` = 'App\\Models\\User'
      AND mr.`role_id` = (SELECT `id` FROM `roles` WHERE `name` = 'Lider' AND `guard_name` = 'web')
SET u.`is_leader` = 1
WHERE mr.`role_id` IS NOT NULL;