<?php

namespace App\Controller\Backend;

use App\Entity\Movie;
use App\Form\Type\MovieType;
use App\Repository\MovieRepository;
use App\Service\MessageGenerator;
use App\Service\Slugger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/backend/movie")
 */
class MovieController extends AbstractController
{
    /**
     * Lister les films
     * 
     * @Route("/", name="backend_movie_list", methods={"GET"})
     */
    public function list(MovieRepository $movieRepository)
    {
        $movies = $movieRepository->findBy([], [
            'title' => 'ASC',
        ]);

        return $this->render('backend/movie/list.html.twig', [
            'movies' => $movies,
        ]);
    }

    /**
     * Ajout d'un film
     * 
     * @Route("/add", name="backend_movie_add", methods={"GET","POST"})
     */
    public function add(Request $request, MessageGenerator $messageGenerator, Slugger $slugger)
    {
        // On crée une nouvelle entité Movie
        $movie = new Movie();

        // On crée le formulaire d'ajout du film
        // ... sur lequel on 'map' le film
        $form = $this->createForm(MovieType::class, $movie);

        // On demande au formulaire de 'prendre en charge' la requête
        $form->handleRequest($request);

        // Si form est soumis ? Est-il valide ?
        if ($form->isSubmitted() && $form->isValid()) {
            // A ce stade, l'entité $movie contient déjà toutes les infos du form
            // car mappé via le form depuis handleRequest()
            // On sauvegarde le film
            $em = $this->getDoctrine()->getManager();

            $em->persist($movie);
            $em->flush();

            $this->addFlash('success', 'Le film <b>'. $movie->getTitle() .'</b> a été ajouté.');
            // Utilisation d'un services
            $this->addFlash('success', $messageGenerator->getHappyMessage());

            // On redirige vers la home
            return $this->redirectToRoute('backend_movie_list');
        }

        return $this->render('backend/movie/add.html.twig', [
            // createView() permet de récupérer
            // la représentation HTML du form
            'form' => $form->createView(),
        ]);
    }

    /**
     * Modification d'un film
     * 
     * @Route("/edit/{id<\d+>}", name="backend_movie_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Movie $movie = null, $id, Slugger $slugger)
    {
        if ($movie === null) {
            // 404 ?
            throw $this->createNotFoundException('Ce film n\'existe pas');
        }

        // On crée le formulaire d'édition d'un film
        // ... sur lequel on 'map' le film
        $form = $this->createForm(MovieType::class, $movie);

        // On demande au formulaire de 'prendre en charge' la requête
        $form->handleRequest($request);

        // Si form est soumis ? Est-il valide ?
        if ($form->isSubmitted() && $form->isValid()) {
            // A ce stade, l'entité $movie est connu de Doctrine car mappé via le form depuis handleRequest()
            
            // On sauvegarde le film (donc sans persist ici)
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('success', 'Modification effectuée.');

            // On redirige vers la même page
            return $this->redirectToRoute('backend_movie_edit', ['id' => $id]);
        }

        return $this->render('backend/movie/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Suppression d'un film
     * 
     * @Route("/delete/{id<\d+>}", name="backend_movie_delete", methods={"DELETE"})
     */
    public function delete(Movie $movie = null, Request $request)
    {
        // On récupère le token soumis
        $token = $request->request->get('token');

        // Si le token est invalide
        if(!$this->isCsrfTokenValid('delete-item', $token)) {
            $this->addFlash('danger', 'Le formulaire est invalide, veuillez le renvoyer.');

            // On redirige vers la liste
            return $this->redirectToRoute('backend_movie_list');
        }

        if ($movie === null) {
            // 404 ?
            throw $this->createNotFoundException('Ce film n\'existe pas');
        }

        $em = $this->getDoctrine()->getManager();
        // On supprime le film
        $em->remove($movie);
        $em->flush();

        $this->addFlash('success', 'Le film <b>'. $movie->getTitle() .'</b> a été supprimé.');

        // On redirige vers la liste
        return $this->redirectToRoute('backend_movie_list');
    }

    /**
     * Affiche un film
     * 
     * @Route("/{id<\d+>}", name="backend_movie_show", methods={"GET"})
     */
    public function show(Movie $movie = null)
    {
        // si on veut récupérer la main sur la 404
        // = null en valeur par défaut du param
        if ($movie === null) {
            // 404 ?
            throw $this->createNotFoundException('Ce film n\'existe pas.');
        }

        return $this->render('backend/movie/show.html.twig', [
            'movie' => $movie,
        ]);
    }
}
