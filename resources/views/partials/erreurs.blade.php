{{-- Message rouge affiché EN HAUT d'un formulaire quand la validation a
     échoué. Le détail de chaque erreur reste affiché sous le champ
     concerné (classe .field-error).
     $errors->any() vaut true s'il y a au moins une erreur.
     Utilisation dans une vue : @include('partials.erreurs') --}}
@if ($errors->any())
    <p class="alert alert-error">Le formulaire contient des erreurs : corrigez les champs indiqués en rouge.</p>
@endif
