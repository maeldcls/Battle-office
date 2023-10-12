<?php

namespace App\Controller;

use App\Entity\Adress;
use App\Entity\Client;
use App\Entity\Command;
use App\Entity\Country;
use App\Entity\Payment;
use App\Form\CommandType;
use App\Repository\CountryRepository;
use App\Repository\PaymentRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    #[Route('/',name:'landing_page', methods: ['GET', 'POST'])]
    public function index(Request $request, ProductRepository $productRepository, CountryRepository $countryRepository,PaymentRepository $paymentRepository, EntityManagerInterface $entityManager)
    {
  
        $command = new Command();
        $payment = new Payment();
        
 
        $form = $this->createForm(CommandType::class, $command);

        $form->handleRequest($request);
     
        if ($form->isSubmitted()) {
           
            $selectedProductID = $request->request->get('selected_product_id');
            $product = $productRepository->find($selectedProductID);
           
           $method = $request->request->get('paiement');
           $payment = $paymentRepository->findOneBy(['paymentMethod' => $method]);
           
           $countryDId = $request->request->get('countryDelivery');
           $countryD = $countryRepository->find($countryDId);
           
           $countryBId = $request->request->get('countryBilling');
           $countryB = $countryRepository->find($countryBId);
          
            $command = $form->getData();
                  
            $command->getAdressDelivery()->setCountry($countryD);
            $command->getAdressBilling()->setCountry($countryB);
            $command->addProduct($product);
            $command->setStatus("validé");
            $command->setPayment($payment);
     

            $entityManager->persist($command->getClient());
            $entityManager->persist($command->getAdressDelivery()->getCountry());
            $entityManager->persist($command->getAdressBilling()->getCountry());
            $entityManager->persist($command);
            $entityManager->flush();
           

            return $this->redirectToRoute('confirmation');
        }

        return $this->render('landing_page/index_new.html.twig', [
            'form' => $form,
            'products'=>$productRepository->findAll(),
            'countries'=>$countryRepository->findAll(),
            'command'=>$command
        ]);
    }
    /**
     * @Route("/confirmation", name="confirmation")
     */
    #[Route('/confirmation',name:'confirmation')]
    public function confirmation()
    {
       
        return $this->render('landing_page/confirmation.html.twig', [

        ]);
    }
}
