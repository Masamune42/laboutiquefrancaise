<?php

namespace App\Controller;

use DateTime;
use App\Classe\Cart;
use App\Entity\Order;
use App\Form\OrderType;
use App\Entity\OrderDetails;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/commande", name="order")
     */
    public function index(Cart $cart, Request $request): Response
    {
        if(!$cart->getFull())
            return $this->redirectToRoute('products');
        // $this->getUser()->getAddresses() : on récupère les adresses liées au user sous forme de collection / relation
        // $this->getUser()->getAddresses()->getValues() : on récupère les datas de la relation
        if (!$this->getUser()->getAddresses()->getValues()) {
            return $this->redirectToRoute('account_address_add');
        }
        // 2e param null car non lié à un objet
        // 3e param : on récupère le user pour l'envoyer au form
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart->getFull()
        ]);
    }

    /**
     * @Route("/commande/recapitulatif", name="order_recap")
     */
    public function add(Cart $cart, Request $request): Response
    {
        if(!$cart->getFull())
            return $this->redirectToRoute('products');
        // 2e param null car non lié à un objet
        // 3e param : on récupère le user pour l'envoyer au form
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date = new DateTime();
            $date->setTimezone(new \DateTimeZone('Europe/Paris'));
            // On récupère le transporteur et l'adresse
            $carriers = $form->get('carriers')->getData();
            $delivery = $form->get('addresses')->getData();

            // On rempli les informations de livraison
            $delivery_content = $delivery->getFirstname() . ' ' . $delivery->getLastname();
            $delivery_content .= '<br>' . $delivery->getPhone();
            if ($delivery->getCompany())
                $delivery_content .= '<br>' . $delivery->getCompany();
            $delivery_content .= '<br>' . $delivery->getAddress();
            $delivery_content .= '<br>' . $delivery->getPostal() . ' ' . $delivery->getCity();
            $delivery_content .= '<br>' . $delivery->getCountry();

            // Enregistrer ma commande Order()
            // On crée un nouvel objet Order
            $order = new Order();

            // On crée une référence unique pour la commande
            $reference = $date->format('dmY').'-'.uniqid();
            $order->setReference($reference);
            // On assigne l'utilisateur actuel à la commande
            $order->setUser($this->getUser());
            $order->setCreatedAt($date);
            // On assigne le nom et le prix du transporteur
            $order->setCarrierName($carriers->getName());
            $order->setCarrierPrice($carriers->getPrice());
            $order->setDelivery($delivery_content);
            $order->setState(0);
            // On persiste la commande
            $this->entityManager->persist($order);

            // Pour chaque élément du panier, on persiste
            foreach ($cart->getFull() as $product) {
                $orderDetails = new OrderDetails();
                $orderDetails->setMyOrder($order);
                $orderDetails->setProduct($product['product']->getName());
                $orderDetails->setQuantity($product['quantity']);
                $orderDetails->setPrice($product['product']->getPrice());
                $orderDetails->setTotal($product['product']->getPrice() * $product['quantity']);
                $this->entityManager->persist($orderDetails);
            }

            // On envoie en BDD
            $this->entityManager->flush();

            return $this->render('order/add.html.twig', [
                'cart' => $cart->getFull(),
                'carrier' => $carriers,
                'delivery' => $delivery_content,
                'reference' => $order->getReference(),
            ]);
        }

        // Si on ne vient pas d'un formulaire soumis
        return $this->redirectToRoute('cart');
    }
}
