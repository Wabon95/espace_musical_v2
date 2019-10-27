<?php

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

    public function timestampToDatetime(String $timestamp) {
        $dateTime = new \DateTime();
        $dateTime->setTimestamp($timestamp);
        return $dateTime;
    }
}