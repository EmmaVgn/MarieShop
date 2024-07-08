<?php

namespace App\Controller;

use App\Entity\Order;
use App\Cart\CartService;
use App\Form\OrderFormType;
use App\Entity\OrderDetails;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\User\UserInterface;

class OrderController extends AbstractController
{
    /**
     * Récupération du panier, choix de l'adresse et du transporteur
     *
     * @param SessionInterface $session
     * @param CartService $cart
     * @return Response
     */
    #[Route('/commande', name: 'order')]
    public function index(SessionInterface $session, CartService $cart, UserRepository $user): Response
    {
        /** @var User */
        $user = $this->getUser();
        $cartProducts = $cart->getDetailedCartItems();

        // // Redirection si panier vide
        if (empty($cartProducts)) {
            return $this->redirectToRoute('product_display');
        }


        //Redirection si utilisateur n'a pas encore d'adresse

        if (!$user->getAddresses() || $user->getAddresses()->isEmpty()) {
            // Cette ligne peut être problématique si getAddresses() renvoie une collection
            // La collection ne peut pas être convertie en chaîne directement
            $session->set('order', 1);
            return $this->redirectToRoute('account_address_new');
        }

        $form = $this->createForm(OrderFormType::class, null, [
            'user' => $user     //Permet de passer l'utilisateur courant dans le tableau d'options du OrderType
        ]); 

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cartProducts,
            'totalPrice' => $cart->getTotal()
        ]);
    }

    /**
     * Enregistrement des données de la commande
     *
     * @param CartService $cart
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/commande/recap', name: 'order_add', methods: 'POST')]
    public function summary(CartService $cart, Request $request, EntityManagerInterface $em): Response
    {
        $cartProducts = $cart->getDetailedCartItems();

        $form = $this->createForm(OrderFormType::class, null, [
            'user' => $this->getUser()
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $address = $form->get('addresses')->getData();
            $carrier = $form->get('carriers')->getData();

            $deliveryString = sprintf(
                '%s %s<br>%s<br>%s%s<br>%s<br>%s<br>%s',
                $address->getFirstname(),
                $address->getLastname(),
                $address->getPhone(),
                $address->getCompany() ? $address->getCompany() . '<br>' : '',
                $address->getAddress(),
                $address->getPostal(),
                $address->getCity(),
                $address->getCountry()
            );

            $order = new Order();
            $order->setUser($this->getUser())
                ->setCreatedAt(new \DateTime())
                ->setCarrierName($carrier->getName())
                ->setCarrierPrice($carrier->getPrice())
                ->setDelivery($deliveryString)
                ->setState(0)
                ->setReference((new \DateTime())->format('YmdHis') . '-' . uniqid());

            $em->persist($order);

            foreach ($cartProducts as $item) {
                $orderDetails = new OrderDetails();
                $orderDetails->setBindedOrder($order)
                    ->setProduct($item->getProduct()->getName())
                    ->setQuantity($item->getQuantity())
                    ->setPrice($item->getProduct()->getPrice())
                    ->setTotal($item->getTotal());

                $em->persist($orderDetails);
            }

            $em->flush();

            return $this->render('order/add.html.twig', [
                'cart' => $cartProducts,
                'totalPrice' => $cart->getTotal(),
                'order' => $order
            ]);
        }

        return $this->redirectToRoute('cart');
    }
}
