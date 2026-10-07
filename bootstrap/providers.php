<?php

use App\Providers\AppServiceProvider;
use App\Providers\FortifyServiceProvider;

return [
    AppServiceProvider::class,
    // Ajouté à la main (on n'a pas lancé "fortify:install") : configure
    // Fortify pour notre projet (vues, connexion, inscription).
    FortifyServiceProvider::class,
];
