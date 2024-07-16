<?php

namespace App\Controller;

use App\Repository\AdviseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdviseController extends AbstractController
{
    #[Route('/advise', name: 'advise')]
    public function index(AdviseRepository $adviseRepository): Response
    {
        $advise = $adviseRepository->findBy([], [], 3);

        return $this->render('advise/index.html.twig', [
            'controller_name' => 'AdviseController',
            'advise' => $advise,
        ]);
    }

    #[Route('/blog/{slug}', name: 'advise_show')]
    public function show($slug, AdviseRepository $adviseRepository): Response
    {
        $advise = $adviseRepository->findOneBy(['slug' => $slug]);

        if (!$advise) {
            throw $this->createNotFoundException('The advise does not exist');
        }

        return $this->render('advise/show.html.twig', [
            'advise' => $advise,
        ]);
    }
}
