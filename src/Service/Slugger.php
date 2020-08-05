<?php

namespace App\Service;

class Slugger
{
    /**
     * Retourne le slug d'une chaîne de caractère donnée
     * 
     * @param string $stringToSlug La chaîne à transformer en slug
     * @return string
     */
    public function slugify(string $stringToSlug)
    {
        // On retourne le slug
        return preg_replace( '/[^a-zA-Z0-9]+(?:-[a-zA-Z0-9]+)*/', '-', strtolower(trim(strip_tags($stringToSlug))) );
    }
}