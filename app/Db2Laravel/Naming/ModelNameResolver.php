<?php

namespace App\Db2Laravel\Naming;

use Illuminate\Support\Str;

final class ModelNameResolver
{
    public function __construct(
        private readonly FrenchSingularizer $singularizer,
    ) {
    }

    public function resolve(string $table): string
    {
        $configured = config('db2laravel.naming.models', []);

        if (array_key_exists($table, $configured)) {
            return $configured[$table];
        }

        $parts = preg_split('/_+/', $table, -1, PREG_SPLIT_NO_EMPTY);

        $parts = array_map(
            fn (string $part): string => $this->singularizer->singular($part),
            $parts
        );

        return Str::studly(implode('_', $parts));
    }
}
