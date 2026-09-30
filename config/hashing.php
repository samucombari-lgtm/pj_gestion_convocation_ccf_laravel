<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Hash Driver
    |--------------------------------------------------------------------------
    */

    'driver' => 'bcrypt',

    /*
    |--------------------------------------------------------------------------
    | Bcrypt Options
    |--------------------------------------------------------------------------
    |
    | "verify" => false : DÉSACTIVE une vérification stricte que Laravel 13
    | fait en plus du hash lui-même : il regarde le préfixe du hash
    | ($2a$, $2b$, $2y$...) et refuse de continuer s'il ne reconnaît pas
    | EXACTEMENT "bcrypt" au sens de PHP. Or les hash de test.sql ont été
    | générés avec une librairie JavaScript qui produit un préfixe $2b$,
    | que PHP ne reconnaît pas toujours comme "bcrypt" selon les versions.
    | Le hash reste un VRAI hash bcrypt et password_verify() le valide très
    | bien : seule cette vérification "bonus" (protection contre une
    | éventuelle confusion d'algorithme si on changeait un jour pour
    | Argon2) posait problème. On la désactive plutôt que de régénérer les
    | mots de passe de test.sql, qu'on ne doit pas modifier.
    |
    */

    'bcrypt' => [
        'rounds' => env('BCRYPT_ROUNDS', 12),
        'verify' => false,
        'limit' => null,
    ],

];
