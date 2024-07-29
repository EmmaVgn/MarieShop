<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Product;
use App\Form\CommentFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CommentController extends AbstractController
{
    #[Route('/produit/{id}/commentaire', name: 'product_comment', methods: ['GET', 'POST'])]
    public function comment(Request $request, Product $product, EntityManagerInterface $em): Response
    {
        // Assurez-vous que l'utilisateur est connecté
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
    
        // Créer un nouveau commentaire
        $comment = new Comment();
        $comment->setUser($user); // Lier l'utilisateur connecté au commentaire
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
                return $this->redirectToRoute('product_show', ['slug' => $product->getSlug()]);
            } else {
                $this->addFlash('error', 'Il y a eu un problème avec votre commentaire. Veuillez réessayer.');
            }
        }
    
        return $this->render('comment/comment.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
        ]);
    }
}    
