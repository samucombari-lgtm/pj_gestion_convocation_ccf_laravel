<?php

namespace App\Http\Controllers;

class ConvocationController extends Controller
{
    // J'ai choisi un contrôleur dédié (ConvocationController) plutôt
    // qu'une méthode de plus dans MenuController : MenuController n'a
    // qu'un seul travail (afficher le menu), et si on lui ajoute une
    // méthode par page du menu, il va vite devenir un gros fichier qui
    // mélange plein de sujets différents. Un contrôleur par fonctionnalité
    // (ici : "consulter sa convocation") reste plus simple à retrouver et
    // à maintenir. On fera pareil pour les pages GEST/JURY/ADMIN.

    public function index()
    {
        // Sécurité : cette page n'a de sens que pour un compte CAND. La
        // route est protégée par le middleware "auth" (donc on sait déjà
        // qu'il y a un utilisateur connecté), mais rien n'empêche un
        // gestionnaire ou un jury de taper l'URL directement. On bloque
        // ce cas avec un code HTTP 403 (accès refusé).
        if (! auth()->user()->hasRole('CAND')) {
            abort(403);
        }

        // auth()->user() = le compte de connexion (mcd_users).
        // ->utilisateur = la fiche métier liée (mcd_utilisateurs), via la
        // relation hasOne définie dans User.php.
        // ->convocations() = la relation hasMany qu'on vient d'ajouter
        // dans Utilisateur.php, vers la table mcd_passer.
        $convocations = auth()->user()->utilisateur
            ->convocations()
            // ->with(...) précharge les relations "epreuve", "epreuve.lieu"
            // et "jury" en 3 requêtes SQL groupées, au lieu de laisser la
            // vue déclencher une requête à chaque fois qu'elle affiche
            // $convocation->epreuve ou $convocation->jury (ce qu'on
            // appelle le problème du "N+1 requêtes").
            ->with(['epreuve.lieu', 'jury'])
            // Trie les convocations de la plus proche à la plus lointaine.
            ->orderBy('horaire')
            ->get();

        return view('candidat.convocation', [
            'convocations' => $convocations,
        ]);
    }
}
