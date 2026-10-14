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

    // Accès : middleware('can:convocations.view') dans routes/web.php.
    // Un gestionnaire ou un jury qui tape l'URL reçoit une erreur 403.
    public function index()
    {
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
