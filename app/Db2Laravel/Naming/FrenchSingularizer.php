<?php

namespace App\Db2Laravel\Naming;

final class FrenchSingularizer
{
    public function singular(string $word): string
    {
        $word = mb_strtolower($word);

        $configured = config('db2laravel.naming.singular', []);

        if (array_key_exists($word, $configured)) {
            return $configured[$word];
        }

        $invariable = config('db2laravel.naming.invariable', []);

        if (in_array($word, $invariable, true)) {
            return $word;
        }

        /*
         * Dans les conventions retenues pour db2laravel, un verbe utilisé
         * dans un nom de table est à l'infinitif et reste inchangé.
         *
         * Cette détection est volontairement simple : les éventuelles
         * ambiguïtés peuvent être corrigées dans la configuration.
         */
        if ($this->looksLikeInfinitive($word)) {
            return $word;
        }

        /*
         * Quelques pluriels français courants.
         * Les exceptions lexicales (travaux, chevaux...) doivent être
         * indiquées dans naming.singular.
         */
        if (str_ends_with($word, 'eaux')) {
            return substr($word, 0, -1);
        }

        if (str_ends_with($word, 'aux')) {
            return substr($word, 0, -3) . 'al';
        }

        /*
         * Les mots terminés par s, x ou z sont généralement invariables
         * au singulier pour x/z, ou perdent leur s final.
         */
        if (str_ends_with($word, 's') && mb_strlen($word) > 1) {
            return mb_substr($word, 0, -1);
        }

        return $word;
    }

    private function looksLikeInfinitive(string $word): bool
    {
        return str_ends_with($word, 'er')
            || str_ends_with($word, 'ir')
            || str_ends_with($word, 're');
    }
}
