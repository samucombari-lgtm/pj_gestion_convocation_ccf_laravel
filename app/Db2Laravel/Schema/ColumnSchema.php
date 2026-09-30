<?php

namespace App\Db2Laravel\Schema;

final class ColumnSchema
{
    public function __construct(
        public readonly string $name,

        /*
         * Définition SQL complète fournie par Laravel.
         * Exemples : varchar(100), bigint unsigned, decimal(8,2).
         */
        public readonly string $type,

        /*
         * Nom normalisé du type.
         * Exemples : varchar, bigint, decimal.
         */
        public readonly string $typeName,

        public readonly bool $nullable,
        public readonly mixed $default = null,
        public readonly bool $autoIncrement = false,
        public readonly ?string $collation = null,
        public readonly ?string $comment = null,
        public readonly ?array $generation = null,
    ) {
    }
}
