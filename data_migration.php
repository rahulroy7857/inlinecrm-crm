<?php

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');
set_time_limit(0);

/*
|--------------------------------------------------------------------------
| Database configuration
|--------------------------------------------------------------------------
|
| Enter table names below, then run:
|   php data_migration.php
|
| Or open in browser:
|   /data_migration.php
|
*/

$dbHost = '127.0.0.1';
$dbPort = 3306;
$dbUser = 'root';
$dbPass = '';

$sourceDatabase      = 'test';
$destinationDatabase = 'u428141704_crm';

/*
| Enter the table name to migrate here.
| If both tables have the same name, set only $sourceTable.
*/
$sourceTable      = 'leads';
$destinationTable = 'leads';

/*
| error  = fail on duplicate primary/unique keys
| ignore = skip duplicate rows (INSERT IGNORE)
*/
$duplicatePolicy = 'error';

/*
| true  = copy auto-increment IDs from source (needed for foreign keys)
| false = let MySQL generate new IDs
*/
$copyAutoIncrementId = true;

/*
| true  = only show the SQL, do not insert
| false = actually migrate data
*/
$dryRun = false;

/*
| true  = temporarily disable foreign key checks while inserting
|         (use this when parent tables are not migrated yet)
*/
$disableForeignKeyChecks = true;

headerIfBrowser();

/*
|--------------------------------------------------------------------------
| Helper functions
|--------------------------------------------------------------------------
*/

function headerIfBrowser(): void
{
    if (PHP_SAPI !== 'cli') {
        header('Content-Type: text/plain; charset=utf-8');
    }
}

function output(string $message): void
{
    echo $message . PHP_EOL;
}

function throwUsage(string $message): never
{
    output('Migration failed.');
    output('Error: ' . $message);
    output('');
    output('Set table names at the top of data_migration.php:');
    output("  \$sourceTable      = 'your_table';");
    output("  \$destinationTable = \$sourceTable;");
    output('');
    output('Then run:');
    output('  php data_migration.php');

    exit(1);
}

function validateIdentifier(string $identifier, string $label): void
{
    if ($identifier === '') {
        throw new InvalidArgumentException("{$label} is required.");
    }

    /*
     * Allows letters, numbers and underscores.
     * Example: academic_years, 8545_callhistory
     */
    if (!preg_match('/^[A-Za-z0-9_]+$/', $identifier)) {
        throw new InvalidArgumentException(
            "Invalid {$label}: {$identifier}"
        );
    }
}

function quoteIdentifier(string $identifier): string
{
    return '`' . str_replace('`', '``', $identifier) . '`';
}

function fullTableName(string $database, string $table): string
{
    return quoteIdentifier($database) . '.' . quoteIdentifier($table);
}

function tableExists(
    PDO $pdo,
    string $database,
    string $table
): bool {
    $sql = "
        SELECT COUNT(*)
        FROM INFORMATION_SCHEMA.TABLES
        WHERE TABLE_SCHEMA = :database
          AND TABLE_NAME = :table
    ";

    $statement = $pdo->prepare($sql);

    $statement->execute([
        'database' => $database,
        'table' => $table,
    ]);

    return (int) $statement->fetchColumn() > 0;
}

function getTableColumns(
    PDO $pdo,
    string $database,
    string $table
): array {
    $sql = "
        SELECT
            COLUMN_NAME,
            ORDINAL_POSITION,
            COLUMN_DEFAULT,
            IS_NULLABLE,
            DATA_TYPE,
            COLUMN_TYPE,
            COLUMN_KEY,
            EXTRA,
            GENERATION_EXPRESSION
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = :database
          AND TABLE_NAME = :table
        ORDER BY ORDINAL_POSITION
    ";

    $statement = $pdo->prepare($sql);

    $statement->execute([
        'database' => $database,
        'table' => $table,
    ]);

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function isGeneratedColumn(array $column): bool
{
    $extra = strtolower((string) $column['EXTRA']);
    $generationExpression = trim(
        (string) ($column['GENERATION_EXPRESSION'] ?? '')
    );

    return str_contains($extra, 'generated')
        || $generationExpression !== '';
}

function isAutoIncrementColumn(array $column): bool
{
    return str_contains(
        strtolower((string) $column['EXTRA']),
        'auto_increment'
    );
}

/*
|--------------------------------------------------------------------------
| Run migration
|--------------------------------------------------------------------------
*/

try {
    if ($sourceTable === '') {
        throwUsage('Set $sourceTable at the top of this file.');
    }

    validateIdentifier($sourceDatabase, 'source database');
    validateIdentifier($destinationDatabase, 'destination database');
    validateIdentifier($sourceTable, 'source table');
    validateIdentifier($destinationTable, 'destination table');

    if (!in_array($duplicatePolicy, ['error', 'ignore'], true)) {
        throw new InvalidArgumentException(
            'Duplicate policy must be error or ignore.'
        );
    }

    $dsn = sprintf(
        'mysql:host=%s;port=%d;charset=utf8mb4',
        $dbHost,
        $dbPort
    );

    $pdo = new PDO(
        $dsn,
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );

    if (!tableExists($pdo, $sourceDatabase, $sourceTable)) {
        throw new RuntimeException(
            "Source table does not exist: " .
            "{$sourceDatabase}.{$sourceTable}"
        );
    }

    if (!tableExists(
        $pdo,
        $destinationDatabase,
        $destinationTable
    )) {
        throw new RuntimeException(
            "Destination table does not exist: " .
            "{$destinationDatabase}.{$destinationTable}"
        );
    }

    $sourceColumns = getTableColumns(
        $pdo,
        $sourceDatabase,
        $sourceTable
    );

    $destinationColumns = getTableColumns(
        $pdo,
        $destinationDatabase,
        $destinationTable
    );

    /*
     * Store source columns by lowercase name.
     * This helps compare column names dynamically.
     */
    $sourceColumnMap = [];

    foreach ($sourceColumns as $sourceColumn) {
        $sourceColumnMap[
            strtolower($sourceColumn['COLUMN_NAME'])
        ] = $sourceColumn;
    }

    $destinationColumnNames = [];

    foreach ($destinationColumns as $destinationColumn) {
        $destinationColumnNames[
            strtolower($destinationColumn['COLUMN_NAME'])
        ] = $destinationColumn['COLUMN_NAME'];
    }

    $insertColumns = [];
    $selectExpressions = [];

    $matchingColumns = [];
    $nullColumns = [];
    $defaultColumns = [];
    $skippedGeneratedColumns = [];
    $skippedAutoIncrementColumns = [];
    $requiredMissingColumns = [];
    $unmatchedSourceColumns = [];

    foreach ($sourceColumns as $sourceColumn) {
        $lookupName = strtolower($sourceColumn['COLUMN_NAME']);

        if (!isset($destinationColumnNames[$lookupName])) {
            $unmatchedSourceColumns[] =
                $sourceColumn['COLUMN_NAME'];
        }
    }

    foreach ($destinationColumns as $destinationColumn) {
        $destinationColumnName =
            $destinationColumn['COLUMN_NAME'];

        $lookupName = strtolower($destinationColumnName);

        /*
         * Generated columns cannot be inserted manually.
         */
        if (isGeneratedColumn($destinationColumn)) {
            $skippedGeneratedColumns[] =
                $destinationColumnName;

            continue;
        }

        $isAutoIncrement =
            isAutoIncrementColumn($destinationColumn);

        $sourceColumn =
            $sourceColumnMap[$lookupName] ?? null;

        /*
         * Destination auto-increment should generate a new ID.
         */
        if ($isAutoIncrement && !$copyAutoIncrementId) {
            $skippedAutoIncrementColumns[] =
                $destinationColumnName;

            continue;
        }

        /*
         * Column exists in both source and destination.
         */
        if ($sourceColumn !== null) {
            $insertColumns[] =
                quoteIdentifier($destinationColumnName);

            $selectExpressions[] =
                'src.' .
                quoteIdentifier($sourceColumn['COLUMN_NAME']) .
                ' AS ' .
                quoteIdentifier($destinationColumnName);

            $matchingColumns[] =
                $destinationColumnName;

            continue;
        }

        /*
         * Destination column is missing from source.
         */
        $isNullable =
            strtoupper(
                (string) $destinationColumn['IS_NULLABLE']
            ) === 'YES';

        $hasDefault =
            $destinationColumn['COLUMN_DEFAULT'] !== null;

        /*
         * Auto-increment column missing from source.
         * Omit it so MySQL generates the ID.
         */
        if ($isAutoIncrement) {
            $skippedAutoIncrementColumns[] =
                $destinationColumnName;

            continue;
        }

        /*
         * Missing nullable destination column.
         * Explicitly insert NULL.
         */
        if ($isNullable) {
            $insertColumns[] =
                quoteIdentifier($destinationColumnName);

            $selectExpressions[] =
                'NULL AS ' .
                quoteIdentifier($destinationColumnName);

            $nullColumns[] =
                $destinationColumnName;

            continue;
        }

        /*
         * Missing NOT NULL column with a default value.
         * Omit it from INSERT so MySQL uses its default.
         */
        if ($hasDefault) {
            $defaultColumns[] =
                $destinationColumnName;

            continue;
        }

        /*
         * Missing NOT NULL column without default.
         * Migration cannot insert NULL into this column.
         */
        $requiredMissingColumns[] =
            $destinationColumnName;
    }

    if (!empty($requiredMissingColumns)) {
        throw new RuntimeException(
            "Migration cannot continue.\n" .
            "The following destination columns do not exist " .
            "in the source table, do not allow NULL and have " .
            "no default value:\n" .
            implode(', ', $requiredMissingColumns) . "\n\n" .
            "Make these columns nullable, add a default value, " .
            "or rename/map them in the source table."
        );
    }

    if (empty($insertColumns)) {
        throw new RuntimeException(
            'No insertable columns were found.'
        );
    }

    $sourceFullTable = fullTableName(
        $sourceDatabase,
        $sourceTable
    );

    $destinationFullTable = fullTableName(
        $destinationDatabase,
        $destinationTable
    );

    $insertCommand = $duplicatePolicy === 'ignore'
        ? 'INSERT IGNORE INTO'
        : 'INSERT INTO';

    $migrationSql = sprintf(
        "%s %s\n(\n    %s\n)\nSELECT\n    %s\nFROM %s AS src",
        $insertCommand,
        $destinationFullTable,
        implode(",\n    ", $insertColumns),
        implode(",\n    ", $selectExpressions),
        $sourceFullTable
    );

    $sourceRowCount = (int) $pdo
        ->query(
            "SELECT COUNT(*) FROM {$sourceFullTable}"
        )
        ->fetchColumn();

    output('============================================');
    output('Dynamic table migration');
    output('============================================');
    output(
        "Source: {$sourceDatabase}.{$sourceTable}"
    );
    output(
        "Destination: " .
        "{$destinationDatabase}.{$destinationTable}"
    );
    output(
        'Copy auto-increment IDs: ' .
        ($copyAutoIncrementId ? 'yes' : 'no')
    );
    output("Duplicate policy: {$duplicatePolicy}");
    output(
        'Disable foreign key checks: ' .
        ($disableForeignKeyChecks ? 'yes' : 'no')
    );
    output('Mode: ' . ($dryRun ? 'dry-run' : 'live'));
    output("Source row count: {$sourceRowCount}");
    output('');

    output('Matching columns copied:');
    output(
        !empty($matchingColumns)
            ? implode(', ', $matchingColumns)
            : 'None'
    );

    output('');
    output('Missing columns receiving NULL:');
    output(
        !empty($nullColumns)
            ? implode(', ', $nullColumns)
            : 'None'
    );

    output('');
    output('Columns using destination default:');
    output(
        !empty($defaultColumns)
            ? implode(', ', $defaultColumns)
            : 'None'
    );

    output('');
    output('Auto-increment columns left to MySQL:');
    output(
        !empty($skippedAutoIncrementColumns)
            ? implode(', ', $skippedAutoIncrementColumns)
            : 'None'
    );

    output('');
    output('Generated columns skipped:');
    output(
        !empty($skippedGeneratedColumns)
            ? implode(', ', $skippedGeneratedColumns)
            : 'None'
    );

    output('');
    output('Source-only columns ignored:');
    output(
        !empty($unmatchedSourceColumns)
            ? implode(', ', $unmatchedSourceColumns)
            : 'None'
    );

    output('');
    output('Generated migration query:');
    output('--------------------------------------------');
    output($migrationSql);
    output('--------------------------------------------');
    output('');

    if ($sourceRowCount === 0) {
        output('Source table has no records.');
        output('Nothing was inserted.');
        exit(0);
    }

    if ($dryRun) {
        output('Dry-run only. No data was inserted.');
        exit(0);
    }

    $pdo->beginTransaction();

    try {
        if ($disableForeignKeyChecks) {
            $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
        }

        $affectedRows = (int) $pdo->exec($migrationSql);

        if ($disableForeignKeyChecks) {
            $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
        }

        $pdo->commit();
    } catch (Throwable $exception) {
        if ($disableForeignKeyChecks) {
            try {
                $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
            } catch (Throwable) {
                // Keep the original migration error.
            }
        }

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $exception;
    }

    $destinationRowCount = (int) $pdo
        ->query(
            "SELECT COUNT(*) FROM {$destinationFullTable}"
        )
        ->fetchColumn();

    output('Migration completed successfully.');
    output("Affected rows: {$affectedRows}");
    output(
        "Destination total rows: {$destinationRowCount}"
    );
} catch (Throwable $exception) {
    if (
        isset($pdo) &&
        $pdo instanceof PDO &&
        $pdo->inTransaction()
    ) {
        $pdo->rollBack();
    }

    if (PHP_SAPI !== 'cli') {
        http_response_code(500);
    }

    output('Migration failed.');
    output('Error: ' . $exception->getMessage());

    exit(1);
}
