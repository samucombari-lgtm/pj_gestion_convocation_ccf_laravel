<?php

// Messages de la réinitialisation du mot de passe ("mot de passe oublié"),
// affichés par Fortify sur les pages forgot-password et login.
// Le TEXTE de l'e-mail envoyé (objet, phrases, bouton) est traduit, lui,
// dans lang/fr.json : Laravel y cherche les phrases anglaises écrites
// telles quelles dans son code (ex. "Reset Password Notification").

return [

    'reset' => 'Votre mot de passe a été réinitialisé.',
    'sent' => 'Nous vous avons envoyé par e-mail le lien de réinitialisation du mot de passe.',
    'throttled' => 'Veuillez patienter avant de réessayer.',
    'token' => 'Ce lien de réinitialisation du mot de passe n\'est pas valide.',
    'user' => 'Aucun utilisateur n\'a été trouvé avec cette adresse e-mail.',

];
