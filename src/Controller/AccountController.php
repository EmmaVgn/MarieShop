<?php

namespace App\Controller;

use App\Entity\User;
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
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em): Response
{
    // Get the currently authenticated user
    $user = $this->getUser();
    
    if (!$user instanceof User) {
        throw $this->createAccessDeniedException('User not found');
    }
    
    $form = $this->createForm(ChangePasswordFormType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $old_password = $form->get('old_password')->getData();
        $new_password = $form->get('new_password')->getData();
        $isOldPasswordValid = $passwordHasher->isPasswordValid($user, $old_password);
        if ($isOldPasswordValid) {
            $password = $passwordHasher->hashPassword($user, $new_password);
            $user->setPassword($password);
            $em->flush();
            $this->addFlash(
                'notice', 
                'Mot de passe modifié :)'
            );
            return $this->redirectToRoute('account');
        } else {
            $this->addFlash(
                'notice', 
                'Mot de passe actuel erroné :('
            );
        }
    }

    return $this->render('account/password.html.twig', [
        'form' => $form->createView(),
    ]);
}


        /**
     * Affiche la vue de toutes les commandes d'un utilisateur
     */
    #[Route('/compte/commandes', name: 'account_orders')]
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
    public function showOrder($reference, OrderRepository $orderRepository ): Response
    {
        $order = $orderRepository->findOneBy ([
            'reference' => $reference
        ]);
       

        if (!$order || $order->getUser() != $this->getUser()) {
            throw $this->createNotFoundException('Commande innaccessible');
        }
        
        return $this->render('account/order.html.twig', [
            'order' => $order,
           
        ]);
    }
}
