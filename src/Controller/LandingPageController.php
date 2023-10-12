<?php

namespace App\Controller;

use App\Entity\Adress;
use App\Entity\Client;
use App\Entity\Command;
use App\Entity\Country;
use App\Entity\Payment;
use App\Form\CommandType;
use App\Repository\CountryRepository;
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
    public function index(Request $request, ProductRepository $productRepository, CountryRepository $countryRepository, EntityManagerInterface $entityManager)
    {
        
       

        $command = new Command();
 
        $form = $this->createForm(CommandType::class, $command);

        $form->handleRequest($request);
     
        if ($form->isSubmitted()) {
          
            $payment = new Payment();
            $country = new Country();
            $country->setCountry("fronce");
 
            $adress = new Adress();
            $payment->setPaymentMethod("paypal");
            
           
             $command->setClient($command->getClient());
             $command->setAdressBilling($command->getAdressBilling());
             $command->getAdressBilling()->setCountry($country);

             $country->setCountry("belgiuquekjdsfnk");
             $command->setAdressDelivery($command->getAdressDelivery());
             $command->getAdressDelivery()->setCountry($country);
             //$command->setPayment($command->getPayment());
            $command->setStatus("validé");
            $command->setPayment($payment);
          
            $command = $form->getData();
            
            $entityManager->persist($command->getClient());
            $entityManager->persist($command->getAdressDelivery()->getCountry());
            $entityManager->persist($command->getAdressBilling()->getCountry());
            $entityManager->persist($command);
            $entityManager->flush();
           

            return $this->redirectToRoute('confirmation');
        }

        return $this->render('landing_page/confirmation.html.twig', [
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
