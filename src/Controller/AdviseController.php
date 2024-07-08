<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdviseController extends AbstractController
{
    #[Route('/advise', name: 'advise')]
    public function index(): Response
    {
        return $this->render('advise/index.html.twig', [
            'controller_name' => 'AdviseController',
        ]);
    }
}
