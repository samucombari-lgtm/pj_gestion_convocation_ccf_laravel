<?php

namespace App\Livewire;

use App\Models\Role;
use App\Models\Utilisateur;
use Livewire\Component;

// Composant Livewire de la page "Gestion des utilisateurs" (rôle ADMIN).
//
// Un composant Livewire = une classe PHP (ici) + une vue Blade
// (resources/views/livewire/gestion-comptes.blade.php). Les propriétés
// "public" de la classe sont visibles dans la vue, et la vue peut appeler
// les méthodes "public" de la classe (wire:click, wire:change...) SANS
// recharger toute la page : Livewire envoie une petite requête au serveur,
// exécute la méthode, puis met à jour uniquement ce qui a changé à l'écran.
class GestionComptes extends Component
{
    // Statuts autorisés pour les boutons. On liste ce qui est AUTORISÉ
    // (même logique que la connexion, qui n'accepte que 'A').
    private const STATUTS = ['A', 'I', 'B'];

    // Texte tapé dans le champ de recherche (lié par wire:model dans la vue).
    public string $recherche = '';

    // Case "en attente" : n'afficher que les comptes Inactifs ('I'),
    // c'est-à-dire ceux qu'un administrateur n'a pas encore validés.
    public bool $enAttente = false;

    // Message de confirmation ou d'erreur affiché en haut du tableau.
    public string $message = '';
    public bool $erreur = false;

    // IMPORTANT (sécurité) : chaque clic sur un bouton Livewire est une
    // NOUVELLE requête HTTP envoyée au serveur. Vérifier le rôle une seule
    // fois à l'affichage de la page ne suffit donc pas : quelqu'un pourrait
    // envoyer ces requêtes à la main. On revérifie donc ADMIN dans mount()
    // (premier affichage) ET au début de chaque action.
    private function verifierAdmin(): void
    {
        if (! auth()->user()?->hasRole('ADMIN')) {
            abort(403);
        }
    }

    // mount() est appelée une seule fois, quand le composant s'affiche
    // pour la première fois (l'équivalent d'un constructeur).
    public function mount(): void
    {
        $this->verifierAdmin();
    }

    // Change le statut d'un compte : 'A' Actif, 'I' Inactif, 'B' Banni.
    public function changerStatut(int $id, string $code): void
    {
        $this->verifierAdmin();

        if (! in_array($code, self::STATUTS, true)) {
            $this->afficher('Statut inconnu.', true);
            return;
        }

        // Protection : l'administrateur connecté ne peut pas se désactiver
        // ni se bannir lui-même (il ne pourrait plus se reconnecter, et
        // s'il est le seul admin, plus personne ne pourrait gérer l'appli).
        if ($id === (int) auth()->id() && $code !== 'A') {
            $this->afficher('Vous ne pouvez pas désactiver ou bannir votre propre compte.', true);
            return;
        }

        // findOrFail : erreur 404 si l'id n'existe pas (au lieu d'un plantage).
        $utilisateur = Utilisateur::findOrFail($id);
        $utilisateur->update(['code_statut' => $code]);

        $this->afficher("Statut de {$utilisateur->prenom} {$utilisateur->nom} modifié.");
    }

    // Change le rôle d'un compte (un des rôles de mcd_roles).
    public function changerRole(int $id, $idRole): void
    {
        $this->verifierAdmin();

        // On vérifie que le rôle choisi existe vraiment en base : la valeur
        // vient du navigateur, elle peut avoir été modifiée.
        $role = Role::find((int) $idRole);
        if (! $role) {
            $this->afficher('Rôle inconnu.', true);
            return;
        }

        // Protection : l'admin connecté ne peut pas s'enlever son propre
        // rôle ADMIN (il perdrait l'accès à cette page immédiatement).
        if ($id === (int) auth()->id() && $role->code !== 'ADMIN') {
            $this->afficher('Vous ne pouvez pas retirer votre propre rôle administrateur.', true);
            return;
        }

        $utilisateur = Utilisateur::findOrFail($id);
        $utilisateur->update(['id_role' => $role->id]);

        $this->afficher("{$utilisateur->prenom} {$utilisateur->nom} est maintenant « {$role->nom} ».");
    }

    // Petite méthode utilitaire pour remplir le message affiché.
    private function afficher(string $texte, bool $erreur = false): void
    {
        $this->message = $texte;
        $this->erreur = $erreur;
    }

    // render() est rappelée après chaque action ou chaque frappe dans la
    // recherche : la liste est donc toujours à jour.
    public function render()
    {
        $requete = Utilisateur::with(['role', 'statut', 'genre', 'compte'])
            ->orderBy('nom')
            ->orderBy('prenom');

        // Recherche sur le nom, le prénom ou l'email (l'email est dans
        // mcd_users, d'où le whereHas sur la relation "compte").
        // Le "function ($q)" regroupe les OR entre parenthèses en SQL,
        // pour qu'ils ne se mélangent pas avec le filtre "en attente".
        if ($this->recherche !== '') {
            $texte = '%'.$this->recherche.'%';
            $requete->where(function ($q) use ($texte) {
                $q->where('nom', 'like', $texte)
                    ->orWhere('prenom', 'like', $texte)
                    ->orWhereHas('compte', fn ($c) => $c->where('email', 'like', $texte));
            });
        }

        if ($this->enAttente) {
            $requete->where('code_statut', 'I');
        }

        return view('livewire.gestion-comptes', [
            'utilisateurs' => $requete->get(),
            'roles' => Role::orderBy('nom')->get(),
        ]);
    }
}
