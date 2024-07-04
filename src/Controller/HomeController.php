<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findBy([], [], 3);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'products' => $products
        ]);
    }
}