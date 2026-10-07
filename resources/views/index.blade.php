<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des convocations E5/E6</title>
</head>
<body>
    <h1>Gestion des convocations aux épreuves E5/E6</h1>

    <p>
        Application de gestion des convocations aux épreuves E5 et E6 du
        BTS SIO, évaluées en CCF, au lycée Saint Joseph.
    </p>

    {{-- Chaque bouton est un petit formulaire en GET : cliquer dessus
         ouvre simplement la page indiquée dans "action". (Mettre un
         <button> à l'intérieur d'un lien <a> est interdit en HTML.) --}}
    <form method="GET" action="{{ route('login') }}">
        <button type="submit">Se connecter</button>
    </form>

    {{-- La route "register" (inscription) n'existera qu'une fois
         Fortify installé. Route::has('register') renvoie false tant
         qu'elle n'existe pas : le bouton est alors simplement masqué,
         au lieu de provoquer une erreur "Route [register] not defined". --}}
    @if (Route::has('register'))
        <form method="GET" action="{{ route('register') }}">
            <button type="submit">S'inscrire</button>
        </form>
    @endif
</body>
</html>
