<?php

namespace App\Db2Laravel;

use App\Db2Laravel\Schema\ColumnSchema;
use App\Db2Laravel\Schema\DatabaseSchema;
use App\Db2Laravel\Schema\ForeignKeySchema;
use App\Db2Laravel\Schema\IndexSchema;
use App\Db2Laravel\Schema\TableSchema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseInspector
{
    public function inspect(?string $connectionName = null): DatabaseSchema
    {
        $connectionName ??= config('db2laravel.connection')
            ?? config('database.default');

        $connection = DB::connection($connectionName);

        $databaseName = $connection->getDatabaseName();
        $prefix = $connection->getTablePrefix();

        $schema = Schema::connection($connectionName);

        $tables = [];

        /*
         * getTables() reçoit explicitement le nom de la base afin de ne pas
         * inspecter les autres bases auxquelles l'utilisateur SQL aurait accès.
         */
        foreach ($schema->getTables($databaseName) as $tableInfo) {
            $physicalName = $tableInfo['name'];
            $name = $this->removePrefix($physicalName, $prefix);

            if (!$this->tableIsIncluded($name)) {
                continue;
            }

            /*
             * Important : les méthodes getColumns(), getIndexes() et
             * getForeignKeys() appliquent elles-mêmes le préfixe de connexion.
             * On leur transmet donc le nom LOGIQUE, sans préfixe.
             */
            $columns = [];

            foreach ($schema->getColumns($name) as $columnInfo) {
                $columns[] = new ColumnSchema(
                    name: $columnInfo['name'],
                    type: $columnInfo['type'],
                    typeName: $columnInfo['type_name'],
                    nullable: $columnInfo['nullable'],
                    default: $columnInfo['default'] ?? null,
                    autoIncrement: (bool) ($columnInfo['auto_increment'] ?? false),
                    collation: $columnInfo['collation'] ?? null,
                    comment: $columnInfo['comment'] ?? null,
                    generation: $columnInfo['generation'] ?? null,
                );
            }

            $indexes = [];

            foreach ($schema->getIndexes($name) as $indexInfo) {
                $indexes[] = new IndexSchema(
                    name: $indexInfo['name'],
                    columns: $indexInfo['columns'] ?? [],
                    unique: (bool) ($indexInfo['unique'] ?? false),
                    primary: (bool) ($indexInfo['primary'] ?? false),
                );
            }

            $foreignKeys = [];

            foreach ($schema->getForeignKeys($name) as $foreignKeyInfo) {
                $foreignPhysicalTable = $foreignKeyInfo['foreign_table'];
                $foreignTable = $this->removePrefix(
                    $foreignPhysicalTable,
                    $prefix
                );

                $foreignKeys[] = new ForeignKeySchema(
                    name: $foreignKeyInfo['name'] ?? '',
                    columns: $foreignKeyInfo['columns'] ?? [],
                    foreignPhysicalTable: $foreignPhysicalTable,
                    foreignTable: $foreignTable,
                    foreignColumns: $foreignKeyInfo['foreign_columns'] ?? [],
                    onUpdate: $foreignKeyInfo['on_update'] ?? null,
                    onDelete: $foreignKeyInfo['on_delete'] ?? null,
                );
            }

            $tables[] = new TableSchema(
                physicalName: $physicalName,
                name: $name,
                columns: $columns,
                indexes: $indexes,
                foreignKeys: $foreignKeys,
            );
        }

        return new DatabaseSchema(
            connectionName: $connectionName,
            databaseName: $databaseName,
            tablePrefix: $prefix,
            tables: $tables,
        );
    }

    private function removePrefix(string $table, string $prefix): string
    {
        if ($prefix === '' || !str_starts_with($table, $prefix)) {
            return $table;
        }

        return substr($table, strlen($prefix));
    }

    private function tableIsIncluded(string $table): bool
    {
        $include = config('db2laravel.tables.include', []);
        $exclude = config('db2laravel.tables.exclude', []);

        if ($include !== [] && !in_array($table, $include, true)) {
            return false;
        }

        if (in_array($table, $exclude, true)) {
            return false;
        }

        return true;
    }
}
