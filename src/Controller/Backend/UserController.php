<?php

namespace App\Controller\Backend;

use App\Entity\User;
use App\Form\Type\UserType;
use App\Form\Type\UserEditType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/backend/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_list", methods={"GET"})
     */
    public function list(UserRepository $userRepository): Response
    {
        return $this->render('backend/user/list.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/add", name="user_add", methods={"GET","POST"})
     */
    public function add(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        // handleRequest() met à jour l'objet User avec les infos du form
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // On encode le mot de passe de l'utilisateur
            // en lui passant l'objet User en cours et en second paramètre le mdp à encoder
            $encodedPassword = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encodedPassword);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'L\'utilisateur <b>'. $user->getEmail() .'</b> a été ajouté.');

            return $this->redirectToRoute('user_list');
        }

        return $this->render('backend/user/add.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id<\d+>}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('backend/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/edit/{id<\d+>}", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData();
            // Si un mot de passe a été renseigné, on modifie celui du user
            if (!empty($password)) {
                // On encode le mot de passe de l'utilisateur
                // en lui passant l'objet User en cours et en second paramètre le mdp à encoder
                $encodedPassword = $passwordEncoder->encodePassword($user, $password);
                $user->setPassword($encodedPassword);
            }

            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Modification effectuée.');

            return $this->redirectToRoute('user_edit', ['id' => $user->getId()]);
        }

        return $this->render('backend/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id<\d+>}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete-item', $request->request->get('token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        $this->addFlash('success', 'L\'utilisateur <b>'. $user->getEmail() .'</b> a été supprimé.');

        return $this->redirectToRoute('user_list');
    }
}
