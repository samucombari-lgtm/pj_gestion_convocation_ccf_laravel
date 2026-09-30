<?php

namespace App\Console\Commands;

use App\Db2Laravel\DatabaseInspector;
use App\Db2Laravel\Generators\ModelGenerator;
use App\Db2Laravel\Naming\ModelNameResolver;
use Illuminate\Console\Command;

class Db2Laravel extends Command
{
    protected $signature = 'db2laravel
                            {--connection= : Connexion à la base de données}
                            {--models : Générer les modèles Eloquent}';

    protected $description =
        'Analyse une base existante en vue de générer les modèles et migrations Laravel';

    public function handle(
        DatabaseInspector $inspector,
        ModelNameResolver $nameResolver,
        ModelGenerator $modelGenerator,
    ): int
    {
        $connectionName = $this->option('connection');

        $this->info('Analyse de la base de données...');

        $database = $inspector->inspect($connectionName);

        $this->newLine();
        $this->line('Connexion : <info>' . $database->connectionName . '</info>');
        $this->line('Base      : <info>' . $database->databaseName . '</info>');
        $this->line(
            'Préfixe   : <info>' .
            ($database->tablePrefix !== '' ? $database->tablePrefix : '(aucun)') .
            '</info>'
        );
        $this->newLine();

        foreach ($database->tables as $table) {
            $this->line(
                sprintf(
                    '<info>%s</info> → %s → <comment>%s</comment>',
                    $table->physicalName,
                    $table->name,
                    $nameResolver->resolve($table->name)
                )
            );

            $this->line('  Colonnes :');

            foreach ($table->columns as $column) {
                $attributes = [
                    $column->nullable ? 'NULL' : 'NOT NULL',
                ];

                if ($column->autoIncrement) {
                    $attributes[] = 'AUTO_INCREMENT';
                }

                if ($column->default !== null) {
                    $attributes[] = 'DEFAULT ' . $this->formatDefault($column->default);
                }

                $this->line(
                    sprintf(
                        '    %-25s %-30s %s',
                        $column->name,
                        $column->type,
                        implode(' ', $attributes)
                    )
                );
            }

            $this->line('  Index :');

            if ($table->indexes === []) {
                $this->line('    (aucun)');
            } else {
                foreach ($table->indexes as $index) {
                    $attributes = [];

                    if ($index->primary) {
                        $attributes[] = 'PRIMARY';
                    } elseif ($index->unique) {
                        $attributes[] = 'UNIQUE';
                    }

                    $suffix = $attributes !== []
                        ? ' ' . implode(' ', $attributes)
                        : '';

                    $this->line(
                        sprintf(
                            '    %-30s [%s]%s',
                            $index->name,
                            implode(', ', $index->columns),
                            $suffix
                        )
                    );
                }
            }

            $this->line('  Clés étrangères :');

            if ($table->foreignKeys === []) {
                $this->line('    (aucune)');
            } else {
                foreach ($table->foreignKeys as $foreignKey) {
                    $this->line(
                        sprintf(
                            '    [%s] → %s.[%s] (logique : %s)',
                            implode(', ', $foreignKey->columns),
                            $foreignKey->foreignPhysicalTable,
                            implode(', ', $foreignKey->foreignColumns),
                            $foreignKey->foreignTable
                        )
                    );

                    $actions = [];

                    if ($foreignKey->onUpdate !== null) {
                        $actions[] = 'ON UPDATE ' . $foreignKey->onUpdate;
                    }

                    if ($foreignKey->onDelete !== null) {
                        $actions[] = 'ON DELETE ' . $foreignKey->onDelete;
                    }

                    if ($actions !== []) {
                        $this->line('        ' . implode(' / ', $actions));
                    }
                }
            }

            $this->newLine();
        }

        if ($this->option('models')) {
            $this->info('Génération des modèles...');
            $this->newLine();

            foreach ($database->tables as $table) {
                $result = $modelGenerator->generate($table, $database);
                $modelName = $nameResolver->resolve($table->name);

                if ($result['base'] !== '') {
                    $this->line(
                        sprintf(
                            '  %-30s base régénérée',
                            $modelName
                        )
                    );
                }

                $this->line(
                    sprintf(
                        '  %-30s %s',
                        '',
                        $result['model_created']
                            ? 'modèle créé'
                            : 'modèle existant conservé'
                    )
                );
            }

            $this->newLine();
        }

        return self::SUCCESS;
    }

    private function formatDefault(mixed $default): string
    {
        if (is_bool($default)) {
            return $default ? 'true' : 'false';
        }

        if (is_string($default)) {
            return "'" . $default . "'";
        }

        return (string) $default;
    }
}
