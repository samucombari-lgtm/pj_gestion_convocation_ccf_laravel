<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Connexion
    |--------------------------------------------------------------------------
    |
    | null signifie que la connexion par défaut de Laravel sera utilisée.
    |
    */

    'connection' => null,

    /*
    |--------------------------------------------------------------------------
    | Tables
    |--------------------------------------------------------------------------
    |
    | Les noms indiqués ici sont les noms logiques, sans préfixe.
    |
    */

    'tables' => [
    'include' => [],

    'exclude' => [
        'cache',
        'cache_locks',
        'failed_jobs',
        'jobs',
        'job_batches',
        'migrations',
        'password_reset_tokens',
        'sessions',
        'mcd_former',      // table pivot pure : pas de modèle dédié, gérée via belongsToMany
        'mcd_affecter',    // idem
    ],
],

'naming' => [
    'language' => 'fr',

    'models' => [
        'mcd_users'        => 'User',
        'mcd_utilisateurs' => 'Utilisateur',
        'mcd_roles'        => 'Role',
        'mcd_statuts'      => 'Statut',
        'mcd_genres'       => 'Genre',
        'mcd_epreuves'     => 'Epreuve',
        'mcd_lieux'        => 'Lieu',
        'mcd_jurys'        => 'Jury',
        'mcd_passer'       => 'Passer',
    ],

    'singular' => [],
    'invariable' => [],
],
    /*
    |--------------------------------------------------------------------------
    | Modèles
    |--------------------------------------------------------------------------
    */

    'models' => [
        'path' => app_path('Models'),
        'namespace' => 'App\\Models',

        'base_model' => true,
        'base_path' => app_path('Models/Base'),
        'base_namespace' => 'App\\Models\\Base',
        'base_suffix' => 'Base',
    ],

    /*
    |--------------------------------------------------------------------------
    | Migrations
    |--------------------------------------------------------------------------
    */

    'migrations' => [
        'path' => database_path('migrations'),
    ],
];
