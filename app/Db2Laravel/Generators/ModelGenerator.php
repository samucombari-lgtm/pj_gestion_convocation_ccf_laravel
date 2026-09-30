<?php

namespace App\Db2Laravel\Generators;

use App\Db2Laravel\Naming\ModelNameResolver;
use App\Db2Laravel\Schema\ColumnSchema;
use App\Db2Laravel\Schema\DatabaseSchema;
use App\Db2Laravel\Schema\TableSchema;
use Illuminate\Support\Str;
use RuntimeException;

final class ModelGenerator
{
    public function __construct(
        private readonly ModelNameResolver $nameResolver,
    ) {
    }

    /**
     * @return array{base: string, model: string, model_created: bool}
     */
    public function generate(TableSchema $table, DatabaseSchema $database): array
    {
        $modelName = $this->nameResolver->resolve($table->name);

        $modelsPath = rtrim(config('db2laravel.models.path'), '/');
        $modelsNamespace = trim(config('db2laravel.models.namespace'), '\\');

        $baseEnabled = (bool) config('db2laravel.models.base_model', true);
        $basePath = rtrim(config('db2laravel.models.base_path'), '/');
        $baseNamespace = trim(config('db2laravel.models.base_namespace'), '\\');
        $baseSuffix = config('db2laravel.models.base_suffix', 'Base');

        $baseClassName = $modelName . $baseSuffix;

        $this->ensureDirectory($modelsPath);

        if ($baseEnabled) {
            $this->ensureDirectory($basePath);

            $baseFile = $basePath . '/' . $baseClassName . '.php';

            file_put_contents(
                $baseFile,
                $this->buildBaseModel(
                    table: $table,
                    database: $database,
                    modelName: $modelName,
                    className: $baseClassName,
                    namespace: $baseNamespace,
                )
            );

            $modelFile = $modelsPath . '/' . $modelName . '.php';
            $created = false;

            if (!is_file($modelFile)) {
                file_put_contents(
                    $modelFile,
                    $this->buildChildModel(
                        modelName: $modelName,
                        namespace: $modelsNamespace,
                        baseClassName: $baseClassName,
                        baseNamespace: $baseNamespace,
                    )
                );

                $created = true;
            }

            return [
                'base' => $baseFile,
                'model' => $modelFile,
                'model_created' => $created,
            ];
        }

        $modelFile = $modelsPath . '/' . $modelName . '.php';

        file_put_contents(
            $modelFile,
            $this->buildStandaloneModel(
                table: $table,
                database: $database,
                modelName: $modelName,
                namespace: $modelsNamespace,
            )
        );

        return [
            'base' => '',
            'model' => $modelFile,
            'model_created' => true,
        ];
    }

    private function buildBaseModel(
        TableSchema $table,
        DatabaseSchema $database,
        string $modelName,
        string $className,
        string $namespace,
    ): string {
        $body = $this->buildStructuralProperties($table, $modelName);
        $relations = $this->buildBelongsToRelations($table);
        $relations .= $this->buildInverseRelations($table, $database);

        return <<<PHP
<?php

namespace {$namespace};

use Illuminate\Database\Eloquent\Model;

/**
 * Classe générée automatiquement par db2laravel.
 *
 * Ne pas modifier directement cette classe : elle peut être régénérée.
 */
abstract class {$className} extends Model
{
{$body}{$relations}
}

PHP;
    }

    private function buildChildModel(
        string $modelName,
        string $namespace,
        string $baseClassName,
        string $baseNamespace,
    ): string {
        return <<<PHP
<?php

namespace {$namespace};

use {$baseNamespace}\\{$baseClassName};

class {$modelName} extends {$baseClassName}
{
}

PHP;
    }

    private function buildStandaloneModel(
        TableSchema $table,
        DatabaseSchema $database,
        string $modelName,
        string $namespace,
    ): string {
        $body = $this->buildStructuralProperties($table, $modelName);
        $relations = $this->buildBelongsToRelations($table);
        $relations .= $this->buildInverseRelations($table, $database);

        return <<<PHP
<?php

namespace {$namespace};

use Illuminate\Database\Eloquent\Model;

class {$modelName} extends Model
{
{$body}{$relations}
}

PHP;
    }

    private function buildStructuralProperties(
        TableSchema $table,
        string $modelName,
    ): string {
        $lines = [];

        /*
         * Le nom logique de la table est toujours explicite.
         * Le préfixe SQL appartient à la connexion Laravel et n'est jamais
         * inscrit dans le modèle.
         */
        $lines[] = "    protected \$table = " . var_export($table->name, true) . ";";

        $primary = $this->primaryIndex($table);

        if ($primary !== null && count($primary->columns) === 1) {
            $primaryKey = $primary->columns[0];

            if ($primaryKey !== 'id') {
                $lines[] = "    protected \$primaryKey = " . var_export($primaryKey, true) . ";";
            }

            $primaryColumn = $this->column($table, $primaryKey);

            if ($primaryColumn !== null) {
                if (!$primaryColumn->autoIncrement) {
                    $lines[] = "    public \$incrementing = false;";
                }

                $keyType = $this->eloquentKeyType($primaryColumn);

                if ($keyType !== 'int') {
                    $lines[] = "    protected \$keyType = " . var_export($keyType, true) . ";";
                }
            }
        } elseif ($primary !== null && count($primary->columns) > 1) {
            $columns = implode(', ', $primary->columns);
            $lines[] = "    // ATTENTION : clé primaire composite détectée : {$columns}";
            $lines[] = "    // Eloquent ne gère pas nativement les clés primaires composites.";
            $lines[] = "    public \$incrementing = false;";
        }

        if (!$this->hasLaravelTimestamps($table)) {
            $lines[] = "    public \$timestamps = false;";
        }

        $fillable = $this->fillableColumns($table, $primary);

        if ($fillable !== []) {
            $items = implode(
                ",\n",
                array_map(
                    static fn (string $name): string => "        " . var_export($name, true),
                    $fillable
                )
            );

            $lines[] = "    protected \$fillable = [\n{$items},\n    ];";
        }

        $casts = $this->casts($table);

        if ($casts !== []) {
            $items = implode(
                ",\n",
                array_map(
                    static fn (string $name, string $cast): string =>
                        "            " . var_export($name, true) . " => " . var_export($cast, true),
                    array_keys($casts),
                    array_values($casts)
                )
            );

            $lines[] = <<<PHP
    protected function casts(): array
    {
        return [
{$items},
        ];
    }
PHP;
        }

        return implode("\n\n", $lines);
    }

    private function fillableColumns(TableSchema $table, ?object $primary): array
    {
        $autoIncrementPrimary = null;

        if ($primary !== null && count($primary->columns) === 1) {
            $primaryColumn = $this->column($table, $primary->columns[0]);

            if ($primaryColumn?->autoIncrement) {
                $autoIncrementPrimary = $primaryColumn->name;
            }
        }

        $excluded = array_filter([
            $autoIncrementPrimary,
            'created_at',
            'updated_at',
        ]);

        return array_values(
            array_map(
                static fn (ColumnSchema $column): string => $column->name,
                array_filter(
                    $table->columns,
                    static fn (ColumnSchema $column): bool =>
                        !in_array($column->name, $excluded, true)
                        && $column->generation === null
                )
            )
        );
    }

    /**
     * Génère uniquement les casts suffisamment sûrs à partir du type SQL.
     *
     * @return array<string, string>
     */
    private function casts(TableSchema $table): array
    {
        $casts = [];

        foreach ($table->columns as $column) {
            $type = strtolower($column->typeName);

            $cast = match ($type) {
                'tinyint' => $this->tinyIntCast($column),
                'smallint', 'mediumint', 'int', 'integer', 'bigint' => 'integer',
                'decimal', 'numeric' => $this->decimalCast($column),
                'float', 'double', 'real' => 'float',
                'bool', 'boolean' => 'boolean',
                'date' => 'date',
                'datetime', 'timestamp' => 'datetime',
                'json' => 'array',
                default => null,
            };

            if ($cast !== null) {
                $casts[$column->name] = $cast;
            }
        }

        return $casts;
    }

    private function tinyIntCast(ColumnSchema $column): string
    {
        /*
         * MySQL/MariaDB utilisent très fréquemment TINYINT(1) pour un booléen.
         * On ne considère booléen que cette forme explicite.
         */
        if (preg_match('/^tinyint\s*\(\s*1\s*\)/i', $column->type) === 1) {
            return 'boolean';
        }

        return 'integer';
    }

    private function decimalCast(ColumnSchema $column): string
    {
        if (preg_match(
            '/^(?:decimal|numeric)\s*\(\s*\d+\s*,\s*(\d+)\s*\)/i',
            $column->type,
            $matches
        ) === 1) {
            return 'decimal:' . $matches[1];
        }

        /*
         * Sans échelle connue, on évite d'inventer une précision.
         */
        return 'string';
    }

    private function buildInverseRelations(
        TableSchema $table,
        DatabaseSchema $database,
    ): string {
        $methods = [];
        $usedNames = [];

        foreach ($database->tables as $sourceTable) {
            foreach ($sourceTable->foreignKeys as $foreignKey) {
                if (
                    $foreignKey->foreignTable !== $table->name
                    || count($foreignKey->columns) !== 1
                    || count($foreignKey->foreignColumns) !== 1
                ) {
                    continue;
                }

                $foreignColumn = $foreignKey->columns[0];
                $localKey = $foreignKey->foreignColumns[0];
                $sourceModel = $this->nameResolver->resolve($sourceTable->name);
                $isUnique = $this->isSingleColumnUnique($sourceTable, $foreignColumn);

                $relationName = $this->inverseRelationName(
                    $sourceTable,
                    $foreignColumn,
                    $isUnique
                );

                if (isset($usedNames[$relationName])) {
                    $relationName = $this->inverseRelationNameWithRole(
                        $sourceTable,
                        $foreignColumn,
                        $isUnique
                    );
                }

                if (isset($usedNames[$relationName])) {
                    $relationName = $this->numberedRelationName(
                        $relationName,
                        $usedNames
                    );
                }

                $usedNames[$relationName] = true;

                $targetNamespace = trim(
                    config('db2laravel.models.namespace'),
                    '\\'
                );
                $targetClass = '\\' . $targetNamespace . '\\' . $sourceModel;

                $relationType = $isUnique ? 'HasOne' : 'HasMany';
                $method = $isUnique ? 'hasOne' : 'hasMany';

                $methods[] = <<<PHP


    public function {$relationName}(): {$relationType}
    {
        return \$this->{$method}(
            {$targetClass}::class,
            '{$foreignColumn}',
            '{$localKey}'
        );
    }
PHP;
            }
        }

        return implode('', $methods);
    }

    private function isSingleColumnUnique(
        TableSchema $table,
        string $column,
    ): bool {
        foreach ($table->indexes as $index) {
            if (
                ($index->unique || $index->primary)
                && count($index->columns) === 1
                && $index->columns[0] === $column
            ) {
                return true;
            }
        }

        return false;
    }

    private function inverseRelationName(
        TableSchema $sourceTable,
        string $foreignColumn,
        bool $singular,
    ): string {
        $model = $this->nameResolver->resolve($sourceTable->name);

        return $singular
            ? Str::camel($model)
            : Str::camel($sourceTable->name);
    }

    private function inverseRelationNameWithRole(
        TableSchema $sourceTable,
        string $foreignColumn,
        bool $singular,
    ): string {
        $role = $this->relationNameFromForeignKeyColumn($foreignColumn);
        $model = $this->nameResolver->resolve($sourceTable->name);

        $base = $singular
            ? Str::camel($model)
            : Str::camel($sourceTable->name);

        return $base . ucfirst($role);
    }

    private function buildBelongsToRelations(TableSchema $table): string
    {
        $methods = [];
        $usedNames = [];

        foreach ($table->foreignKeys as $foreignKey) {
            if (count($foreignKey->columns) !== 1 || count($foreignKey->foreignColumns) !== 1) {
                $columns = implode(', ', $foreignKey->columns);
                $methods[] = "\n\n    // ATTENTION : relation non générée pour la clé étrangère composite : {$columns}";
                continue;
            }

            $foreignColumn = $foreignKey->columns[0];
            $ownerKey = $foreignKey->foreignColumns[0];
            $relationName = $this->relationNameFromForeignKeyColumn($foreignColumn);

            if (isset($usedNames[$relationName])) {
                $candidate = Str::camel($foreignColumn);
                $relationName = isset($usedNames[$candidate])
                    ? $this->numberedRelationName($relationName, $usedNames)
                    : $candidate;
            }
            $usedNames[$relationName] = true;

            $targetModel = $this->nameResolver->resolve($foreignKey->foreignTable);
            $targetNamespace = trim(config('db2laravel.models.namespace'), '\\');
            $targetClass = '\\' . $targetNamespace . '\\' . $targetModel;

            $methods[] = <<<PHP


    public function {$relationName}(): BelongsTo
    {
        return \$this->belongsTo(
            {$targetClass}::class,
            '{$foreignColumn}',
            '{$ownerKey}'
        );
    }
PHP;
        }

        return implode('', $methods);
    }

    private function relationNameFromForeignKeyColumn(string $column): string
    {
        $name = $column;

        if (str_starts_with($name, 'id_')) {
            $name = substr($name, 3);
        } elseif (str_ends_with($name, '_id')) {
            $name = substr($name, 0, -3);
        }

        return Str::camel($name !== '' ? $name : $column);
    }

    /** @param array<string, bool> $usedNames */
    private function numberedRelationName(string $base, array $usedNames): string
    {
        $number = 2;
        do {
            $candidate = $base . $number++;
        } while (isset($usedNames[$candidate]));

        return $candidate;
    }

    private function primaryIndex(TableSchema $table): ?object
    {
        foreach ($table->indexes as $index) {
            if ($index->primary) {
                return $index;
            }
        }

        return null;
    }

    private function column(TableSchema $table, string $name): ?ColumnSchema
    {
        foreach ($table->columns as $column) {
            if ($column->name === $name) {
                return $column;
            }
        }

        return null;
    }

    private function hasLaravelTimestamps(TableSchema $table): bool
    {
        $names = array_map(
            static fn (ColumnSchema $column): string => $column->name,
            $table->columns
        );

        return in_array('created_at', $names, true)
            && in_array('updated_at', $names, true);
    }

    private function eloquentKeyType(ColumnSchema $column): string
    {
        return match (strtolower($column->typeName)) {
            'tinyint', 'smallint', 'mediumint', 'int', 'integer', 'bigint' => 'int',
            default => 'string',
        };
    }

    private function ensureDirectory(string $path): void
    {
        if (is_dir($path)) {
            return;
        }

        if (!mkdir($path, 0775, true) && !is_dir($path)) {
            throw new RuntimeException(
                "Impossible de créer le répertoire : {$path}"
            );
        }
    }
}
