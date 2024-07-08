<?php

namespace App\Controller;

use App\Repository\HeadersRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(ProductRepository $productRepository,  HeadersRepository $headersRepository): Response
    {
        $products = $productRepository->findBy([], [], 3);
        $headers = $headersRepository->findAll();

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'carousel' => true,  //Le caroussel ne s'affiche que sur la page d'accueil (voir base.twig)
            'top_products' => $products,
            'headers' => $headers
          
        ]);
    }
}