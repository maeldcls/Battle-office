<?php

namespace App\Controller;

use GuzzleHttp\Client as GuzzleClient;
use App\Entity\Adress;
use App\Entity\Client;
use App\Entity\Command;
use App\Entity\Country;
use App\Entity\Payment;
use App\Entity\Product;
use App\Form\CommandType;
use App\Repository\CountryRepository;
use App\Repository\PaymentRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LandingPageController extends AbstractController
{
    /**
     * @Route("/", name="landing_page")
     * @throws \Exception
     */
    #[Route('/', name: 'landing_page', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        ProductRepository $productRepository,
        CountryRepository $countryRepository,
        MailerInterface $mailer,
        PaymentRepository $paymentRepository,
        EntityManagerInterface $entityManager
    ) {

        $command = new Command();
        $payment = new Payment();

        $form = $this->createForm(CommandType::class, $command);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //récupère l'id produit choisis par le user pour cherche ce produit dans la BDD
            $selectedProductID = $request->request->get('selected_product_id');
            $product = $productRepository->find($selectedProductID);

            //récupère la méthode de paiement choisie par le user puis récupère ce payment method dans la BDD
            $method = $request->request->get('paiement');
            $payment = $paymentRepository->findOneBy(['paymentMethod' => $method]);

            //récupère le pays choisi par le user puis récupère ce pays dans la BDD
            //    $countryBId = $request->request->get('countryBilling');
            //    $countryB = $countryRepository->find($countryBId);

            $command = $form->getData();

            //Si l'adresse delivery est null alors l'adresse delivery est la même que l'adresse facturation
            if (
                $command->getAdressDelivery()->getFirstName() == null || $command->getAdressDelivery()->getLastName() ||
                $command->getAdressDelivery()->getAdress() || $command->getAdressDelivery()->getCity() || $command->getAdressDelivery()->getZipCode()
            ) {

                $command->setAdressDelivery($command->getAdressBilling());
                //    $command->getAdressDelivery()->setCountry($countryB);
                //    $command->getAdressBilling()->setCountry($countryB);

            } else {
                $countryDId = $request->request->get('countryDelivery');
                $countryD = $countryRepository->find($countryDId);

                // $command->getAdressDelivery()->setCountry($countryD);
                // $command->getAdressBilling()->setCountry($countryB);
            }


            $command->addProduct($product);
            $command->setStatus("WAITING");
            $command->setPayment($payment);


            $entityManager->persist($command);
            $entityManager->flush();




            // fabrication json
            $client = [
                'firstname' => $command->getAdressDelivery()->getFirstName(),
                'lastname' => $command->getAdressDelivery()->getLastName(),
                'email' => $command->getClient()->getMail()
            ];

            $billing = [
                'address_line1' => $command->getAdressBilling()->getAdress(),
                'address_line2' => $command->getAdressBilling()->getComplementaryAdress(),
                'city' => $command->getAdressBilling()->getCity(),
                'zipcode' => strval($command->getAdressBilling()->getZipCode()),
                'country' => $command->getAdressBilling()->getCountry()->getCountry(),
                'phone' => $command->getAdressBilling()->getPhone(),
            ];
            $shipping = [
                'address_line1' => $command->getAdressDelivery()->getAdress(),
                'address_line2' => $command->getAdressDelivery()->getComplementaryAdress(),
                'city' => $command->getAdressDelivery()->getCity(),
                'zipcode' => strval($command->getAdressDelivery()->getZipCode()),
                'country' => $command->getAdressDelivery()->getCountry()->getCountry(),
                'phone' => $command->getAdressDelivery()->getPhone(),
            ];
            $addresses = [
                'billing' => $billing,
                'shipping' => $shipping
            ];

            $json = [
                'id' => strval($command->getId()),
                'product' => $product->getProductName(),
                'payment_method' => $payment->getPaymentMethod(),
                'status' => $command->getStatus(),
                'client' => $client,
                'addresses' => $addresses

            ];
             $order = ['order'=>$json];
            $jsonString = json_encode($order);
    
            $token = 'mJxTXVXMfRzLg6ZdhUhM4F6Eutcm1ZiPk4fNmvBMxyNR4ciRsc8v0hOmlzA0vTaX';

            $client = new GuzzleClient([
                'base_uri' => 'https://api-commerce.simplon-roanne.com/',
                'verify' => false
            ]);

             $response = $client->post('/order', [
                 'headers' => [
                     'Authorization' => 'Bearer ' . $token, // Add the Bearer token to the request headers
                     'Content-Type' => 'application/json', // Set the Content-Type header
                 ],
                 'body' => $jsonString, // Set the JSON data as the request body
             ]);

            //Check the response
            if ($response->getStatusCode() === 200) {
                // Request was successful
                $responseData = json_decode($response->getBody(), true); // If the API returns JSON response
                $orderId = $responseData["order_id"];
                
            } else {
                // Request failed
                echo 'API Request Failed: ' . $response->getStatusCode();
                // You can handle error cases here
            }
             $response = $client->get('https://api-commerce.simplon-roanne.com/order/25/status');
             $data = json_decode($response->getBody()->getContents(), true);
          
         




\Stripe\Stripe::setApiKey('sk_test_51O0mBCLp19bydb83PUkHWqWW7mWJcg11Gf1qDKiNMITxJNk9pZnD3AZcNyTwbLCAWAHCMG7CK7jrBnFO1u7vzOVG00uc0s569s');
$successUrl = $this->generateUrl('confirmation', [], UrlGeneratorInterface::ABSOLUTE_URL);


$session = \Stripe\Checkout\Session::create([
    'payment_method_types' => ['card'],
    'line_items' => [[
        'price_data' => [
            'currency' => 'usd',
            'product_data' => [
                'name' => $product->getProductName(), 
            ],
            'unit_amount' => $product->getReducPrice() * 100,
        ],
        'quantity' => 1, 
    ]],
    'mode' => 'payment',
    'success_url' => $successUrl, 
    'cancel_url' => 'https://www.google.com/', 
]);
    return $this->redirect($session['url']);
//return $this->redirectToRoute('app_product_show', ['id' => $product->getId()]);
        

return $this->redirectToRoute('confirmation');
}  
        return $this->render('landing_page/index_new.html.twig', [
            'form' => $form,
            'products' => $productRepository->findAll(),
            'command' => $command,

        ]);
    
}
    /**
     * @Route("/confirmation", name="confirmation")
     */
    #[Route('/confirmation', name: 'confirmation')]
    public function confirmation(MailerInterface $mailer)
    {

        $email = (new Email())
        ->from("battle@office.com")
        ->to("aaaa@aaaa.com")
        ->subject('Confirmation de commande')
        ->html('
        <h2>Merci pour votre achat !'.' </h2>
        <p>Détails de votre commande</p>
        <p>Mail :'.'zfhskjhflkjh' .'</p>');

         $mailer->send($email);

        return $this->render('landing_page/confirmation.html.twig', [
            
        ]);
    }
}
