<?php

namespace App\Controller;

use App\Entity\Client;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LandingPageController extends AbstractController
{
    /**
     * @Route("/", name="landing_page")
     * @throws \Exception
     */
    #[Route('/',name:'landing_page')]
    public function index(Request $request, ProductRepository $productRepository)
    {
        $products = $productRepository->findAll();
        dd($products);
      
        return $this->render('landing_page/index_new.html.twig', [

        ]);
    }
    /**
     * @Route("/confirmation", name="confirmation")
     */
    #[Route('/confirmation',name:'confirmation')]
    public function confirmation()
    {
        dd('confirmation');
        return $this->render('landing_page/confirmation.html.twig', [

        ]);
    }
}
