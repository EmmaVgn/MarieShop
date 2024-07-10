<?php

namespace App\Controller;

use App\Cart\CartService;
use App\Repository\ProductRepository;
use App\Form\CartConfirmationFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    protected $productRepository;
    protected $cartService;

    public function __construct(ProductRepository $productRepository, CartService $cartService)
    {
        $this->productRepository = $productRepository;
        $this->cartService = $cartService;
    }

    #[Route('/cart/add/{id}', name: 'cart_add', requirements: ["id" => '\d+'])]
    public function add($id, Request $request): Response
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException("Le produit $id n'existe pas");
        }
        $quantity = $request->query->getInt('quantity', 1); // Utilisation de 1 comme valeur par défaut

        if ($quantity < 1) {
            throw $this->createNotFoundException("Quantité invalide.");
        }
    
        $this->cartService->add($id, $quantity);

        $this->addFlash('success', "Le produit a bien été ajouté au panier");

        if ($request->query->get('returnToCart')) {
            return $this->redirectToRoute('cart_show');
        }

        return $this->redirectToRoute('product_show', [
            'category_slug' => $product->getCategory()->getSlug(),
            'slug' => $product->getSlug()
        ]);
    }

    #[Route('/cart', name: 'cart_show')]
    public function show(): Response
    {
        $form = $this->createForm(CartConfirmationFormType::class);
        $detailedCart = $this->cartService->getDetailedCartItems();
        $total = $this->cartService->getTotal();

        

        return $this->render('cart/index.html.twig', [
            'items' => $detailedCart,
            'total' => $total,

            'confirmationForm' => $form->createView()
        ]);
    }

    #[Route('/cart/delete/{id}', name: "cart_delete", requirements: ["id" => '\d+'])]
    public function delete($id): Response
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException("Le produit $id n'existe pas");
        }

        $this->cartService->remove($id);

        $this->addFlash('success', 'Le produit a bien été supprimé du panier');

        return $this->redirectToRoute('cart_show');
    }

    #[Route('/cart/decrement/{id}', name: 'cart_decrement', requirements: ["id" => '\d+'])]
    public function decrement($id): Response
    {
        $product = $this->productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException("Le produit $id n'existe pas");
        }

        $this->cartService->decrement($id);

        $this->addFlash('success', 'Le produit a bien été décrémenté');

        return $this->redirectToRoute('cart_show');
    }
}
