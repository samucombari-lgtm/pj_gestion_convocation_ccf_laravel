<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\ResetsUserPasswords;

// Action appelée par Fortify quand le formulaire "nouveau mot de passe"
// (POST /reset-password) est envoyé. Avant d'arriver ici, Laravel a DÉJÀ
// vérifié que le jeton du lien est valide, non expiré, et qu'il correspond
// bien à cet e-mail (table password_reset_tokens). Juste après, Laravel
// supprime ce jeton : le lien ne peut donc servir qu'une seule fois.
class ResetUserPassword implements ResetsUserPasswords
{
    // Mêmes règles que l'inscription : 8 caractères minimum + confirmation.
    use PasswordValidationRules;

    /**
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
     */
    public function reset(User $user, array $input): void
    {
        Validator::make($input, [
            'password' => $this->passwordRules(),
        ])->validate();

        // On ne modifie QUE le mot de passe (haché avec Hash::make, jamais
        // stocké en clair), dans mcd_users. Le statut (mcd_utilisateurs)
        // n'est PAS touché : un compte Inactif ou Banni peut changer son
        // mot de passe, mais la connexion lui restera refusée par le
        // contrôle du statut (Fortify::authenticateUsing).
        // Cette action ne connecte pas non plus l'utilisateur : Fortify le
        // renvoie sur la page de connexion avec un message.
        $user->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();
    }
}
