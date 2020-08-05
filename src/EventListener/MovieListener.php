<?php

namespace App\EventListener;

use App\Entity\Movie;
use App\Service\Slugger;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class MovieListener
{
    private $slugger;

    public function __construct(Slugger $slugger)
    {
        $this->slugger = $slugger;
    }

    // the entity listener methods receive two arguments:
    // the entity instance and the lifecycle event
    public function updateSlug(Movie $movie, LifecycleEventArgs $event)
    {
        // On met à jour le slug : Attention au SEO déjà présent
        // (prévoir des redirections)
        // cf : https://www.leptidigital.fr/webmarketing/seo/comment-faire-redirection-301-htaccess-exemples-13824/
        $movie->setSlug($this->slugger->slugify($movie->getTitle()));
    }
}