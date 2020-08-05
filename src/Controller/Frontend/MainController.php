<?php

namespace App\Controller\Frontend;

use App\Entity\Movie;
use App\Entity\Casting;
use App\Repository\MovieRepository;
use App\Repository\PersonRepository;
use App\Repository\CastingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    /**
     * Affiche la liste des films
     * 
     * @Route("/", name="home")
     */
    public function list(MovieRepository $movieRepository, Request $request)
    {
        // Y a-t-il une recherche dans l'url
        $search = $request->query->get('search');
        // On récupère la liste des films avec ou sans recherche
        $movies = $movieRepository->getMoviesOrderedByTitleAscQb($search);
        // dump($movies);

        return $this->render('frontend/main/index.html.twig', [
            'movies' => $movies,
        ]);
    }

    /**
     * Affiche un film selon un id
     * 
     * @Route("/movie/show/{slug}", name="movie_show", requirements={"slug"="[a-z0-9-]+"})
     */
    public function movieShow(Movie $movie = null, CastingRepository $castingRepository)
    {
        // Avec le ParamConverter, si on veut récupérer la main sur la 404
        // on doit définir la valeur de $movie à null par défaut dans la méthode movieShow()

        // 404 ?
        if ($movie === null) {
            throw $this->createNotFoundException('Ce film n\'existe pas.');
        }

        // On récupère tous les castings d'un film via une requête perso
        // pour réduire le nombre de requêtes SQL et optimiser le code
        $castings = $castingRepository->getCastingsJoinedToPersonByMovieDql($movie);
        
        // Récupération du film via ParamConverter
        return $this->render('frontend/main/movie_show.html.twig', [
            'movie' => $movie,
            'castings' => $castings,
        ]);
    }

    /**
     * @Route("/casting/add", name="casting_add")
     */
    public function castingAdd(MovieRepository $movieRepository, PersonRepository $personRepository, EntityManagerInterface $em) 
    {
        // Notre casting
        $casting = new Casting();

        // On lui associe un film existant
        $movie = $movieRepository->find(3);
        $casting->setMovie($movie);

        // On lui associe une personne existante
        $person = $personRepository->find(1);
        $casting->setPerson($person);

        // On attribue le rôle
        $casting->setRole('Christian Wolff');
        // on attribue l'ordre d'apparition dans le générique
        $casting->setCreditOrder(1);
        // La date de création
        $casting->setCreatedAt(new \DateTime());

        // Persist & Flush
        $em->persist($casting);
        $em->flush();

        // Redirection vers la home
        return $this->redirectToRoute('home');
    }
}
