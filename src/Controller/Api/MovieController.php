<?php

namespace App\Controller\Api;

use App\Repository\MovieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api", name="api_")
 */
class MovieController extends AbstractController
{
    /**
     * @Route("/movies", name="movies_get")
     */
    public function getMoviesCollection(MovieRepository $movieRepository)
    {
        // On va chercher les films
        $movies = ['movies' => $movieRepository->findAll()];

        return $this->json($movies, 200, [], ['groups' => 'movies_get']);
    }
}
