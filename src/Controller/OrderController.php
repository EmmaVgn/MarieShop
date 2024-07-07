<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderFormType;
use App\Cart\CartService;
use App\Entity\OrderDetails;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    #[Route('/commande', name: 'order')]
    public function index(SessionInterface $session, CartService $cart): Response
    {
        $user = $this->getUser();
        $cartProducts = $cart->getDetailedCartItems();

        // Redirect if cart is empty
        if (empty($cartProducts['products'])) {
            return $this->redirectToRoute('product_display');
        }
        
        // Redirect if user has no address
        if (!$user->getAddresses()->getValues()) {      
            $session->set('order', 1);
            return $this->redirectToRoute('account_address_new');
        }

        $form = $this->createForm(OrderFormType::class, null, [
            'user' => $user
        ]);

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cartProducts,
            'totalPrice' => $cartProducts['totals']['price']
        ]);
    }

        /**
     * Enregistrement des données "en dur" de la commande contenant adresse, transporteur et produits
     * Les relations ne sont pas directement utilisées pour la persistance des données dans les entités Order et OrderDetails
     * pour éviter des incohérences dans le cas ou des modifications seraient faites sur les autres entités par la suite
     *
     * @param Cart $cart
     * @param Request $request
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
            $deliveryString = $address->getFirstname() . ' ' . $address->getLastname()
                . '<br>' . $address->getPhone()
                . '<br>' . ($address->getCompany() ?? '')
                . '<br>' . $address->getAddress()
                . '<br>' . $address->getPostal()
                . '<br>' . $address->getCity()
                . '<br>' . $address->getCountry();

            $order = new Order();
            $date = new \DateTime();
            $order
                ->setUser($this->getUser())
                ->setCreatedAt($date)
                ->setCarrierName($form->get('carriers')->getData()->getName())
                ->setCarrierPrice($form->get('carriers')->getData()->getPrice())
                ->setDelivery($deliveryString)
                ->setState(0)
                ->setReference($date->format('YmdHis') . '-' . uniqid());

            $em->persist($order);

            foreach ($cartProducts['products'] as $item) {
                $orderDetails = new OrderDetails();
                $orderDetails
                    ->setBindedOrder($order)
                    ->setProduct($item['product']->getName())
                    ->setQuantity($item['quantity'])
                    ->setPrice($item['product']->getPrice())
                    ->setTotal($item['product']->getPrice() * $item['quantity']);

                $em->persist($orderDetails);
            }

            $em->flush();

            return $this->render('order/add.html.twig', [
                'cart' => $cartProducts,
                'totalPrice' => $cartProducts['totals']['price'],
                'order' => $order
            ]);
        }

        return $this->redirectToRoute('cart');
    }
}
