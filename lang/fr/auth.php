<?php

// Messages d'authentification en français. Laravel choisit ce fichier car
// APP_LOCALE=fr dans le .env. Sans lui, les messages seraient en anglais.

return [

    // Email inconnu ou mot de passe faux (même message que l'ancien
    // LoginController, volontairement vague : on ne dit pas si c'est
    // l'email ou le mot de passe qui est faux).
    'failed' => 'Identifiants incorrects.',

    'password' => 'Le mot de passe est incorrect.',

    // Trop de tentatives ratées (limitation à 5 par minute).
    'throttle' => 'Trop de tentatives de connexion. Veuillez réessayer dans :seconds secondes.',

];
