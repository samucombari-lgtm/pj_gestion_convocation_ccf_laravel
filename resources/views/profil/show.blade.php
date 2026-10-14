@extends('layouts.app')

@section('title', 'Mon profil')

@section('content')
    <h1>Mon profil</h1>

    {{-- Carte 1 : informations du compte, en LECTURE SEULE. Seul un
         administrateur peut les modifier (Gestion des comptes).
         "?->" et "??" : si une information manque, on affiche "—" au lieu
         de provoquer une erreur. --}}
    <div class="card">
        <h2>Mes informations</h2>

        <dl class="fiche">
            <dt>Nom</dt>
            <dd>{{ $utilisateur?->nom ?? '—' }}</dd>

            <dt>Prénom</dt>
            <dd>{{ $utilisateur?->prenom ?? '—' }}</dd>

            <dt>E-mail</dt>
            <dd>{{ $user->email }}</dd>

            <dt>Rôle</dt>
            <dd>{{ $utilisateur?->role?->nom ?? '—' }}</dd>

            <dt>Statut</dt>
            <dd>{{ $utilisateur?->statut?->nom ?? '—' }}</dd>

            <dt>Genre</dt>
            <dd>{{ $utilisateur?->genre?->nom ?? '—' }}</dd>

            <dt>Classe</dt>
            <dd>{{ $utilisateur?->classe ?? '—' }}</dd>

            {{-- Le numéro de candidat n'est affiché que s'il est renseigné
                 (seuls les candidats en ont un). C'est de l'affichage, pas
                 un droit d'accès : on teste la donnée, pas le rôle. --}}
            @if ($utilisateur?->numero_candidat)
                <dt>Numéro de candidat</dt>
                <dd>{{ $utilisateur?->numero_candidat ?? '—' }}</dd>
            @endif
        </dl>
    </div>

    {{-- Carte 2 : changement du mot de passe, traité par Fortify.
         Fortify range les erreurs de ce formulaire dans le sac d'erreurs
         "updatePassword" (voir UpdateUserPassword.php) : on les lit donc
         avec $errors->updatePassword, et non avec $errors tout court. --}}
    <div class="card">
        <h2>Changer mon mot de passe</h2>

        {{-- Après un changement réussi, Fortify revient sur cette page avec
             session('status') = 'password-updated'. --}}
        @if (session('status') === 'password-updated')
            <p class="alert alert-success">Mot de passe modifié.</p>
        @endif

        @if ($errors->updatePassword->any())
            <p class="alert alert-error">Le formulaire contient des erreurs : corrigez les champs indiqués en rouge.</p>
        @endif

        {{-- Un formulaire HTML ne sait envoyer que GET ou POST : @method('PUT')
             ajoute un champ caché qui dit à Laravel de le traiter comme un
             PUT, la méthode attendue par la route Fortify. On ne remet jamais
             un mot de passe dans le champ (pas de old()). --}}
        <form method="POST" action="{{ route('user-password.update') }}" class="form">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="current_password">Mot de passe actuel :</label>
                <input type="password" id="current_password" name="current_password" autocomplete="current-password">
                @if ($errors->updatePassword->first('current_password'))
                    <span class="field-error">{{ $errors->updatePassword->first('current_password') }}</span>
                @endif
            </div>

            <div class="form-group">
                <label for="password">Nouveau mot de passe (8 caractères minimum) :</label>
                <input type="password" id="password" name="password" autocomplete="new-password">
                @if ($errors->updatePassword->first('password'))
                    <span class="field-error">{{ $errors->updatePassword->first('password') }}</span>
                @endif
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirmation du nouveau mot de passe :</label>
                <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Changer mon mot de passe</button>
            </div>
        </form>
    </div>
@endsection
