<?php

namespace App\Cart;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    // public function __construct(
    //     private RequestStack $requestStack,
    // ) {
    // }
    protected $requestStack;
    protected $productRepository;

    public function __construct(RequestStack $requestStack, ProductRepository $productRepository)
    {
        $this->requestStack = $requestStack;
        $this->productRepository = $productRepository;
    }

    protected function getCart(): array
    {
        $session = $this->requestStack->getSession();
        return $session->get('cart', []);
    }

    protected function saveCart(array $cart)
    {
        $session = $this->requestStack->getSession();
        $session->set('cart', $cart);
    }

    public function add(int $id)
    {
        // 1. Retrouver le panier dans la session (sous forme de tableau)
        // 2. S'il n'existe pas encore, alors prendre un tableau vide
        $cart = $this->getCart();
        // 3. Voir si le produit {id} existe déjà dans le tableau
        // 4. Si c'est le cas, simplement augmenter la quantité
        // 5. Sinon, ajouter le produit avec la quantité 1
        // if (array_key_exists($id, $cart)) {
        //     $cart[$id]++;
        // } else {
        //     $cart[$id] = 1;
        // }

        //Refactoring
        if (!array_key_exists($id, $cart)) {
            $cart[$id] = 0;
        }

        $cart[$id]++;

        // 6. Enregistrer le tableau mis à jour dans la session
        $this->saveCart($cart);
    }

    public function decrement(int $id)
    {
        $cart = $this->getCart();
        if (!array_key_exists($id, $cart)) {
            return;
        }
        // Soit le produit = 1, alors il faut le supprimer
        if ($cart[$id] === 1) {
            $this->remove($id);
        } else {
            // Soit le produit est > 1, alors il faut décrémenter
            $cart[$id]--;
            $this->saveCart($cart);
        }
    }

    public function remove(int $id)
    {
        $cart = $this->getCart();
        unset($cart[$id]);
        $this->saveCart($cart);
    }

    public function getTotal(): int
    {
        $total = 0;

        foreach ($this->getCart() as $id => $qty) {
            $product = $this->productRepository->find($id);

            if (!$product) {
                continue;
            }

            $total += $product->getPrice() * $qty;
        }

        return $total;
    }

    /**
     * @return CartItem[]
     */
    public function getDetailedCartItems(): array
    {
        $detailedCart = [];
        // [12 => ['product' => '...'], 'quantity' => qté]

        foreach ($this->getCart() as $id => $qty) {
            $product = $this->productRepository->find($id);

            if (!$product) {
                continue;
            }

            $detailedCart[] = new CartItem($product, $qty);
            // $detailedCart[] = [
            //     'product' => $product,
            //     'qty' => $qty
            // ];
        }

        return $detailedCart;
    }

    public function empty()
    {
        $this->saveCart([]);
    }
}
