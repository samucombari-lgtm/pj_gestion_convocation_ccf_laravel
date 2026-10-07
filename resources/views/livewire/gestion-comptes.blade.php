{{-- Vue du composant Livewire App\Livewire\GestionComptes.
     Règle Livewire : la vue d'un composant doit avoir UN SEUL élément
     racine (ici le <div> qui englobe tout). --}}
<div>
    {{-- wire:model.live : à chaque frappe, la propriété $recherche de la
         classe est mise à jour et la liste se recharge toute seule.
         .debounce.300ms : on attend 300 ms après la dernière touche, pour
         ne pas envoyer une requête à chaque lettre. --}}
    {{-- Mêmes classes que les formulaires (partie 9 de style.css) ;
         .filtres les aligne à gauche, juste au-dessus du tableau. --}}
    <div class="filtres">
        <div class="form-group">
            <label for="recherche">Rechercher (nom, prénom ou email) :</label>
            <input type="text" id="recherche" wire:model.live.debounce.300ms="recherche">
        </div>

        {{-- Case à cocher liée à $enAttente : n'affiche que les comptes
             Inactifs, par exemple ceux qui viennent de s'inscrire. --}}
        <label class="form-check">
            <input type="checkbox" wire:model.live="enAttente">
            Seulement les comptes en attente (Inactifs)
        </label>
    </div>

    @if ($message !== '')
        {{-- Message vert (succès) ou rouge (erreur) selon $erreur. --}}
        <p class="alert {{ $erreur ? 'alert-error' : 'alert-success' }}">{{ $message }}</p>
    @endif

    {{-- .table-defilement : si la fenêtre est étroite, le tableau défile
         horizontalement au lieu d'élargir toute la page. --}}
    <div class="table-defilement">
    <table class="table">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Statut</th>
                <th>Genre</th>
                <th>Classe</th>
                <th>N° candidat</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($utilisateurs as $utilisateur)
                {{-- wire:key : identifiant unique de la ligne, pour que
                     Livewire sache quelle ligne mettre à jour. --}}
                <tr wire:key="utilisateur-{{ $utilisateur->id }}">
                    {{-- .nowrap : le nom et l'email restent sur une ligne. --}}
                    <td class="nowrap">{{ $utilisateur->prenom }} {{ $utilisateur->nom }}</td>
                    <td class="nowrap">{{ $utilisateur->compte?->email }}</td>
                    <td>
                        {{-- wire:change : dès qu'on choisit un autre rôle,
                             on appelle changerRole(id, nouveau rôle).
                             $event.target.value = la valeur choisie. --}}
                        <select wire:change="changerRole({{ $utilisateur->id }}, $event.target.value)">
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}" @selected($role->id === $utilisateur->id_role)>{{ $role->nom }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        {{ $utilisateur->statut->nom }}
                        @if($utilisateur->code_statut === 'B')
                            <span class="text-danger">⚠</span>
                        @endif
                    </td>
                    <td>{{ $utilisateur->genre->nom }}</td>
                    <td>{{ $utilisateur->classe ?? '—' }}</td>
                    <td>{{ $utilisateur->numero_candidat ?? '—' }}</td>
                    <td>
                        {{-- Un bouton par statut, sauf le statut actuel
                             (inutile de proposer "Activer" à un compte
                             déjà actif). wire:click appelle la méthode
                             changerStatut() de la classe. wire:confirm
                             demande confirmation avant de bannir.
                             div.actions : boutons côte à côte sur une ligne.
                             Couleurs : Activer bleu, Désactiver gris,
                             Bannir rouge, Modifier blanc. --}}
                        <div class="actions">
                            @if ($utilisateur->code_statut !== 'A')
                                <button type="button" class="btn btn-primary btn-petit" wire:click="changerStatut({{ $utilisateur->id }}, 'A')">Activer</button>
                            @endif
                            @if ($utilisateur->code_statut !== 'I')
                                <button type="button" class="btn btn-secondaire btn-petit" wire:click="changerStatut({{ $utilisateur->id }}, 'I')">Désactiver</button>
                            @endif
                            @if ($utilisateur->code_statut !== 'B')
                                <button type="button" class="btn btn-danger btn-petit" wire:click="changerStatut({{ $utilisateur->id }}, 'B')" wire:confirm="Bannir ce compte ?">Bannir</button>
                            @endif
                            <a class="btn btn-petit" href="{{ route('utilisateurs.edit', $utilisateur) }}">Modifier</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">Aucun utilisateur ne correspond.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>
