{{-- Barre de menu affichée en haut de toutes les pages connectées
     (incluse par layouts/app.blade.php avec @include('partials.menu')).

     Chaque lien n'est affiché QUE si l'utilisateur a l'action
     correspondante (table mcd_autoriser, via son rôle) :
       - @can('action') ... @endcan : affiché si l'action est autorisée ;
       - @canany(['a', 'b']) ... @endcanany : affiché si AU MOINS UNE des
         actions est autorisée (sert pour les menus déroulants : on
         n'affiche pas un menu qui serait vide).
     Le menu ne fait qu'AFFICHER des liens : la vraie protection reste
     le middleware can: de chaque route (erreur 403 sinon).

     request()->routeIs('...') renvoie true si la page affichée correspond
     à cette route : on ajoute alors la classe "actif" pour surligner le
     lien. L'étoile (*) veut dire "toutes les routes qui commencent par". --}}
<header class="navbar">
    <a class="marque" href="{{ route('menu') }}">Convocations E5/E6</a>

    <ul class="liens">
        <li>
            <a href="{{ route('menu') }}" @class(['actif' => request()->routeIs('menu')])>Accueil</a>
        </li>

        {{-- Candidat --}}
        @can('convocations.view')
            <li>
                <a href="{{ route('convocation') }}" @class(['actif' => request()->routeIs('convocation')])>Ma convocation</a>
            </li>
        @endcan

        {{-- Jury (enseignant et professionnel) --}}
        @canany(['planning.viewAny', 'evaluations.viewAny'])
            <li>
                {{-- <details> s'ouvre et se ferme au clic sur <summary>,
                     sans JavaScript (voir la partie 4 de style.css). --}}
                <details class="deroulant">
                    <summary @class(['actif' => request()->routeIs('jury.*')])>Jury</summary>
                    <ul>
                        @can('planning.viewAny')
                            <li><a href="{{ route('jury.planning') }}">Mon planning</a></li>
                        @endcan
                        @can('evaluations.viewAny')
                            <li><a href="{{ route('jury.candidats') }}">Mes candidats à évaluer</a></li>
                        @endcan
                    </ul>
                </details>
            </li>
        @endcanany

        {{-- Gestionnaire. "Affecter un jury" et "Voir les convocations"
             concernent UNE épreuve précise : ces liens sont sur chaque
             ligne de la liste des épreuves, pas dans le menu. --}}
        @canany(['epreuves.viewAny', 'epreuves.create'])
            <li>
                <details class="deroulant">
                    <summary @class(['actif' => request()->routeIs('epreuves.*')])>Épreuves</summary>
                    <ul>
                        @can('epreuves.viewAny')
                            <li><a href="{{ route('epreuves.index') }}">Liste des épreuves</a></li>
                        @endcan
                        @can('epreuves.create')
                            <li><a href="{{ route('epreuves.create') }}">Créer une session d'épreuves</a></li>
                        @endcan
                    </ul>
                </details>
            </li>
        @endcanany

        {{-- Administrateur --}}
        @canany(['utilisateurs.viewAny', 'parametrage.viewAny'])
            <li>
                <details class="deroulant">
                    <summary @class(['actif' => request()->routeIs('utilisateurs.*', 'parametrage.*')])>Administration</summary>
                    <ul>
                        @can('utilisateurs.viewAny')
                            <li><a href="{{ route('utilisateurs.index') }}">Gestion des comptes</a></li>
                        @endcan
                        @can('parametrage.viewAny')
                            <li><a href="{{ route('parametrage.index') }}">Paramétrage</a></li>
                        @endcan
                    </ul>
                </details>
            </li>
        @endcanany
    </ul>

    {{-- À droite : menu "Profil", le même pour tous (il est en dehors
         des @can ci-dessus). C'est aussi un <details> : il s'ouvre au
         clic, sans JavaScript. Il contient le nom de l'utilisateur, le lien
         vers sa page Profil et le bouton Déconnexion.
         La déconnexion DOIT être un formulaire POST avec @csrf (route
         Fortify "logout") : un simple lien (GET) ne fonctionnerait pas. --}}
    <div class="utilisateur">
        <details class="deroulant menu-profil">
            <summary>Profil</summary>
            <ul>
                <li class="nom-utilisateur">{{ auth()->user()->name }}</li>
                <li><a href="{{ route('profil') }}">Mon profil</a></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit">Déconnexion</button>
                    </form>
                </li>
            </ul>
        </details>
    </div>
</header>
