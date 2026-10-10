<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;

// Action appelée par Fortify quand le formulaire "Changer mon mot de passe"
// de la page Profil est envoyé (PUT /user/password, route Fortify
// "user-password.update", réservée aux utilisateurs connectés).
// Fortify s'occupe de la route et du contrôleur ; nous, on décide ICI des
// règles de validation et de ce qui est enregistré.
class UpdateUserPassword implements UpdatesUserPasswords
{
    // Mêmes règles que l'inscription : 8 caractères minimum + confirmation
    // (voir PasswordValidationRules.php).
    use PasswordValidationRules;

    /**
     * @param  array<string, string>  $input  les champs envoyés par le formulaire
     *
     * @throws ValidationException
     */
    public function update(User $user, array $input): void
    {
        // 'current_password:web' : Laravel compare le mot de passe tapé avec
        // le hash enregistré pour l'utilisateur connecté (garde "web").
        // Ça empêche quelqu'un qui trouverait une session restée ouverte de
        // changer le mot de passe sans le connaître.
        //
        // validateWithBag('updatePassword') : les erreurs sont rangées dans
        // un "sac" d'erreurs nommé updatePassword. Dans la vue, on les lit
        // donc avec $errors->updatePassword->first('...').
        Validator::make($input, [
            'current_password' => ['required', 'string', 'current_password:web'],
            'password' => $this->passwordRules(),
        ], [
            'current_password.current_password' => 'Le mot de passe actuel est incorrect.',
        ])->validateWithBag('updatePassword');

        // On ne modifie QUE le mot de passe (haché, jamais stocké en clair),
        // dans mcd_users. Identité, e-mail, rôle et statut ne changent pas.
        $user->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();
    }
}
