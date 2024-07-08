<?php

namespace App\Cart;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    private $requestStack;
    private $productRepository;

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

    protected function saveCart(array $cart): void
    {
        $session = $this->requestStack->getSession();
        $session->set('cart', $cart);
    }

    public function add(int $id): void
    {
        $cart = $this->getCart();
        $cart[$id] = ($cart[$id] ?? 0) + 1;
        $this->saveCart($cart);
    }

    public function decrement(int $id): void
    {
        $cart = $this->getCart();
        if (!array_key_exists($id, $cart)) {
            return;
        }

        if ($cart[$id] === 1) {
            $this->remove($id);
        } else {
            $cart[$id]--;
            $this->saveCart($cart);
        }
    }

    public function remove(int $id): void
    {
        $cart = $this->getCart();
        unset($cart[$id]);
        $this->saveCart($cart);
    }

    public function getTotal(): float
    {
        $total = 0;

        foreach ($this->getCart() as $id => $qty) {
            $product = $this->productRepository->find($id);
            if ($product) {
                $total += $product->getPrice() * $qty;
            }
        }

        return $total;
    }

    /**
     * @return CartItem[]
     */
    public function getDetailedCartItems(): array
    {
        $detailedCart = [];

        foreach ($this->getCart() as $id => $qty) {
            $product = $this->productRepository->find($id);
            if ($product) {
                $detailedCart[] = new CartItem($product, $qty);
            }
        }

        return $detailedCart;
    }

    public function empty(): void
    {
        $this->saveCart([]);
    }
}
