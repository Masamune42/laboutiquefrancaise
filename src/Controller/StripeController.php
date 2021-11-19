<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeController extends AbstractController
{

    public function __construct(string $api_key)
    {
        // On récupère la clé de l'API de Stripe
        $this->api_key = $api_key;
    }

    /**
     * @Route("/commande/create-session/{reference}", name="stripe_create_session")
     */
    public function index(EntityManagerInterface $entityManager, Cart $cart, $reference): Response
    {
        $product_for_stripe = [];
        $YOUR_DOMAIN = 'http://localhost:8000';

        // On récupère la commande par sa référence
        $order = $entityManager->getRepository(Order::class)->findOneByReference($reference);

        // Si la référence n'existe pas, on retourne à la page de la commande
        if(!$order) {
            return $this->redirectToRoute('order');
        }

        // On parcourt les détailes de la commandes afin de contruire le tableau à envoyer à Stripe
        foreach ($order->getOrderDetails()->getValues() as $product) {
            // On récupère les informations du produit (sert pour récupérer l'image)
            $product_object = $entityManager->getRepository(Product::class)->findOneByName($product->getProduct());
            $product_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product->getPrice(),
                    'product_data' => [
                        'name' => $product->getProduct(),
                        'images' => [$YOUR_DOMAIN . "/uploads/" . $product_object->getIllustration()],
                    ],
                ],
                'quantity' => $product->getQuantity(),
            ];
        }
        // On ajoute au tableau le cout de la livraison
        $product_for_stripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $order->getCarrierPrice(),
                'product_data' => [
                    'name' => $order->getCarrierName(),
                    'images' => [$YOUR_DOMAIN],
                ],
            ],
            'quantity' => 1,
        ];

        // On utilise clé de l'API
        Stripe::setApiKey($this->api_key);

        // On crée une session avec les informations
        $checkout_session = Session::create([
            'line_items' => [
                $product_for_stripe
            ],
            'payment_method_types' => [
                'card',
            ],
            'mode' => 'payment',
            // On redirige vers les liens suivants en cas de succès / échec avec l'ID de session de paiement
            'success_url' => $YOUR_DOMAIN . '/commande/merci/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/commande/erreur/{CHECKOUT_SESSION_ID}',
            // Auto remplissage de l'adresse mail pour la commande
            'customer_email' => $this->getUser()->getEmail()
        ]);

        // On transmet l'ID de la session de paiement en BDD
        $order->setStripeSessionId($checkout_session->id);
        $entityManager->flush();

        header("HTTP/1.1 303 See Other");
        header("Location: " . $checkout_session->url);
        exit;
    }
}
