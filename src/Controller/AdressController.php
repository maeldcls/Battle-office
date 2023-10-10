<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdressController extends AbstractController
{
    #[Route('/adress', name: 'app_adress')]
    public function index(): Response
    {
        return $this->render('adress/index.html.twig', [
            'controller_name' => 'AdressController',
        ]);
    }
}
