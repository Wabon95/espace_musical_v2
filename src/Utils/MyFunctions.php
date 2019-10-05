<?php

// TODO: Contraintes entité Ad
// TODO: méthode d'ajout Ad
// TODO: Méthode de récupération de toutes les ads
// TODO: Méthode de récupération d'une ad en particulier via son slug
// TODO: méthode de modification Ad
// TODO: méthode de suppression Ad

namespace App\Utils;

class MyFunctions {

    public function multiple_array_key_exist(Array $properties, Array $arrayToCheck): Array {
        $errors = [];
        foreach ($properties as $value) {
            if (!array_key_exists($value, $arrayToCheck)) {
                $errors[] = "Vous devez renseigner une propriété : '".$value."'.";
            }
        }
        return $errors;
    }
}