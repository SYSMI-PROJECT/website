SET @dbname = DATABASE();
SET @tablename = "utilisateur";
SET @columnname = "langue";
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (TABLE_SCHEMA = @dbname)
      AND (TABLE_NAME = @tablename)
      AND (COLUMN_NAME = @columnname)
  ) > 0,
  "SELECT 'La colonne langue existe déjà dans la table utilisateur' AS message;",
  "ALTER TABLE utilisateur ADD COLUMN langue VARCHAR(5) DEFAULT 'fr' AFTER theme;"
));

PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;