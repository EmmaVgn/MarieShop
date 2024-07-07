<?php

namespace App\Controller;

use App\Form\OrderFormType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    #[Route('/commande', name: 'order')]
    public function index(SessionInterface $session): Response
    {
        $user = $this->getUser();

        if (!$user) {
            // Redirect to login if the user is not authenticated
            return $this->redirectToRoute('app_login');
        }

        if (!$user->getAddresses()->count()) {
            // Set session and redirect if the user has no addresses
            $session->set('order', 1);
            return $this->redirectToRoute('account_address_new');
        }

        // Create the form for placing an order
        $form = $this->createForm(OrderFormType::class, null, [
            'user' => $user
        ]);

        return $this->render('order/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
