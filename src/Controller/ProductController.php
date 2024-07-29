<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Form\SearchFormType;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    #[Route('/{slug}', name: 'product_category', priority: -1)]
    public function category($slug, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy(['slug' => $slug]);

        if (!$category) {
            throw $this->createNotFoundException("La catégorie demandée n'existe pas");
        }

        return $this->render('product/category.html.twig', [
            'slug' => $slug,
            'category' => $category,
        ]);
    }

    #[Route('/{category_slug}/{slug}', name: 'product_show', priority: -1)]
    public function show($category_slug, $slug, ProductRepository $productRepository): Response
    {
        $product = $productRepository->findOneBy(['slug' => $slug]);

        if (!$product) {
            throw $this->createNotFoundException("La page demandée n'existe pas");
        }

        $similarProducts = $productRepository->findSimilarProducts($product);

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'similarProducts' => $similarProducts,
            'category_slug' => $category_slug,
        ]);
    }

    #[Route('/produits', name: 'product_display')]
    public function display(ProductRepository $productRepository, Request $request): Response
    {
        $data = new SearchData;
        $data->page = $request->get('page', 1);

        $form = $this->createForm(SearchFormType::class, $data);
        $form->handleRequest($request);

        [$minPrice, $maxPrice] = $productRepository->findMinMaxPrice($data);
        $products = $productRepository->findSearch($data);
        $totalItems = $productRepository->countItems($data);

        if ($request->get('ajax')) {
            return new JsonResponse([
                'content' => $this->renderView('product/_products.html.twig', ['products' => $products]),
                'sorting' => $this->renderView('product/_sorting.html.twig', ['products' => $products]),
                'pagination' => $this->renderView('product/_pagination.html.twig', ['products' => $products]),
                'minPrice' => $minPrice,
                'maxPrice' => $maxPrice,
            ]);
        }

        return $this->render('product/display.html.twig', [
            'products' => $products,
            'form' => $form,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'totalItems' => $totalItems,
        ]);
    }
}
