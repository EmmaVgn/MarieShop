<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\UserRepository;
use App\Repository\OrderRepository;
use App\Form\ChangePasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Espace membre (sécurisé)
 */
class AccountController extends AbstractController
{
    #[Route('/compte', name: 'account')]
    public function index(): Response
    {
        return $this->render('account/index.html.twig');
    }

    /**
     * Permet la modification du mot de passe d'un utilisateur sur une page dédiée
     */
    #[Route('/compte/mot-de-passe', name: 'account_password')]
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em, UserRepository $user): Response
    {
         /** @var User */
        $user->getUser();
        $form = $this->createForm(ChangePasswordFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $oldPassword = $form->get('old_password')->getData();
            $newPassword = $form->get('new_password')->getData();
            if ($passwordHasher->isPasswordValid($user->getUser(), $oldPassword)) {
                $hashedPassword = $passwordHasher->hashPassword($user->getUser(), $newPassword);
                $user->setPassword($hashedPassword);
                $em->flush();
                $this->addFlash('success', 'Mot de passe modifié :)');
                return $this->redirectToRoute('account');
            } else {
                $this->addFlash('error', 'Mot de passe actuel erroné :(');
            }
        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

        /**
     * Affiche la vue de toutes les commandes d'un utilisateur
     */
    #[Route('/compte/commandes', name: 'account_order')]
    public function showOrders(OrderRepository $repository): Response
    {
        $orders = $repository->findPaidOrdersByUser($this->getUser());
        return $this->render('account/orders.html.twig', [
            'orders' => $orders
        ]);
    }

    /**
     * Affiche une commande
     */
    #[Route('/compte/commandes/{reference}', name: 'account_order')]
    public function showOrder(Order $order): Response
    {
        if (!$order || $order->getUser() != $this->getUser()) {
            throw $this->createNotFoundException('Commande innaccessible');
        }
        return $this->render('account/order.html.twig', [
            'order' => $order
        ]);
    }
}
