{{-- Barre de menu affichée en haut de toutes les pages connectées
     (incluse par layouts/app.blade.php avec @include('partials.menu')).

     Chaque bloc @if n'affiche ses liens QUE pour le bon rôle, avec les
     méthodes hasRole() et isJury() du modèle User. Le menu ne fait
     qu'AFFICHER des liens : la vraie protection reste dans chaque
     contrôleur (erreur 403 si le rôle ne correspond pas).

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
        @if (auth()->user()->hasRole('CAND'))
            <li>
                <a href="{{ route('convocation') }}" @class(['actif' => request()->routeIs('convocation')])>Ma convocation</a>
            </li>
        @endif

        {{-- Jury enseignant ET jury professionnel : isJury() --}}
        @if (auth()->user()->isJury())
            <li>
                {{-- <details> s'ouvre et se ferme au clic sur <summary>,
                     sans JavaScript (voir la partie 4 de style.css). --}}
                <details class="deroulant">
                    <summary @class(['actif' => request()->routeIs('jury.*')])>Jury</summary>
                    <ul>
                        <li><a href="{{ route('jury.planning') }}">Mon planning</a></li>
                        <li><a href="{{ route('jury.candidats') }}">Mes candidats à évaluer</a></li>
                    </ul>
                </details>
            </li>
        @endif

        {{-- Gestionnaire. "Affecter un jury" et "Voir les convocations"
             concernent UNE épreuve précise : ces liens sont sur chaque
             ligne de la liste des épreuves, pas dans le menu. --}}
        @if (auth()->user()->hasRole('GEST'))
            <li>
                <details class="deroulant">
                    <summary @class(['actif' => request()->routeIs('epreuves.*')])>Épreuves</summary>
                    <ul>
                        <li><a href="{{ route('epreuves.index') }}">Liste des épreuves</a></li>
                        <li><a href="{{ route('epreuves.create') }}">Créer une session d'épreuves</a></li>
                    </ul>
                </details>
            </li>
        @endif

        {{-- Administrateur --}}
        @if (auth()->user()->hasRole('ADMIN'))
            <li>
                <details class="deroulant">
                    <summary @class(['actif' => request()->routeIs('utilisateurs.*', 'parametrage.*')])>Administration</summary>
                    <ul>
                        <li><a href="{{ route('utilisateurs.index') }}">Gestion des comptes</a></li>
                        <li><a href="{{ route('parametrage.index') }}">Paramétrage</a></li>
                    </ul>
                </details>
            </li>
        @endif
    </ul>

    {{-- À droite : nom de l'utilisateur connecté et bouton Déconnexion.
         La déconnexion DOIT être un formulaire POST avec @csrf (route
         Fortify "logout") : un simple lien (GET) ne fonctionnerait pas. --}}
    <div class="utilisateur">
        <span>{{ auth()->user()->name }}</span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-primary">Déconnexion</button>
        </form>
    </div>
</header>
