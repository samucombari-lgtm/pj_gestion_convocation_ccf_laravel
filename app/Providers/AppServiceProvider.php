<?php

namespace App\Providers;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->definirLesGates();
    }

    // Autorisations pilotées par la base : on crée une "Gate" Laravel
    // pour chaque action de la table mcd_actions (ex. 'epreuves.create').
    // Une Gate est une règle nommée qui répond true/false pour
    // l'utilisateur connecté. Elle est ensuite utilisée par :
    //   - le middleware can:epreuves.create dans routes/web.php ;
    //   - @can('epreuves.create') dans les vues Blade ;
    //   - Gate::authorize('epreuves.create') dans le composant Livewire.
    // Une action absente de mcd_actions n'a pas de Gate : Laravel refuse
    // alors l'accès (erreur 403), même à l'administrateur.
    private function definirLesGates(): void
    {
        // boot() est exécuté à CHAQUE démarrage de Laravel, y compris
        // pour les commandes "php artisan" et pendant "composer install"
        // (package:discover). À ce moment-là, la base peut être
        // inaccessible ou la table mcd_actions pas encore créée : sans ce
        // try / catch, la requête planterait et bloquerait ces commandes.
        // On note le problème dans storage/logs/laravel.log et on continue
        // sans Gate (toutes les pages protégées répondront 403).
        try {
            $nomsDesPermissions = Permission::pluck('nom');
        } catch (\Throwable $erreur) {
            Log::warning('Autorisations non chargées (table mcd_actions absente ou base inaccessible) : '.$erreur->getMessage());

            return;
        }

        foreach ($nomsDesPermissions as $permission) {
            Gate::define($permission, function (User $user) use ($permission) {
                return $user->hasPermission($permission);
            });
        }
    }
}
