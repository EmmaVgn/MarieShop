<?php

namespace App\Controller;

use Stripe\Stripe;

use App\Service\Mail;
use App\Cart\CartService;
use Stripe\Checkout\Session;
use App\Event\OrderSuccessEvent;
use App\Repository\UserRepository;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PaymentController extends AbstractController
{

    protected $em;
    protected $cartService;

    public function __construct(EntityManagerInterface $em, CartService $cartService)
    {
        $this->em = $em;
        $this->cartService = $cartService;
    }

    /**
     * Etape de vérification avant confirmation du paiement
     */
    #[Route('/commande/checkout/{reference}', name: 'checkout')]
    public function payment(OrderRepository $repository, $reference, EntityManagerInterface $em): Response
    {
        // Récupération des produits de la dernière commande et formattage dans un tableau pour Stripe
        $order = $repository->findOneByReference($reference);
        if (!$order) {
            throw $this->createNotFoundException('Cette commande n\'existe pas');
        }
        $products = $order->getOrderDetails()->getValues();
        $productsForStripe = [];
        foreach ($products as $item) {
            $productsForStripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $item->getPrice(),
                    'product_data' => [
                        'name' => $item->getProduct()
                    ]
                ],
                'quantity' => $item->getQuantity()
            ];
        }
        // Ajout des frais de livraison
        $productsForStripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $order->getCarrierPrice(),
                'product_data' => [
                    'name' => $order->getCarrierName()
                ]
            ],
            'quantity' => 1
        ];
        Stripe::setApiKey('sk_test_51PYmV7Ek3IUhoeZsQjEW2etfg1ttQ2lAviitRad21SaSYgs3OXvRoNVeSzXAg9Vh7cbaEpnCE54xu7JRPI4UKfFP00TQMb2avc');


        $YOUR_DOMAIN = 'http://localhost:8000';

        
        // Création de la session Stripe avec les données du panier
        $checkout_session = Session::create([
            'line_items' => $productsForStripe,
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/commande/valide/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/commande/echec/{CHECKOUT_SESSION_ID}',
        ]);
        $order->setStripeSession($checkout_session->id);
        $em->flush();
        return $this->redirect($checkout_session->url);
    }

    /**
     * Méthode appelée lorsque le paiement est validé
     */
    #[Route('/commande/valide/{stripeSession}', name: 'payment_success')]
    public function paymentSuccess(OrderRepository $repository, $stripeSession, EntityManagerInterface $em, CartService $cart, UserRepository $user, EventDispatcherInterface $dispatcher): Response 
    {
        $order = $repository->findOneByStripeSession($stripeSession);

        if (!$order || $order->getUser() != $this->getUser()) {
            throw $this->createNotFoundException('Commande inaccessible');
        }
        if (!$order->getState()) {
            $order->setState(1);
            $em->flush();
        }

        // Suppression du panier une fois la commande validée
        $this->cartService->empty();
        // 3.1 Lancer un événement qui permet d'envoyer un mail à la prise d'une commande
        $orderEvent = new OrderSuccessEvent($order);
        $dispatcher->dispatch($orderEvent, 'order.success');
        // 4. Je redirige avec un flash vers la liste des commandes
        $this->addFlash('success', 'La commande a été payée et confirmée');
        return $this->redirectToRoute('account_order');
    }

        /**
         * Commande annullée (clic sur retour dans la fenêtre)
         */
        #[Route('/commande/echec/{stripeSession}', name: 'payment_fail')]
        public function paymentFail(OrderRepository $repository, $stripeSession) 
        {
            $order = $repository->findOneByStripeSession($stripeSession);
            if (!$order || $order->getUser() != $this->getUser()) {
                throw $this->createNotFoundException('Commande innaccessible');
            }

            return $this->render('payment/fail.html.twig', [
                'order' => $order
            ]);
        }
}