<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Data\SearchData;
use App\Form\SearchFormType;
use App\Form\CommentFormType;
use App\Repository\CommentRepository;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

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
    public function show(
        ProductRepository $productRepository,
        CommentRepository $commentRepository,
        Request $request,
        string $slug,
        string $category_slug,
        EntityManagerInterface $em
    ): Response {
        $product = $productRepository->findOneBy(['slug' => $slug]);
    
        if (!$product) {
            throw $this->createNotFoundException("La page demandée n'existe pas");
        }
    
        $comments = $commentRepository->findBy(['product' => $product], ['createdAt' => 'DESC']);
        $similarProducts = $productRepository->findSimilarProducts($product);

    // Calcul de la moyenne des avis
    $averageRating = count($comments) > 0
    ? array_sum(array_map(fn(Comment $comment) => $comment->getRating(), $comments)) / count($comments)
    : 0;



        // Créer un nouveau commentaire
        $comment = new Comment();
        $comment->setProduct($product); // Associer le commentaire au produit

        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);

        // Vérifier si le formulaire a été soumis et est valide
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // Ajoutez une vérification pour rating ici
                if (null === $comment->getRating()) {
                    $comment->setRating(0); // Valeur par défaut si rating est null
                }
                $comment->setCreatedAt(new \DateTimeImmutable());
                $comment->setIsValid(false); // Par défaut, les commentaires ne sont pas validés

                $em->persist($comment);
                $em->flush();

                $this->addFlash('success', 'Votre commentaire a été soumis et sera visible après validation.');
                return $this->redirectToRoute('product_show', [
                    'category_slug' => $product->getCategory()->getSlug(), // Add this
                    'slug' => $product->getSlug(),
                ]);
            } else {
                $this->addFlash('error', 'Il y a eu un problème avec votre commentaire. Veuillez réessayer.');
            }
        }
    
        return $this->render('product/show.html.twig', [
            'product' => $product,
            'similarProducts' => $similarProducts,
            'category_slug' => $category_slug,
            'comments' => $comments,
            'commentForm' => $form->createView(),
            'averageRating' => $averageRating,
        ]);
    }

    #[Route('/produits', name: 'product_display')]
    public function display(ProductRepository $productRepository, Request $request): Response
    {
        $data = new SearchData();
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
            'form' => $form->createView(),
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'totalItems' => $totalItems,
        ]);
    }
}

