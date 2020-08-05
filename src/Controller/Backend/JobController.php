<?php

namespace App\Controller\Backend;

use App\Entity\Job;
use App\Form\Type\JobType;
use App\Repository\JobRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/backend/job")
 */
class JobController extends AbstractController
{
    /**
     * Lister les jobs
     * 
     * @Route("/", name="backend_job_list", methods={"GET"})
     */
    public function list(JobRepository $jobRepository)
    {
        $jobs = $jobRepository->getJobsAndDepartmentsOrderedByName();

        // dump($jobs);

        return $this->render('backend/job/list.html.twig', [
            'jobs' => $jobs,
        ]);
    }

    /**
     * Ajout d'un job
     * 
     * @Route("/add", name="backend_job_add", methods={"GET","POST"})
     */
    public function add(Request $request)
    {
        // On crée une nouvelle entité job
        $job = new Job();

        // On crée le formulaire d'ajout du job
        // ... sur lequel on 'map' le job
        $form = $this->createForm(JobType::class, $job);

        // On demande au formulaire de 'prendre en charge' la requête
        $form->handleRequest($request);

        // Si form est soumis ? Est-il valide ?
        if ($form->isSubmitted() && $form->isValid()) {
            // A ce stade, l'entité $job contient déjà toutes les infos du form
            // car mappé via le form depuis handleRequest()
            // On sauvegarde le job
            $em = $this->getDoctrine()->getManager();
            $em->persist($job);
            $em->flush();

            $this->addFlash('success', 'Le job <b>'. $job->getName() .'</b> a été ajouté.');

            // On redirige vers la liste des jobs
            return $this->redirectToRoute('backend_job_list');
        }

        return $this->render('backend/job/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Modification d'un job
     * 
     * @Route("/edit/{id<\d+>}", name="backend_job_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Job $job, $id)
    {
        if ($job === null) {
            // 404 ?
            throw $this->createNotFoundException('Ce job n\'existe pas');
        }

        // On crée le formulaire d'édition d'un film
        // ... sur lequel on 'map' le film
        $form = $this->createForm(JobType::class, $job);

        // On demande au formulaire de 'prendre en charge' la requête
        $form->handleRequest($request);

        // Si form est soumis ? Est-il valide ?
        if ($form->isSubmitted() && $form->isValid()) {
            // A ce stade, l'entité $job est connu de Doctrine car mappé via le form depuis handleRequest()
            // On met à jour l'entité
            $job->setUpdatedAt(new \DateTime());
            // On sauvegarde le film (donc sans persist ici)
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('success', 'Modification effectuée.');

            // On redirige vers la même page
            return $this->redirectToRoute('backend_job_edit', ['id' => $id]);
        }

        return $this->render('backend/job/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Suppression d'un job
     * 
     * @Route("/delete/{id<\d+>}", name="backend_job_delete", methods={"DELETE"})
     */
    public function delete(Job $job, Request $request)
    {
        // On récupère le token soumis
        $token = $request->request->get('token');

        // Si le token est invalide
        if(!$this->isCsrfTokenValid('delete-item', $token)) {
            $this->addFlash('danger', 'Le formulaire est invalide, veuillez le renvoyer.');

            // On redirige vers la liste
            return $this->redirectToRoute('backend_job_list');
        }

        if ($job === null) {
            // 404 ?
            throw $this->createNotFoundException('Ce job n\'existe pas');
        }

        $em = $this->getDoctrine()->getManager();
        // On supprime le job
        $em->remove($job);
        $em->flush();

        $this->addFlash('success', 'Le job <b>'. $job->getName() .'</b> a été supprimé.');

        // On redirige vers la liste
        return $this->redirectToRoute('backend_job_list');
    }

    /**
     * Affiche un job
     * 
     * @Route("/{id<\d+>}", name="backend_job_show", methods={"GET"})
     */
    public function show(Job $job = null)
    {
        // si on veut récupérer la main sur la 404
        // = null en valeur par défaut du param
        if ($job === null) {
            // 404 ?
            throw $this->createNotFoundException('Ce job n\'existe pas.');
        }

        return $this->render('backend/job/show.html.twig', [
            'job' => $job,
        ]);
    }
}
